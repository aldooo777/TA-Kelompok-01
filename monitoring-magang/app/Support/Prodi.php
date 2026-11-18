<?php

namespace App\Support;

class Prodi
{
    /**
     * Cocokkan teks prodi dari CIS (mis. "D3 Teknologi Informasi",
     * "DIII Teknologi Informasi", "Teknologi Informasi", "D3TI").
     * Mengembalikan id study_programs atau null.
     */
    public static function dariTeksCis(?string $teks): ?int
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

        foreach (Sql::all('SELECT id, code, name FROM study_programs') as $p) {
            if ($kode === $p->code || ($cari !== '' && $norm($p->name) === $cari)) {
                return (int) $p->id;
            }
        }

        return null;
    }

    /** Label "D3 Teknologi Informasi". */
    public static function label(?object $p): string
    {
        return $p ? trim(($p->level ?? '') . ' ' . ($p->name ?? '')) : '-';
    }
}
