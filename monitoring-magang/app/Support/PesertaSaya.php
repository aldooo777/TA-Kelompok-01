<?php

namespace App\Support;

/** Data PDLK milik mahasiswa yang sedang login. */
class PesertaSaya
{
    /** Peserta aktif (atau selesai bila $termasukSelesai) terbaru milik NIM ini. */
    public static function untukNim(?string $nim, bool $termasukSelesai = false): ?object
    {
        if (!$nim) {
            return null;
        }

        $status = $termasukSelesai ? "pp.status IN ('active', 'completed')" : "pp.status = 'active'";

        return Sql::one(
            Query::PESERTA . " WHERE pp.nim = ? AND pp.is_active AND $status
             ORDER BY CASE pp.status WHEN 'active' THEN 0 ELSE 1 END, pp.created_at DESC LIMIT 1",
            [$nim]
        );
    }

    /**
     * Jadwal monev yang sudah dibuka untuk periode peserta beserta status
     * pengisiannya. Kolom tambahan: response_id, response_status, submitted_at.
     */
    public static function jadwalMonev(object $peserta): \Illuminate\Support\Collection
    {
        if (!$peserta->period_id) {
            return collect();
        }

        return Sql::all(
            "SELECT s.*, q.type, q.title, q.description,
                    r.id AS response_id, r.status AS response_status, r.submitted_at
             FROM monev_schedules s
             JOIN monev_questionnaires q ON q.id = s.questionnaire_id
             LEFT JOIN monev_responses r ON r.schedule_id = s.id AND r.participant_id = ?
             WHERE s.period_id = ? AND s.is_active AND q.is_active
             ORDER BY CASE q.type WHEN 'mid' THEN 0 ELSE 1 END, s.open_at",
            [$peserta->id, $peserta->period_id]
        );
    }

    /** Jadwal yang wajib diisi sekarang: sudah dibuka, belum ditutup, belum dikirim. */
    public static function monevWajib(object $peserta): ?object
    {
        return self::jadwalMonev($peserta)->first(fn ($s) =>
            $s->open_at <= now()->toDateTimeString() && !$s->is_closed && $s->response_status !== 'submitted');
    }
}
