@extends('layouts.pdlk')

@php
    $baru = $lowongan === null;
    $l = $lowongan;
    $fields = old('field_tambahan', $fields->map(fn ($f) => ['label' => $f->label, 'field_type' => $f->field_type])->all());
    $tipeDaftar = old('registration_type', $l?->registration_type ?? 'internal');
@endphp

@section('title', ($baru ? 'Tambah' : 'Ubah') . ' Lowongan | Monitoring PDLK')
@section('breadcrumb', 'Kelola Lowongan')
@section('page-title', $baru ? 'Tambah Lowongan' : 'Ubah Lowongan')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-stack"
          action="{{ $baru ? route('kelola.lowongan.store') : route('kelola.lowongan.update', $l->id) }}">
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
                    <label for="title">Judul lowongan <span class="p-required">*</span></label>
                    <input id="title" name="title" class="p-input" required maxlength="200"
                           value="{{ old('title', $l?->title) }}" placeholder="Contoh: Magang Software Engineer Semester Genap">
                </div>

                <div class="p-field">
                    <label for="nama_mitra">Nama mitra <span class="p-required">*</span></label>
                    <input id="nama_mitra" name="nama_mitra" class="p-input" required list="daftarMitra"
                           value="{{ old('nama_mitra', $l?->partner_name) }}" placeholder="Ketik nama perusahaan / instansi">
                    <datalist id="daftarMitra">
                        @foreach ($mitraList as $m)<option value="{{ $m }}">@endforeach
                    </datalist>
                    <p class="p-help">Mitra baru otomatis tersimpan.</p>
                </div>

                <div class="p-field">
                    <label for="activity_type_id">Bentuk kegiatan <span class="p-required">*</span></label>
                    <select id="activity_type_id" name="activity_type_id" class="p-select" required>
                        <option value="">Pilih bentuk kegiatan</option>
                        @foreach ($jenisKegiatan as $j)
                            <option value="{{ $j->id }}" @selected((int) old('activity_type_id', $l?->activity_type_id) === (int) $j->id)>{{ $j->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-field">
                    <label for="period_id">Periode <span class="p-optional">(opsional)</span></label>
                    <select id="period_id" name="period_id" class="p-select">
                        <option value="">Tanpa periode</option>
                        @foreach ($periodeList as $pr)
                            <option value="{{ $pr->id }}" @selected((int) old('period_id', $l?->period_id) === (int) $pr->id)>{{ $pr->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-field">
                    <label for="position">Posisi <span class="p-optional">(opsional)</span></label>
                    <input id="position" name="position" class="p-input" value="{{ old('position', $l?->position) }}">
                </div>

                <div class="p-field">
                    <label for="location">Lokasi <span class="p-optional">(opsional)</span></label>
                    <input id="location" name="location" class="p-input" value="{{ old('location', $l?->location) }}" placeholder="Contoh: Jakarta / Remote">
                </div>

                <div class="p-field">
                    <label for="quota">Kuota <span class="p-optional">(opsional)</span></label>
                    <input type="number" min="1" id="quota" name="quota" class="p-input" value="{{ old('quota', $l?->quota) }}">
                </div>

                <div class="p-field">
                    <label for="registration_start_date">Tanggal buka <span class="p-optional">(opsional)</span></label>
                    <input type="date" id="registration_start_date" name="registration_start_date" class="p-input"
                           value="{{ old('registration_start_date', $l?->registration_start_date) }}">
                </div>

                <div class="p-field">
                    <label for="registration_deadline">Batas pendaftaran <span class="p-optional">(opsional)</span></label>
                    <input type="date" id="registration_deadline" name="registration_deadline" class="p-input"
                           value="{{ old('registration_deadline', $l?->registration_deadline) }}">
                </div>

                <div class="p-field">
                    <label for="registration_type">Cara pendaftaran <span class="p-required">*</span></label>
                    <select id="registration_type" name="registration_type" class="p-select">
                        <option value="internal" @selected($tipeDaftar === 'internal')>Internal: formulir di sistem ini</option>
                        <option value="external" @selected($tipeDaftar === 'external')>Eksternal: tautan situs mitra</option>
                    </select>
                </div>

                <div class="p-field" id="boxExternal" @if ($tipeDaftar !== 'external') hidden @endif>
                    <label for="external_url">Tautan pendaftaran mitra <span class="p-required">*</span></label>
                    <input type="url" id="external_url" name="external_url" class="p-input"
                           value="{{ old('external_url', $l?->external_url) }}" placeholder="https://...">
                    <p class="p-help">Mahasiswa yang lulus didata manual sebagai mahasiswa aktif.</p>
                </div>

                <div class="p-field p-span-2">
                    <label for="cover">Poster <span class="p-optional">(opsional, maks. 4 MB)</span></label>
                    <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" class="p-input">
                    @if ($l?->cover_image)
                        <label class="p-check" style="margin-top:8px"><input type="checkbox" name="hapus_cover" value="1"> Hapus poster saat ini</label>
                    @endif
                </div>

                <div class="p-field p-span-2">
                    <label for="summary">Ringkasan <span class="p-optional">(opsional)</span></label>
                    <input id="summary" name="summary" class="p-input" maxlength="255" value="{{ old('summary', $l?->summary) }}">
                </div>

                <div class="p-field p-span-2">
                    <label for="content">Deskripsi kegiatan <span class="p-required">*</span></label>
                    <textarea id="content" name="content" class="p-textarea" rows="6" required>{{ old('content', $l?->content) }}</textarea>
                </div>

                <div class="p-field p-span-2">
                    <label for="criteria">Kriteria pendaftar <span class="p-required">*</span></label>
                    <textarea id="criteria" name="criteria" class="p-textarea" rows="6" required
                              placeholder="Contoh:&#10;- Mahasiswa semester 5 ke atas&#10;- IPK minimal 3.00&#10;- Menguasai dasar pemrograman web">{{ old('criteria', $l?->criteria) }}</textarea>
                </div>

                <div class="p-field">
                    <label for="expired_at">Pengumuman tampil sampai <span class="p-optional">(opsional)</span></label>
                    <input type="datetime-local" id="expired_at" name="expired_at" class="p-input"
                           value="{{ old('expired_at', $l?->expired_at ?? null ? substr(str_replace(' ', 'T', $l->expired_at), 0, 16) : '') }}">
                </div>

                @include('partials.form-lampiran', ['lampiran' => $lampiran])

                <div class="p-field">
                    <input type="hidden" name="terbit" value="0">
                    <label class="p-check">
                        <input type="checkbox" name="terbit" value="1" @checked(old('terbit', $baru || $l->status === 'published'))>
                        Terbitkan di halaman publik
                    </label>
                </div>

                <div class="p-field">
                    <input type="hidden" name="is_closed" value="0">
                    <label class="p-check">
                        <input type="checkbox" name="is_closed" value="1" @checked(old('is_closed', $l?->is_closed))>
                        Tutup pendaftaran sekarang (sebelum batas waktu)
                    </label>
                </div>
            </div>
        </div>

        <div class="p-card" id="boxFieldTambahan" @if ($tipeDaftar === 'external') hidden @endif>
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
                        <select name="field_tambahan[{{ $i }}][field_type]" class="p-select">
                            @foreach (\App\Support\Label::FIELD_TYPE as $kode => $label)
                                <option value="{{ $kode }}" @selected(($f['field_type'] ?? 'text') === $kode)>{{ $label }}</option>
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
        <select name="field_tambahan[__i__][field_type]" class="p-select">
            @foreach (\App\Support\Label::FIELD_TYPE as $kode => $label)
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

        document.getElementById('registration_type').addEventListener('change', function () {
            const eksternal = this.value === 'external';
            document.getElementById('boxExternal').hidden = !eksternal;
            document.getElementById('boxFieldTambahan').hidden = eksternal;
        });
    })();
</script>
@endsection
