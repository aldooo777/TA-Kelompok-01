<?php

namespace App\Services;

use App\Support\Label;
use App\Support\Peran;
use App\Support\Sql;

/** Logika pendaftaran -> mahasiswa aktif PDLK (query SQL langsung). */
class ParticipantService
{
    /**
     * Mengubah status pendaftaran. Bila menjadi "passed" (Lulus), mahasiswa
     * otomatis dicatat sebagai mahasiswa aktif PDLK.
     *
     * @return array{ok: bool, pesan: string}
     */
    public function changeApplicationStatus(int $applicationId, string $status, ?string $notes = null): array
    {
        $app = Sql::oneOrFail('SELECT * FROM applications WHERE id = ?', [$applicationId]);

        $sudahPeserta = (bool) Sql::value('SELECT EXISTS (SELECT 1 FROM pdlk_participants WHERE application_id = ?)', [$app->id]);

        if ($app->status === 'passed' && $status !== 'passed' && $sudahPeserta) {
            return [
                'ok' => false,
                'pesan' => "Pendaftaran {$app->name} sudah lulus dan tercatat sebagai mahasiswa aktif. "
                    . 'Tidak ada pembatalan; untuk kondisi force majeure ubah statusnya menjadi Tidak Aktif di menu Mahasiswa Aktif.',
            ];
        }

        Sql::transaction(function () use ($app, $status, $notes) {
            Sql::update('applications', $app->id, [
                'status' => $status,
                'notes' => $notes ?? $app->notes,
                'processed_by' => Peran::userId(),
                'processed_at' => now(),
            ]);

            if ($status === 'passed') {
                $this->createFromApplication($app->id);
            }
        });

        return ['ok' => true, 'pesan' => "Status {$app->name} menjadi " . Label::APPLICATION_STATUS[$status] . '.'];
    }

    public function createFromApplication(int $applicationId): int
    {
        $ada = Sql::value('SELECT id FROM pdlk_participants WHERE application_id = ?', [$applicationId]);

        if ($ada) {
            return (int) $ada;
        }

        $d = Sql::one(
            'SELECT ap.*, v.period_id, v.activity_type_id, v.partner_id, v.position AS v_position, a.title
             FROM applications ap
             JOIN vacancy_details v ON v.id = ap.vacancy_id
             JOIN announcements a ON a.id = v.announcement_id
             WHERE ap.id = ?',
            [$applicationId]
        );

        $id = Sql::insert('pdlk_participants', [
            'user_id' => $d->user_id ?? Sql::value('SELECT id FROM users WHERE nim = ? LIMIT 1', [$d->nim]),
            'nim' => $d->nim,
            'name' => $d->name,
            'study_program_id' => $d->study_program_id,
            'period_id' => $d->period_id,
            'activity_type_id' => $d->activity_type_id,
            'partner_id' => $d->partner_id,
            'application_id' => $d->id,
            'source' => 'application',
            'position' => $d->v_position,
            'status' => 'active',
        ]);

        $this->catatRiwayat($id, null, 'active', 'Lulus seleksi: ' . $d->title);

        return $id;
    }

    /** Ubah status peserta sekaligus mencatat riwayat. */
    public function changeParticipantStatus(int $participantId, string $newStatus, ?string $reason = null): void
    {
        $p = Sql::oneOrFail('SELECT id, status FROM pdlk_participants WHERE id = ?', [$participantId]);

        if ($p->status === $newStatus) {
            return;
        }

        Sql::transaction(function () use ($p, $newStatus, $reason) {
            Sql::update('pdlk_participants', $p->id, ['status' => $newStatus]
                + ($newStatus === 'inactive' ? ['inactive_reason' => $reason] : []));
            $this->catatRiwayat($p->id, $p->status, $newStatus, $reason);
        });
    }

    public function catatRiwayat(int $participantId, ?string $old, string $new, ?string $reason): void
    {
        Sql::insert('participant_status_histories', [
            'participant_id' => $participantId,
            'old_status' => $old,
            'new_status' => $new,
            'reason' => $reason,
        ]);
    }
}
