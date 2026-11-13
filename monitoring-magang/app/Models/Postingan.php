<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Postingan extends Model
{
    protected $table = 'postingan';

    public const JENIS = [
        'berita' => 'Berita',
        'pengumuman' => 'Pengumuman',
        'kegiatan' => 'Kegiatan',
    ];

    protected $fillable = [
        'jenis', 'judul', 'slug', 'narasi', 'foto_path',
        'is_published', 'published_at', 'dibuat_oleh',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $p) {
            $p->slug = $p->slug ?: static::slugUnik($p->judul);
            $p->published_at = $p->published_at ?: now();
        });
    }

    public static function slugUnik(string $judul): string
    {
        $dasar = Str::slug($judul) ?: 'postingan';
        $slug = $dasar;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }

    public function scopeTerbit(Builder $q): Builder
    {
        return $q->where('is_published', true)->latest('published_at');
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto_path ? Storage::disk('public')->url($this->foto_path) : null;
    }

    public function getJenisLabelAttribute(): string
    {
        return self::JENIS[$this->jenis] ?? ucfirst($this->jenis);
    }

    public function getRingkasanAttribute(): string
    {
        return Str::limit(strip_tags($this->narasi), 160);
    }
}
