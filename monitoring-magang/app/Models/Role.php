<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const MAHASISWA = 'mahasiswa';
    public const STAF_UPT = 'staf_upt';
    public const KOORDINATOR_PDLK_PRODI = 'koordinator_pdlk_prodi';
    public const KOORDINATOR_PRODI = 'koordinator_prodi';

    protected $fillable = ['kode', 'nama'];
}
