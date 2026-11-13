<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mahasiswa AKTIF PDLK (tabel peserta_pdlk, sebelumnya registrations).
 */
class PesertaPdlk extends Model
{
    protected $table = 'peserta_pdlk';

    public const AKTIF = 'aktif';
    public const TIDAK_AKTIF = 'tidak_aktif';
    public const SELESAI = 'selesai';

    public const STATUS = [
        self::AKTIF => 'Aktif',
        self::TIDAK_AKTIF => 'Tidak Aktif',
        self::SELESAI => 'Selesai',
    ];

    public const STATUS_PELAKSANAAN = [
        'onboarding' => 'Onboarding',
        'berjalan' => 'Sedang Berjalan',
        'selesai_kegiatan' => 'Kegiatan Selesai',
    ];

    public const MODE_PELAKSANAAN = [
        'onsite' => 'Onsite',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
    ];

    protected $fillable = [
        'nim', 'nama', 'status', 'pendaftaran_id', 'sumber', 'user_id',
        'program_studi_id', 'jenis_kegiatan_id', 'mitra_id', 'posisi',
        'tanggal_mulai', 'tanggal_selesai', 'status_pelaksanaan',
        'mode_pelaksanaan', 'lokasi', 'alasan_tidak_aktif', 'diinput_oleh',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'jenis_kegiatan_id');
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusPeserta::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Ubah status sekaligus mencatat riwayatnya.
     */
    public function ubahStatus(string $statusBaru, ?string $alasan = null, ?int $olehUserId = null): void
    {
        $statusLama = $this->status;

        if ($statusLama === $statusBaru) {
            return;
        }

        $this->status = $statusBaru;

        if ($statusBaru === self::TIDAK_AKTIF) {
            $this->alasan_tidak_aktif = $alasan;
        }

        $this->save();

        $this->riwayatStatus()->create([
            'status_lama' => $statusLama,
            'status_baru' => $statusBaru,
            'alasan' => $alasan,
            'diubah_oleh' => $olehUserId,
        ]);
    }
}
