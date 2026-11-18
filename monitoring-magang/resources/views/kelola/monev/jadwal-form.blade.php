@extends('layouts.pdlk')

@php
    $baru = $jadwal === null;
    $dt = fn ($v) => $v ? substr(str_replace(' ', 'T', $v), 0, 16) : '';
@endphp

@section('title', ($baru ? 'Tambah' : 'Ubah') . ' Jadwal Monev | Monitoring PDLK')
@section('breadcrumb', 'Monev / Jadwal')
@section('page-title', $baru ? 'Tambah Jadwal Monev' : 'Ubah Jadwal Monev')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    @if ($kuesionerList->isEmpty() || $periodeList->isEmpty())
        <div class="p-alert p-alert-warning">
            Buat dulu
            @if ($kuesionerList->isEmpty()) <a href="{{ route('kelola.kuesioner.create') }}">kuesioner monev</a> @endif
            @if ($kuesionerList->isEmpty() && $periodeList->isEmpty()) dan @endif
            @if ($periodeList->isEmpty()) <a href="{{ route('kelola.master.create', 'periode') }}">periode PDLK</a> @endif
            sebelum membuat jadwal.
        </div>
    @endif

    <form method="POST" class="p-card" action="{{ $baru ? route('kelola.jadwal-monev.store') : route('kelola.jadwal-monev.update', $jadwal->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless
        <div class="p-form-grid">
            <div class="p-field">
                <label for="questionnaire_id">Kuesioner <span class="p-required">*</span></label>
                <select id="questionnaire_id" name="questionnaire_id" class="p-select" required>
                    <option value="">Pilih kuesioner</option>
                    @foreach ($kuesionerList as $q)
                        <option value="{{ $q->id }}" @selected((int) old('questionnaire_id', $jadwal?->questionnaire_id) === (int) $q->id)>{{ $q->label }}</option>
                    @endforeach
                </select>
                @error('questionnaire_id')<p class="p-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-field">
                <label for="period_id">Periode <span class="p-required">*</span></label>
                <select id="period_id" name="period_id" class="p-select" required>
                    <option value="">Pilih periode</option>
                    @foreach ($periodeList as $p)
                        <option value="{{ $p->id }}" @selected((int) old('period_id', $jadwal?->period_id) === (int) $p->id)>{{ $p->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="open_at">Dibuka <span class="p-required">*</span></label>
                <input type="datetime-local" id="open_at" name="open_at" class="p-input" required value="{{ old('open_at', $dt($jadwal?->open_at)) }}">
            </div>
            <div class="p-field">
                <label for="deadline_at">Tenggat <span class="p-required">*</span></label>
                <input type="datetime-local" id="deadline_at" name="deadline_at" class="p-input" required value="{{ old('deadline_at', $dt($jadwal?->deadline_at)) }}">
                @error('deadline_at')<p class="p-error">{{ $message }}</p>@enderror
                <p class="p-help">Setelah tenggat, mahasiswa masih bisa mengirim, tetapi tercatat terlambat.</p>
            </div>
            <div class="p-field p-span-2">
                <input type="hidden" name="is_closed" value="0">
                <label class="p-check"><input type="checkbox" name="is_closed" value="1" @checked(old('is_closed', $jadwal?->is_closed))> Tutup pengisian (mahasiswa tidak dapat mengisi lagi dan dashboard tidak terkunci)</label>
            </div>
        </div>
        <div class="p-form-actions">
            <a href="{{ route('kelola.jadwal-monev.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">Simpan Jadwal</button>
        </div>
    </form>
</div>
@endsection
