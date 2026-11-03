<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'nama',
        'jenis_pdlk',
        'tempat_magang',
        'status',
    ];
}
