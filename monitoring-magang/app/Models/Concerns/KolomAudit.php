<?php

namespace App\Models\Concerns;

use App\Support\Peran;

/**
 * Mengisi kolom audit wajib setiap tabel PDLK:
 *   submitted_at, created_by  (created_at / updated_at diisi Laravel & trigger)
 */
trait KolomAudit
{
    public static function bootKolomAudit(): void
    {
        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = Peran::userId();
            }

            if (empty($model->submitted_at)) {
                $model->submitted_at = now();
            }
        });
    }

    public function initializeKolomAudit(): void
    {
        $this->casts['submitted_at'] = 'datetime';
    }

    public function pembuat()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
