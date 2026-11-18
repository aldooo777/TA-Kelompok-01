{{-- Unggah / hapus lampiran pengumuman. $lampiran: koleksi announcement_attachments --}}
<div class="p-field p-span-2">
    <label for="lampiran">Lampiran <span class="p-optional">(opsional, PDF/Office/gambar, maks. 10 MB per file)</span></label>
    <input type="file" id="lampiran" name="lampiran[]" class="p-input" multiple>
    @if ($lampiran->isNotEmpty())
        <div style="margin-top:10px">
            @foreach ($lampiran as $l)
                <label class="p-check" style="display:flex;margin-top:4px">
                    <input type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}">
                    Hapus <a href="{{ file_url($l->file_path) }}" target="_blank">{{ $l->file_name }}</a>
                </label>
            @endforeach
        </div>
    @endif
</div>
