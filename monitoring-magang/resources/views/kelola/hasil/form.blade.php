@extends('layouts.pdlk')

@php
    $baru = $hasil === null;
    $items = old('items', collect($items)->map(fn ($i) => (array) $i)->all());
@endphp

@section('title', ($baru ? 'Buat' : 'Ubah') . ' Pengumuman Hasil | Monitoring PDLK')
@section('breadcrumb', 'Informasi / Pengumuman Hasil')
@section('page-title', $baru ? 'Buat Pengumuman Hasil' : 'Ubah Pengumuman Hasil')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    @if ($baru)
        <form method="GET" class="p-card">
            <div class="p-toolbar" style="margin:0">
                <div class="p-field">
                    <label for="lowongan">Isi otomatis dari pendaftar Lulus pada lowongan internal</label>
                    <select id="lowongan" name="lowongan" class="p-select">
                        <option value="">Pilih lowongan</option>
                        @foreach ($lowonganList as $l)
                            <option value="{{ $l->id }}" @selected((int) request('lowongan') === (int) $l->id)>{{ $l->label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="p-btn p-btn-light">Ambil Data</button>
            </div>
        </form>
    @endif

    <form method="POST" enctype="multipart/form-data" class="p-stack"
          action="{{ $baru ? route('kelola.hasil.store') : route('kelola.hasil.update', $hasil->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-card">
            <div class="p-form-grid">
                <div class="p-field p-span-2">
                    <label for="title">Judul <span class="p-required">*</span></label>
                    <input id="title" name="title" class="p-input" required maxlength="200"
                           value="{{ old('title', $hasil?->title ?? ($sumber ? 'Hasil Seleksi: ' . $sumber->title : '')) }}">
                </div>
                <div class="p-field">
                    <label for="source_announcement_id">Lowongan asal</label>
                    <select id="source_announcement_id" name="source_announcement_id" class="p-select">
                        <option value="">—</option>
                        @foreach ($lowonganList as $l)
                            <option value="{{ $l->id }}" @selected((int) old('source_announcement_id', $hasil?->source_announcement_id ?? $sumber?->id) === (int) $l->id)>{{ $l->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-field">
                    <label for="summary">Ringkasan</label>
                    <input id="summary" name="summary" class="p-input" maxlength="255" value="{{ old('summary', $hasil?->summary) }}">
                </div>
                <div class="p-field p-span-2">
                    <label for="content">Keterangan</label>
                    <textarea id="content" name="content" class="p-textarea" rows="4" placeholder="Contoh: Mahasiswa yang lulus akan dihubungi langsung oleh mitra.">{{ old('content', $hasil?->content) }}</textarea>
                </div>
                @include('partials.form-lampiran', ['lampiran' => $lampiran])
                <div class="p-field p-span-2">
                    <input type="hidden" name="terbit" value="0">
                    <label class="p-check"><input type="checkbox" name="terbit" value="1" @checked(old('terbit', $baru || $hasil->status === 'published'))> Terbitkan di halaman publik</label>
                </div>
            </div>
        </div>

        <div class="p-card">
            <div class="p-card-header">
                <div>
                    <h2 class="p-card-title">Mahasiswa yang Lulus</h2>
                    <p class="p-card-subtitle">Baris dengan nama kosong diabaikan.</p>
                </div>
                <button type="button" class="p-btn p-btn-light p-btn-sm" id="tambahBaris">+ Tambah baris</button>
            </div>
            <div class="p-table-wrap">
                <table class="p-table" style="min-width:560px">
                    <thead><tr><th style="width:150px">NIM</th><th>Nama</th><th style="width:240px">Prodi</th><th></th></tr></thead>
                    <tbody id="barisLulus">
                        @foreach ($items as $n => $i)
                            <tr>
                                <td><input name="items[{{ $n }}][nim]" class="p-input" value="{{ $i['nim'] ?? '' }}"></td>
                                <td><input name="items[{{ $n }}][student_name]" class="p-input" value="{{ $i['student_name'] ?? '' }}"></td>
                                <td><select name="items[{{ $n }}][study_program_id]" class="p-select">
                                    <option value="">—</option>
                                    @foreach ($prodiList as $p)<option value="{{ $p->id }}" @selected((int) ($i['study_program_id'] ?? 0) === (int) $p->id)>{{ $p->label }}</option>@endforeach
                                </select></td>
                                <td><button type="button" class="p-btn p-btn-danger p-btn-sm" data-hapus>Hapus</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-form-actions" style="border:0;margin:0;padding:0">
            <a href="{{ route('kelola.hasil.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">Simpan Pengumuman</button>
        </div>
    </form>
</div>

<template id="templateBaris">
    <tr>
        <td><input name="items[__i__][nim]" class="p-input"></td>
        <td><input name="items[__i__][student_name]" class="p-input"></td>
        <td><select name="items[__i__][study_program_id]" class="p-select"><option value="">—</option>
            @foreach ($prodiList as $p)<option value="{{ $p->id }}">{{ $p->label }}</option>@endforeach
        </select></td>
        <td><button type="button" class="p-btn p-btn-danger p-btn-sm" data-hapus>Hapus</button></td>
    </tr>
</template>
<script>
    (function () {
        const body = document.getElementById('barisLulus');
        let i = {{ count($items) + 1 }};
        document.getElementById('tambahBaris').addEventListener('click', () =>
            body.insertAdjacentHTML('beforeend', document.getElementById('templateBaris').innerHTML.replaceAll('__i__', i++)));
        body.addEventListener('click', e => { if (e.target.matches('[data-hapus]')) e.target.closest('tr').remove(); });
        if (!body.children.length) document.getElementById('tambahBaris').click();
    })();
</script>
@endsection
