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
            <a href="{{ route('portal.lowongan.index') }}" class="p-btn p-btn-primary">Cari Lowongan</a>
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
                                <td><strong>{{ $p->vacancy_title }}</strong><span class="p-muted">{{ $p->partner_name }}</span></td>
                                <td>{{ $p->activity_name }}</td>
                                <td>{{ tgl($p->created_at, 'd/m/Y H:i') }}</td>
                                <td><span class="p-badge p-badge-{{ $p->status }}">{{ \App\Support\Label::of(\App\Support\Label::APPLICATION_STATUS, $p->status) }}</span></td>
                                <td>
                                    @switch($p->status)
                                        @case('registered') Menunggu diteruskan ke mitra. @break
                                        @case('processing') Data Anda sedang diseleksi mitra. @break
                                        @case('passed') Selamat! Anda tercatat sebagai mahasiswa aktif PDLK. @break
                                        @case('failed') Belum lolos. Silakan coba lowongan lain. @break
                                    @endswitch
                                    @if ($p->notes)<span class="p-muted">Catatan UPT: {{ $p->notes }}</span>@endif
                                </td>
                                <td><a href="{{ route('mahasiswa.pendaftaran.dokumen', $p->id) }}">Unduh</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
