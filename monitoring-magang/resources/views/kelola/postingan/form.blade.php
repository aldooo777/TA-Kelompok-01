@extends('layouts.pdlk')

@php
    $baru = !$postingan->exists;
@endphp

@section('title', ($baru ? 'Buat' : 'Ubah') . ' Postingan | Monitoring PDLK')
@section('breadcrumb', 'Kelola Postingan')
@section('page-title', $baru ? 'Buat Postingan' : 'Ubah Postingan')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-card"
          action="{{ $baru ? route('kelola.postingan.store') : route('kelola.postingan.update', $postingan) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Isi Postingan</h2>
                <p class="p-card-subtitle">Cukup pilih foto dan tulis narasinya, seperti membuat postingan di media sosial.</p>
            </div>
        </div>

        <div class="p-form-grid">
            <div class="p-field p-span-2">
                <label for="foto">Foto <span class="p-optional">(opsional, JPG/PNG/WEBP, maks. 4 MB)</span></label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" class="p-input">
                <div class="p-upload-preview" id="fotoPreview" @if (!$postingan->foto_url) hidden @endif>
                    <img src="{{ $postingan->foto_url }}" alt="Pratinjau foto">
                </div>
                @if ($postingan->foto_url)
                    <label class="p-check" style="margin-top:8px"><input type="checkbox" name="hapus_foto" value="1"> Hapus foto saat ini</label>
                @endif
            </div>

            <div class="p-field">
                <label for="judul">Judul <span class="p-required">*</span></label>
                <input id="judul" name="judul" class="p-input" maxlength="200" required
                       value="{{ old('judul', $postingan->judul) }}" placeholder="Contoh: Pembekalan Peserta Magang Batch 2">
            </div>

            <div class="p-field">
                <label for="jenis">Jenis <span class="p-required">*</span></label>
                <select id="jenis" name="jenis" class="p-select">
                    @foreach (\App\Models\Postingan::JENIS as $kode => $label)
                        <option value="{{ $kode }}" @selected(old('jenis', $postingan->jenis) === $kode)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-field p-span-2">
                <label for="narasi">Narasi <span class="p-required">*</span></label>
                <textarea id="narasi" name="narasi" class="p-textarea" rows="8" required
                          placeholder="Tuliskan isi berita atau pengumuman...">{{ old('narasi', $postingan->narasi) }}</textarea>
            </div>

            <div class="p-field p-span-2">
                <input type="hidden" name="is_published" value="0">
                <label class="p-check">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $postingan->is_published))>
                    Terbitkan sekarang (tampil di halaman publik)
                </label>
            </div>
        </div>

        <div class="p-form-actions">
            <a href="{{ route('kelola.postingan.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan Postingan' : 'Simpan Perubahan' }}</button>
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
