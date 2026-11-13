<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';

    protected $fillable = ['kode', 'nama', 'jenjang'];

    /**
     * Cocokkan teks prodi dari CIS (mis. "D3 Teknologi Informasi",
     * "DIII Teknologi Informasi", "Teknologi Informasi", "D3TI") ke master prodi.
     */
    public static function dariTeksCis(?string $teks): ?self
    {
        $norm = fn ($t) => trim(preg_replace('/\s+/', ' ', preg_replace(
            '/\b(program studi|prodi|diploma|sarjana|d-?iii|d-?iv|d3|d4|s1|s-1)\b/i', ' ',
            mb_strtolower((string) $t)
        )));

        $cari = $norm($teks);
        $kode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $teks));

        if ($cari === '' && $kode === '') {
            return null;
        }

        foreach (static::all() as $prodi) {
            if ($kode === $prodi->kode || ($cari !== '' && $norm($prodi->nama) === $cari)) {
                return $prodi;
            }
        }

        return null;
    }

    public function getLabelAttribute(): string
    {
        return trim(($this->jenjang ? $this->jenjang . ' ' : '') . $this->nama);
    }
}
