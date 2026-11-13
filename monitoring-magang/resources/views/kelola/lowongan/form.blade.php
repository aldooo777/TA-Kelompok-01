@extends('layouts.pdlk')

@php
    $baru = !$lowongan->exists;
    $fields = old('field_tambahan', $lowongan->fieldTambahan?->map(fn ($f) => ['label' => $f->label, 'tipe' => $f->tipe])->all() ?? []);
@endphp

@section('title', ($baru ? 'Tambah' : 'Ubah') . ' Lowongan | Monitoring PDLK')
@section('breadcrumb', 'Kelola Lowongan')
@section('page-title', $baru ? 'Tambah Lowongan' : 'Ubah Lowongan')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-stack"
          action="{{ $baru ? route('kelola.lowongan.store') : route('kelola.lowongan.update', $lowongan) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-card">
            <div class="p-card-header">
                <div>
                    <h2 class="p-card-title">Informasi Lowongan</h2>
                    <p class="p-card-subtitle">Mahasiswa membaca deskripsi dan kriteria ini sebelum memutuskan untuk mendaftar.</p>
                </div>
            </div>

            <div class="p-form-grid">
                <div class="p-field p-span-2">
                    <label for="judul">Judul lowongan <span class="p-required">*</span></label>
                    <input id="judul" name="judul" class="p-input" required maxlength="200"
                           value="{{ old('judul', $lowongan->judul) }}" placeholder="Contoh: Magang Software Engineer Semester Genap">
                </div>

                <div class="p-field">
                    <label for="nama_mitra">Nama mitra <span class="p-required">*</span></label>
                    <input id="nama_mitra" name="nama_mitra" class="p-input" required list="daftarMitra"
                           value="{{ old('nama_mitra', $lowongan->mitra->nama ?? '') }}" placeholder="Ketik nama perusahaan / instansi">
                    <datalist id="daftarMitra">
                        @foreach ($mitraList as $m)<option value="{{ $m }}">@endforeach
                    </datalist>
                    <p class="p-help">Mitra baru otomatis tersimpan.</p>
                </div>

                <div class="p-field">
                    <label for="jenis_kegiatan_id">Bentuk kegiatan <span class="p-required">*</span></label>
                    <select id="jenis_kegiatan_id" name="jenis_kegiatan_id" class="p-select" required>
                        <option value="">Pilih bentuk kegiatan</option>
                        @foreach ($jenisKegiatan as $j)
                            <option value="{{ $j->id }}" @selected((int) old('jenis_kegiatan_id', $lowongan->jenis_kegiatan_id) === $j->id)>{{ $j->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-field">
                    <label for="posisi">Posisi <span class="p-optional">(opsional)</span></label>
                    <input id="posisi" name="posisi" class="p-input" value="{{ old('posisi', $lowongan->posisi) }}">
                </div>

                <div class="p-field">
                    <label for="lokasi">Lokasi <span class="p-optional">(opsional)</span></label>
                    <input id="lokasi" name="lokasi" class="p-input" value="{{ old('lokasi', $lowongan->lokasi) }}" placeholder="Contoh: Jakarta / Remote">
                </div>

                <div class="p-field">
                    <label for="tanggal_buka">Tanggal buka <span class="p-optional">(opsional)</span></label>
                    <input type="date" id="tanggal_buka" name="tanggal_buka" class="p-input"
                           value="{{ old('tanggal_buka', $lowongan->tanggal_buka?->toDateString()) }}">
                </div>

                <div class="p-field">
                    <label for="tanggal_tutup">Tanggal tutup <span class="p-optional">(opsional)</span></label>
                    <input type="date" id="tanggal_tutup" name="tanggal_tutup" class="p-input"
                           value="{{ old('tanggal_tutup', $lowongan->tanggal_tutup?->toDateString()) }}">
                </div>

                <div class="p-field">
                    <label for="kuota">Kuota <span class="p-optional">(opsional)</span></label>
                    <input type="number" min="1" id="kuota" name="kuota" class="p-input" value="{{ old('kuota', $lowongan->kuota) }}">
                </div>

                <div class="p-field">
                    <label for="poster">Poster <span class="p-optional">(opsional, maks. 4 MB)</span></label>
                    <input type="file" id="poster" name="poster" accept="image/jpeg,image/png,image/webp" class="p-input">
                    @if ($lowongan->poster_url)
                        <label class="p-check" style="margin-top:8px"><input type="checkbox" name="hapus_poster" value="1"> Hapus poster saat ini</label>
                    @endif
                </div>

                <div class="p-field p-span-2">
                    <label for="deskripsi">Deskripsi kegiatan <span class="p-required">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" class="p-textarea" rows="6" required>{{ old('deskripsi', $lowongan->deskripsi) }}</textarea>
                </div>

                <div class="p-field p-span-2">
                    <label for="kriteria">Kriteria pendaftar <span class="p-required">*</span></label>
                    <textarea id="kriteria" name="kriteria" class="p-textarea" rows="6" required
                              placeholder="Contoh:&#10;- Mahasiswa semester 5 ke atas&#10;- IPK minimal 3.00&#10;- Menguasai dasar pemrograman web">{{ old('kriteria', $lowongan->kriteria) }}</textarea>
                </div>

                <div class="p-field p-span-2">
                    <input type="hidden" name="is_published" value="0">
                    <label class="p-check">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $lowongan->is_published))>
                        Tampilkan di halaman publik
                    </label>
                </div>
            </div>
        </div>

        <div class="p-card">
            <div class="p-card-header">
                <div>
                    <h2 class="p-card-title">Isian Tambahan Formulir <span class="p-optional">(opsional)</span></h2>
                    <p class="p-card-subtitle">
                        Formulir pendaftaran sudah berisi nama, NIM, prodi, semester, IPK, no. HP, email, dan satu file dokumen gabungan.
                        Tambahkan isian lain hanya bila diminta mitra. Isian tambahan <strong>tidak wajib</strong> diisi mahasiswa.
                    </p>
                </div>
                <button type="button" class="p-btn p-btn-light p-btn-sm" id="tambahField">+ Tambah isian</button>
            </div>

            <div id="daftarField">
                @foreach ($fields as $i => $f)
                    <div class="p-extra-row">
                        <input name="field_tambahan[{{ $i }}][label]" class="p-input" value="{{ $f['label'] ?? '' }}" placeholder="Contoh: Link GitHub">
                        <select name="field_tambahan[{{ $i }}][tipe]" class="p-select">
                            @foreach (\App\Models\LowonganFieldTambahan::TIPE as $kode => $label)
                                <option value="{{ $kode }}" @selected(($f['tipe'] ?? 'text') === $kode)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="p-btn p-btn-danger p-btn-sm" data-hapus>Hapus</button>
                    </div>
                @endforeach
            </div>
            <p class="p-muted" id="kosongField" @if (count($fields)) hidden @endif>Belum ada isian tambahan.</p>
        </div>

        <div class="p-form-actions" style="border:0;margin:0;padding:0">
            <a href="{{ route('kelola.lowongan.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan Lowongan' : 'Simpan Perubahan' }}</button>
        </div>
    </form>
</div>

<template id="templateField">
    <div class="p-extra-row">
        <input name="field_tambahan[__i__][label]" class="p-input" placeholder="Contoh: Link GitHub">
        <select name="field_tambahan[__i__][tipe]" class="p-select">
            @foreach (\App\Models\LowonganFieldTambahan::TIPE as $kode => $label)
                <option value="{{ $kode }}">{{ $label }}</option>
            @endforeach
        </select>
        <button type="button" class="p-btn p-btn-danger p-btn-sm" data-hapus>Hapus</button>
    </div>
</template>

<script>
    (function () {
        const box = document.getElementById('daftarField');
        const kosong = document.getElementById('kosongField');
        let i = {{ count($fields) }};
        const segarkan = () => { kosong.hidden = box.children.length > 0; };

        document.getElementById('tambahField').addEventListener('click', function () {
            const html = document.getElementById('templateField').innerHTML.replaceAll('__i__', i++);
            box.insertAdjacentHTML('beforeend', html);
            segarkan();
        });

        box.addEventListener('click', function (e) {
            if (e.target.matches('[data-hapus]')) {
                e.target.closest('.p-extra-row').remove();
                segarkan();
            }
        });
    })();
</script>
@endsection
