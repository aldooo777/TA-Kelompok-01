<?php

namespace App\Models;

use App\Models\Concerns\KolomAudit;
use Illuminate\Database\Eloquent\Model;

class LowonganFieldTambahan extends Model
{
    use KolomAudit;

    protected $table = 'lowongan_field_tambahan';

    public const TIPE = [
        'text' => 'Teks singkat',
        'textarea' => 'Teks panjang',
        'number' => 'Angka',
        'url' => 'Tautan (URL)',
    ];

    protected $fillable = ['lowongan_id', 'label', 'tipe', 'urutan'];
}
