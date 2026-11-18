@extends('layouts.pdlk')

@php
    use App\Support\Label;
    $p = $peserta;
@endphp

@section('title', $p->name . ' | Monitoring PDLK')
@section('breadcrumb', 'Mahasiswa Aktif PDLK / Detail')
@section('page-title', 'Detail Mahasiswa PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">{{ $p->name }}</h2>
                <p class="p-card-subtitle">{{ $p->nim }} · {{ $p->prodi_code ? $p->prodi_level . ' ' . $p->prodi_name : 'Prodi belum diisi' }}</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('peserta.index') }}" class="p-btn p-btn-light">← Kembali</a>
                @if ($bolehUbah)
                    <a href="{{ route('peserta.edit', $p->id) }}" class="p-btn p-btn-primary">Ubah Data</a>
                @endif
            </div>
        </div>

        <dl class="p-dl">
            <dt>Status</dt>
            <dd><span class="p-badge p-badge-{{ $p->status }}">{{ Label::of(Label::PARTICIPANT_STATUS, $p->status) }}</span>
                @if ($p->inactive_reason)<span class="p-muted"> {{ $p->inactive_reason }}</span>@endif</dd>
            <dt>Sumber data</dt><dd>{{ Label::of(Label::SOURCE, $p->source) }}</dd>
            <dt>Bentuk kegiatan</dt><dd>{{ $p->activity_name ?? '-' }}</dd>
            <dt>Kategori program</dt><dd>{{ Label::of(Label::PROGRAM_CATEGORY, $p->program_category) }}</dd>
            <dt>Mitra</dt><dd>{{ $p->partner_name ?? '-' }}</dd>
            <dt>Posisi</dt><dd>{{ $p->position ?? '-' }}</dd>
            <dt>Periode</dt><dd>{{ $p->period_name ?? '-' }}</dd>
            <dt>Tanggal pelaksanaan</dt><dd>@if ($p->start_date || $p->end_date){{ tgl($p->start_date) ?: '?' }} – {{ tgl($p->end_date) ?: '?' }}@else - @endif</dd>
            <dt>Mentor (mitra)</dt><dd>{{ $p->mentor_name ?? '-' }}</dd>
            <dt>Dosen pembimbing</dt><dd>{{ $p->advisor_name ?? '-' }}</dd>
            <dt>Progres (diisi mahasiswa)</dt>
            <dd>{{ Label::of(Label::STAGE, $p->stage) }} · {{ Label::of(Label::WORK_MODE, $p->work_mode) }} @if ($p->location) · {{ $p->location }} @endif</dd>
            <dt>Dicatat</dt><dd>{{ tgl($p->created_at, 'd F Y H:i') }}</dd>
        </dl>
    </div>

    @if ($bolehUbahStatus && $p->status === 'active')
        <form method="POST" action="{{ route('peserta.nonaktifkan', $p->id) }}" class="p-card"
              onsubmit="return confirm('Ubah status {{ $p->name }} menjadi Tidak Aktif?')">
            @csrf @method('PATCH')
            <h2 class="p-card-title">Ubah Status menjadi Tidak Aktif</h2>
            <p class="p-card-subtitle">Hanya untuk kondisi force majeure (misalnya sakit). Tidak ada fitur pembatalan magang.</p>
            <div class="p-field" style="margin-top:12px">
                <label for="alasan">Alasan <span class="p-required">*</span></label>
                <textarea id="alasan" name="alasan" class="p-textarea" rows="3" required></textarea>
                @error('alasan')<p class="p-error">{{ $message }}</p>@enderror
            </div>
            <div class="p-form-actions"><span></span><button class="p-btn p-btn-danger">Simpan Status Tidak Aktif</button></div>
        </form>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Riwayat Status</h2>
        @if ($riwayat->isEmpty())
            <p class="p-muted">Belum ada riwayat.</p>
        @else
            <div class="p-table-wrap" style="margin-top:12px">
                <table class="p-table">
                    <thead><tr><th>Waktu</th><th>Perubahan</th><th>Alasan</th><th>Oleh</th></tr></thead>
                    <tbody>
                        @foreach ($riwayat as $r)
                            <tr>
                                <td>{{ tgl($r->created_at, 'd/m/Y H:i') }}</td>
                                <td>{{ $r->old_status ? Label::of(Label::PARTICIPANT_STATUS, $r->old_status) . ' → ' : '' }}<strong>{{ Label::of(Label::PARTICIPANT_STATUS, $r->new_status) }}</strong></td>
                                <td>{{ $r->reason ?? '-' }}</td>
                                <td>{{ $r->oleh ?? 'Sistem' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
