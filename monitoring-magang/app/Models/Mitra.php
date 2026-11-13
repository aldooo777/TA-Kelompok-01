<?php

namespace App\Models;

use App\Models\Concerns\KolomAudit;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use KolomAudit;

    protected $table = 'mitra';

    protected $fillable = ['nama', 'bidang', 'alamat', 'website'];

    /**
     * Staf UPT cukup mengetik nama mitra; data mitra dibuat otomatis
     * bila belum ada (tidak perlu mengelola master mitra terpisah).
     */
    public static function dariNama(string $nama): self
    {
        $nama = trim(preg_replace('/\s+/', ' ', $nama));

        return static::query()
            ->whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)])
            ->first()
            ?? static::create(['nama' => $nama]);
    }
}
