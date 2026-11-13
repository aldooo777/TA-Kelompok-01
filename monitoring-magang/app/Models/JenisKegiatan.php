<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    protected $table = 'jenis_kegiatan_pdlk';

    protected $fillable = ['slug', 'nama', 'deskripsi', 'urutan'];

    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'jenis_kegiatan_id');
    }
}
