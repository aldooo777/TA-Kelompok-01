@extends('layouts.pdlk')

@section('title', 'Pendaftaran Saya | Monitoring PDLK')
@section('breadcrumb', 'Pendaftaran Saya')
@section('page-title', 'Progres Pendaftaran')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Pendaftaran Saya</h2>
                <p class="p-card-subtitle">
                    Alur status: <strong>Terdaftar → Proses → Lulus / Tidak Lulus</strong>.
                    Seleksi dilakukan mitra; mitra akan menghubungi Anda langsung.
                </p>
            </div>
            <a href="{{ route('lowongan.index') }}" class="p-btn p-btn-primary">Cari Lowongan</a>
        </div>

        @if ($pendaftaran->isEmpty())
            <div class="p-empty">Anda belum mendaftar pada lowongan mana pun.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Lowongan</th><th>Bentuk</th><th>Tanggal Daftar</th><th>Status</th><th>Keterangan</th><th>Dokumen</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $p)
                            <tr>
                                <td><strong>{{ $p->lowongan->judul }}</strong><span class="p-muted">{{ $p->lowongan->mitra->nama ?? '-' }}</span></td>
                                <td>{{ $p->lowongan->jenisKegiatan->nama ?? '-' }}</td>
                                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="p-badge p-badge-{{ $p->status }}">{{ $p->status_label }}</span></td>
                                <td>
                                    @switch($p->status)
                                        @case('terdaftar') Menunggu diteruskan ke mitra. @break
                                        @case('proses') Data Anda sedang diseleksi mitra. @break
                                        @case('lulus') Selamat! Anda tercatat sebagai mahasiswa aktif PDLK. @break
                                        @case('tidak_lulus') Belum lolos. Silakan coba lowongan lain. @break
                                    @endswitch
                                    @if ($p->catatan)<span class="p-muted">Catatan UPT: {{ $p->catatan }}</span>@endif
                                </td>
                                <td><a href="{{ route('mahasiswa.pendaftaran.dokumen', $p) }}">Unduh</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
