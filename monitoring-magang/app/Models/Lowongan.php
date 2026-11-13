<?php

namespace App\Models;

use App\Models\Concerns\KolomAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Lowongan extends Model
{
    use KolomAudit;

    protected $table = 'lowongan';

    protected $fillable = [
        'judul', 'slug', 'mitra_id', 'jenis_kegiatan_id', 'posisi', 'lokasi',
        'deskripsi', 'kriteria', 'kuota', 'tanggal_buka', 'tanggal_tutup',
        'poster_path', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $l) {
            if (!$l->slug) {
                $dasar = Str::slug($l->judul) ?: 'lowongan';
                $slug = $dasar;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $dasar . '-' . $i++;
                }
                $l->slug = $slug;
            }
        });
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'jenis_kegiatan_id');
    }

    public function fieldTambahan()
    {
        return $this->hasMany(LowonganFieldTambahan::class)->orderBy('urutan');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function scopeTerbit(Builder $q): Builder
    {
        return $q->where('is_published', true);
    }

    /** Lowongan masih menerima pendaftaran. */
    public function getDibukaAttribute(): bool
    {
        $hariIni = now()->startOfDay();

        return $this->is_published
            && (!$this->tanggal_buka || $this->tanggal_buka->lte($hariIni))
            && (!$this->tanggal_tutup || $this->tanggal_tutup->gte($hariIni));
    }

    public function getPosterUrlAttribute(): ?string
    {
        return $this->poster_path ? Storage::disk('public')->url($this->poster_path) : null;
    }
}
