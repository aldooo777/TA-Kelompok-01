<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Lampiran pengumuman (announcement_attachments). */
class Lampiran
{
    public const RULES = [
        'lampiran' => ['nullable', 'array', 'max:10'],
        'lampiran.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png', 'max:10240'],
        'hapus_lampiran' => ['nullable', 'array'],
    ];

    public static function simpan(Request $request, int $announcementId): void
    {
        foreach ($request->file('lampiran', []) as $file) {
            Sql::insert('announcement_attachments', [
                'announcement_id' => $announcementId,
                'file_path' => $file->store("lampiran/$announcementId", 'public'),
                'file_name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'file_size' => $file->getSize(),
            ]);
        }

        foreach ((array) $request->input('hapus_lampiran', []) as $id) {
            $l = Sql::one('SELECT * FROM announcement_attachments WHERE id = ? AND announcement_id = ?', [$id, $announcementId]);
            if ($l) {
                Storage::disk('public')->delete($l->file_path);
                Sql::delete('announcement_attachments', $l->id);
            }
        }
    }

    public static function daftar(int $announcementId): \Illuminate\Support\Collection
    {
        return Sql::all('SELECT * FROM announcement_attachments WHERE announcement_id = ? ORDER BY id', [$announcementId]);
    }
}
