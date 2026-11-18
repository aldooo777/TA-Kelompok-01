@extends('layouts.pdlk')

@php
    use App\Support\Label;
@endphp

@section('title', 'Mahasiswa Aktif PDLK | Monitoring PDLK')
@section('breadcrumb', 'Mahasiswa Aktif PDLK')
@section('page-title', 'Mahasiswa Aktif PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Data Mahasiswa PDLK</h2>
                <p class="p-card-subtitle">
                    Mahasiswa masuk ke daftar ini setelah lulus seleksi lowongan, atau setelah diinput manual untuk PDLK mandiri / tautan mitra.
                    Tidak ada pembatalan; status <strong>Tidak Aktif</strong> hanya untuk force majeure.
                </p>
            </div>
            @if ($bolehInput)
                <a href="{{ route('peserta.create') }}" class="p-btn p-btn-primary">+ Tambah Mahasiswa Aktif</a>
            @endif
        </div>

        <form method="GET" class="p-toolbar">
            <div class="p-field">
                <label for="prodi">Program studi</label>
                <select id="prodi" name="prodi" class="p-select">
                    <option value="">Semua prodi</option>
                    @foreach ($prodiList as $pr)
                        <option value="{{ $pr->id }}" @selected((int) request('prodi') === (int) $pr->id)>{{ $pr->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="periode">Periode</label>
                <select id="periode" name="periode" class="p-select">
                    <option value="">Semua periode</option>
                    @foreach ($periodeList as $pr)
                        <option value="{{ $pr->id }}" @selected((int) request('periode') === (int) $pr->id)>{{ $pr->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="jenis">Bentuk kegiatan</label>
                <select id="jenis" name="jenis" class="p-select">
                    <option value="">Semua bentuk</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j->id }}" @selected((int) request('jenis') === (int) $j->id)>{{ $j->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="status">Status</label>
                <select id="status" name="status" class="p-select">
                    <option value="">Semua status</option>
                    @foreach (Label::PARTICIPANT_STATUS as $kode => $label)
                        <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="q">Cari</label>
                <input id="q" name="q" class="p-input" value="{{ request('q') }}" placeholder="Nama atau NIM">
            </div>
            <button class="p-btn p-btn-primary">Terapkan</button>
        </form>

        @if ($peserta->isEmpty())
            <div class="p-empty">Tidak ada data mahasiswa yang sesuai.</div>
        @else
            <p class="p-muted" style="margin:0 0 10px">Total: {{ $peserta->total() }} mahasiswa</p>
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Mahasiswa</th><th>Kegiatan</th><th>Progres</th><th>Sumber</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($peserta as $ps)
                            <tr>
                                <td><strong>{{ $ps->name }}</strong><span class="p-muted">{{ $ps->nim }} · {{ $ps->prodi_code ? $ps->prodi_level . ' ' . $ps->prodi_name : 'Prodi belum diisi' }}</span></td>
                                <td>{{ $ps->activity_name ?? '-' }}<span class="p-muted">{{ $ps->partner_name ?? '-' }}@if ($ps->position) · {{ $ps->position }}@endif</span>@if ($ps->period_name)<span class="p-muted">{{ $ps->period_name }}</span>@endif</td>
                                <td>{{ $ps->stage ? Label::of(Label::STAGE, $ps->stage) : '—' }}<span class="p-muted">{{ $ps->work_mode ? Label::of(Label::WORK_MODE, $ps->work_mode) : '' }}@if ($ps->location) · {{ $ps->location }}@endif</span></td>
                                <td>{{ Label::of(Label::SOURCE, $ps->source) }}</td>
                                <td>
                                    <span class="p-badge p-badge-{{ $ps->status }}">{{ Label::of(Label::PARTICIPANT_STATUS, $ps->status) }}</span>
                                    @if ($ps->status === 'inactive' && $ps->inactive_reason)
                                        <span class="p-muted">{{ $ps->inactive_reason }}</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('peserta.show', $ps->id) }}" class="p-btn p-btn-light p-btn-sm">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $peserta])
        @endif
    </div>
</div>
@endsection
