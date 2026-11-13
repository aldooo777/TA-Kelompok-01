<?php

namespace App\Models;

use App\Models\Concerns\KolomAudit;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatusPeserta extends Model
{
    use KolomAudit;

    protected $table = 'riwayat_status_peserta';

    protected $fillable = [
        'peserta_pdlk_id', 'status_lama', 'status_baru', 'alasan', 'created_by',
    ];
}
