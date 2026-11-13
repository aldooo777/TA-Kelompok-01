<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatusPeserta extends Model
{
    protected $table = 'riwayat_status_peserta';

    protected $fillable = [
        'peserta_pdlk_id', 'status_lama', 'status_baru', 'alasan', 'diubah_oleh',
    ];
}
