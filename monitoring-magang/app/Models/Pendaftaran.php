<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    public const TERDAFTAR = 'terdaftar';
    public const PROSES = 'proses';
    public const LULUS = 'lulus';
    public const TIDAK_LULUS = 'tidak_lulus';

    public const STATUS = [
        self::TERDAFTAR => 'Terdaftar',
        self::PROSES => 'Proses',
        self::LULUS => 'Lulus',
        self::TIDAK_LULUS => 'Tidak Lulus',
    ];

    protected $fillable = [
        'lowongan_id', 'user_id', 'nim', 'nama', 'program_studi_id', 'semester',
        'ipk', 'no_hp', 'email', 'dokumen_path', 'dokumen_nama_asli',
        'jawaban_tambahan', 'status', 'catatan', 'diproses_oleh', 'diproses_at',
    ];

    protected $casts = [
        'jawaban_tambahan' => 'array',
        'ipk' => 'decimal:2',
        'diproses_at' => 'datetime',
    ];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function pesertaPdlk()
    {
        return $this->hasOne(PesertaPdlk::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst($this->status);
    }

    public function getSudahFinalAttribute(): bool
    {
        return in_array($this->status, [self::LULUS, self::TIDAK_LULUS], true);
    }
}
