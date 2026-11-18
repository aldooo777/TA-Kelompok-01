@extends('layouts.pdlk')

@php
    $baru = $berita === null;
@endphp

@section('title', ($baru ? 'Buat' : 'Ubah') . ' Berita | Monitoring PDLK')
@section('breadcrumb', 'Kelola Berita')
@section('page-title', $baru ? 'Buat Berita' : 'Ubah Berita')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-card"
          action="{{ $baru ? route('kelola.postingan.store') : route('kelola.postingan.update', $berita->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Isi Berita</h2>
                <p class="p-card-subtitle">Cukup pilih foto dan tulis narasinya, seperti membuat postingan di media sosial.</p>
            </div>
        </div>

        <div class="p-form-grid">
            <div class="p-field p-span-2">
                <label for="foto">Foto <span class="p-optional">(opsional, JPG/PNG/WEBP, maks. 4 MB)</span></label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" class="p-input">
                <div class="p-upload-preview" id="fotoPreview" @if (!$berita?->cover_image) hidden @endif>
                    <img src="{{ file_url($berita?->cover_image) }}" alt="Pratinjau foto">
                </div>
                @if ($berita?->cover_image)
                    <label class="p-check" style="margin-top:8px"><input type="checkbox" name="hapus_foto" value="1"> Hapus foto saat ini</label>
                @endif
            </div>

            <div class="p-field p-span-2">
                <label for="title">Judul <span class="p-required">*</span></label>
                <input id="title" name="title" class="p-input" maxlength="200" required
                       value="{{ old('title', $berita?->title) }}" placeholder="Contoh: Pembekalan Peserta Magang Batch 2">
            </div>

            <div class="p-field p-span-2">
                <label for="summary">Ringkasan <span class="p-optional">(opsional, tampil di kartu berita)</span></label>
                <input id="summary" name="summary" class="p-input" maxlength="255" value="{{ old('summary', $berita?->summary) }}">
            </div>

            <div class="p-field p-span-2">
                <label for="content">Narasi <span class="p-required">*</span></label>
                <textarea id="content" name="content" class="p-textarea" rows="8" required
                          placeholder="Tuliskan isi berita...">{{ old('content', $berita?->content) }}</textarea>
            </div>

            <div class="p-field">
                <label for="expired_at">Tampil sampai <span class="p-optional">(opsional)</span></label>
                <input type="datetime-local" id="expired_at" name="expired_at" class="p-input"
                       value="{{ old('expired_at', $berita?->expired_at ? substr(str_replace(' ', 'T', $berita->expired_at), 0, 16) : '') }}">
                <p class="p-help">Kosongkan bila berita tampil seterusnya.</p>
            </div>

            @include('partials.form-lampiran', ['lampiran' => $lampiran])

            <div class="p-field p-span-2">
                <input type="hidden" name="terbit" value="0">
                <label class="p-check">
                    <input type="checkbox" name="terbit" value="1" @checked(old('terbit', $baru || $berita->status === 'published'))>
                    Terbitkan sekarang (tampil di halaman publik)
                </label>
            </div>
        </div>

        <div class="p-form-actions">
            <a href="{{ route('kelola.postingan.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan Berita' : 'Simpan Perubahan' }}</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('foto').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const box = document.getElementById('fotoPreview');
        if (!file) return;
        box.querySelector('img').src = URL.createObjectURL(file);
        box.hidden = false;
    });
</script>
@endsection
