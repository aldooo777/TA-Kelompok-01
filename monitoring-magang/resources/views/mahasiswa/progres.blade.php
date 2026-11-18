@extends('layouts.pdlk')

@php
    use App\Support\Label;
    $p = $peserta;
@endphp

@section('title', 'Progres Pelaksanaan | Monitoring PDLK')
@section('breadcrumb', 'Progres Pelaksanaan')
@section('page-title', 'Progres Pelaksanaan PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <dl class="p-dl">
            <dt>Kegiatan</dt><dd>{{ $p->activity_name ?? '-' }}</dd>
            <dt>Mitra</dt><dd>{{ $p->partner_name ?? '-' }}</dd>
            @if ($p->position)<dt>Posisi</dt><dd>{{ $p->position }}</dd>@endif
            @if ($p->period_name)<dt>Periode</dt><dd>{{ $p->period_name }}</dd>@endif
            <dt>Terakhir diperbarui</dt><dd>{{ tgl($p->updated_at, 'd F Y H:i') }}</dd>
        </dl>
    </div>

    <form method="POST" action="{{ route('mahasiswa.progres.update') }}" class="p-card">
        @csrf @method('PUT')
        <h2 class="p-card-title">Perbarui Progres</h2>
        <p class="p-card-subtitle">Beri tahu UPT PP-ESTEM tahap kegiatan, mode kerja, dan lokasi Anda saat ini.</p>

        <div class="p-form-grid" style="margin-top:16px">
            <div class="p-field">
                <label for="stage">Tahap <span class="p-required">*</span></label>
                <select id="stage" name="stage" class="p-select" required>
                    @foreach (Label::STAGE as $v => $l)
                        <option value="{{ $v }}" @selected(old('stage', $p->stage) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="work_mode">Mode kerja <span class="p-required">*</span></label>
                <select id="work_mode" name="work_mode" class="p-select" required>
                    @foreach (Label::WORK_MODE as $v => $l)
                        <option value="{{ $v }}" @selected(old('work_mode', $p->work_mode) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field p-span-2">
                <label for="location">Lokasi saat ini <span class="p-required">*</span></label>
                <input id="location" name="location" class="p-input" required maxlength="200"
                       value="{{ old('location', $p->location) }}" placeholder="Contoh: Kantor mitra, Jakarta Selatan">
                @error('location')<p class="p-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="p-form-actions">
            <a href="{{ route('dashboard') }}" class="p-btn p-btn-light">← Dashboard</a>
            <button class="p-btn p-btn-primary">Simpan Progres</button>
        </div>
    </form>
</div>
@endsection
