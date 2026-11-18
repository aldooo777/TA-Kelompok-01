@extends('layouts.pdlk')

@php
    use App\Support\Label;
    $baru = $peserta === null;
    $p = $peserta;
@endphp

@section('title', ($baru ? 'Tambah' : 'Ubah') . ' Mahasiswa Aktif | Monitoring PDLK')
@section('breadcrumb', 'Mahasiswa Aktif PDLK')
@section('page-title', $baru ? 'Tambah Mahasiswa Aktif PDLK' : 'Ubah Data Mahasiswa PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    @if ($baru)
        <div class="p-alert p-alert-info">
            Untuk mahasiswa <strong>PDLK mandiri</strong> atau yang mendaftar lewat <strong>tautan eksternal mitra</strong>.
            Mahasiswa yang mendaftar lewat formulir sistem otomatis tercatat saat statusnya diubah menjadi Lulus.
        </div>
    @endif

    <form method="POST" class="p-card" action="{{ $baru ? route('peserta.store') : route('peserta.update', $p->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-form-grid">
            <div class="p-field">
                <label for="nim">NIM <span class="p-required">*</span></label>
                <input id="nim" name="nim" class="p-input" required maxlength="30" value="{{ old('nim', $p?->nim) }}">
                @error('nim')<p class="p-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-field">
                <label for="name">Nama mahasiswa <span class="p-required">*</span></label>
                <input id="name" name="name" class="p-input" required maxlength="150" value="{{ old('name', $p?->name) }}">
            </div>
            <div class="p-field">
                <label for="study_program_id">Program studi <span class="p-required">*</span></label>
                <select id="study_program_id" name="study_program_id" class="p-select" required>
                    <option value="">Pilih program studi</option>
                    @foreach ($prodiList as $pr)
                        <option value="{{ $pr->id }}" @selected((int) old('study_program_id', $p?->study_program_id) === (int) $pr->id)>{{ $pr->label }}</option>
                    @endforeach
                </select>
                @error('study_program_id')<p class="p-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-field">
                <label for="period_id">Periode</label>
                <select id="period_id" name="period_id" class="p-select">
                    <option value="">Tanpa periode</option>
                    @foreach ($periodeList as $pr)
                        <option value="{{ $pr->id }}" @selected((int) old('period_id', $p?->period_id) === (int) $pr->id)>{{ $pr->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="activity_type_id">Bentuk kegiatan <span class="p-required">*</span></label>
                <select id="activity_type_id" name="activity_type_id" class="p-select" required>
                    <option value="">Pilih bentuk kegiatan</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j->id }}" @selected((int) old('activity_type_id', $p?->activity_type_id) === (int) $j->id)>{{ $j->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="program_category">Kategori program</label>
                <select id="program_category" name="program_category" class="p-select">
                    <option value="">Belum ditentukan</option>
                    @foreach (Label::PROGRAM_CATEGORY as $v => $l)
                        <option value="{{ $v }}" @selected(old('program_category', $p?->program_category) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="nama_mitra">Mitra <span class="p-required">*</span></label>
                <input id="nama_mitra" name="nama_mitra" class="p-input" required list="daftarMitra" value="{{ old('nama_mitra', $p?->partner_name) }}">
                <datalist id="daftarMitra">@foreach ($mitraList as $m)<option value="{{ $m }}">@endforeach</datalist>
                <p class="p-help">Mitra baru otomatis tersimpan.</p>
            </div>
            <div class="p-field">
                <label for="position">Posisi</label>
                <input id="position" name="position" class="p-input" maxlength="150" value="{{ old('position', $p?->position) }}">
            </div>
            <div class="p-field">
                <label for="start_date">Tanggal mulai</label>
                <input type="date" id="start_date" name="start_date" class="p-input" value="{{ old('start_date', $p?->start_date) }}">
            </div>
            <div class="p-field">
                <label for="end_date">Tanggal selesai</label>
                <input type="date" id="end_date" name="end_date" class="p-input" value="{{ old('end_date', $p?->end_date) }}">
                @error('end_date')<p class="p-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-field">
                <label for="mentor_name">Nama mentor (mitra)</label>
                <input id="mentor_name" name="mentor_name" class="p-input" maxlength="150" value="{{ old('mentor_name', $p?->mentor_name) }}">
            </div>
            <div class="p-field">
                <label for="advisor_name">Dosen pembimbing</label>
                <input id="advisor_name" name="advisor_name" class="p-input" maxlength="150" value="{{ old('advisor_name', $p?->advisor_name) }}">
            </div>
            <div class="p-field p-span-2">
                <label for="location">Lokasi pelaksanaan</label>
                <input id="location" name="location" class="p-input" maxlength="200" value="{{ old('location', $p?->location) }}">
            </div>
        </div>

        <div class="p-form-actions">
            <a href="{{ $baru ? route('peserta.index') : route('peserta.show', $p->id) }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan sebagai Mahasiswa Aktif' : 'Simpan Perubahan' }}</button>
        </div>
    </form>
</div>
@endsection
