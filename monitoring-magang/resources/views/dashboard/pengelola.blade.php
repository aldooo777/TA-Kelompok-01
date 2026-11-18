@extends('layouts.pdlk')

@php
    use App\Support\Peran;
    $peran = Peran::aktif();
    $nama = session('cis_user')['nama'] ?? session('cis_user')['name'] ?? session('cis_user')['username'] ?? '';
@endphp

@section('title', 'Dashboard | Monitoring PDLK')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard ' . Peran::label())

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card" style="background:linear-gradient(135deg,var(--accent-dark),var(--accent));color:#fff;border:0">
        <p style="margin:0 0 6px;opacity:.85;font-size:13px;letter-spacing:.5px;text-transform:uppercase">{{ Peran::label() }}</p>
        <h2 style="margin:0;font-size:24px">Selamat datang, {{ $nama }}!</h2>
        <p style="margin:10px 0 0;opacity:.92;line-height:1.6">
            @if ($peran === 'upt_staff' && !sprint(2))
                Kelola informasi PDLK: berita, lowongan, galeri, data master, serta pengguna dan peran.
            @elseif ($peran === 'upt_staff')
                Kelola informasi, lowongan, pendaftar, dan pantau mahasiswa aktif PDLK seluruh program studi.
            @elseif (!sprint(2))
                Fitur pemantauan mahasiswa PDLK program studi akan tersedia pada tahap berikutnya.
            @else
                Pantau mahasiswa PDLK di program studi Anda.
            @endif
        </p>
    </div>

    @if (sprint(2))
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Mahasiswa PDLK</h2>
                <p class="p-card-subtitle">Ringkasan status mahasiswa aktif {{ $peran === 'upt_staff' ? 'seluruh prodi' : 'di prodi Anda' }}.</p>
            </div>
            <a href="{{ route('peserta.index') }}" class="p-btn p-btn-primary">Lihat Mahasiswa Aktif →</a>
        </div>
        <div class="p-stats">
            @foreach (\App\Support\Label::PARTICIPANT_STATUS as $kode => $labelStatus)
                <a class="p-stat" style="color:inherit" href="{{ route('peserta.index', ['status' => $kode]) }}">
                    <div class="p-stat-label">{{ $labelStatus }}</div>
                    <div class="p-stat-value">{{ $statistikPeserta[$kode] ?? 0 }}</div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if ($statistikPendaftaran !== null)
        <div class="p-card">
            <div class="p-card-header">
                <div>
                    <h2 class="p-card-title">Pendaftaran Lowongan</h2>
                    <p class="p-card-subtitle">{{ $jumlahLowonganDibuka }} lowongan sedang dibuka · {{ $jumlahBerita }} berita terbit.</p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="{{ route('kelola.lowongan.create') }}" class="p-btn p-btn-light">+ Lowongan</a>
                    <a href="{{ route('kelola.postingan.create') }}" class="p-btn p-btn-light">+ Berita</a>
                    @if (sprint(2))
                        <a href="{{ route('kelola.pendaftaran.index') }}" class="p-btn p-btn-primary">Kelola Pendaftar →</a>
                    @endif
                </div>
            </div>
            <div class="p-stats">
                @if (sprint(2))
                    @foreach (\App\Support\Label::APPLICATION_STATUS as $kode => $label)
                        <a class="p-stat" style="color:inherit" href="{{ route('kelola.pendaftaran.index', ['status' => $kode]) }}">
                            <div class="p-stat-label">{{ $label }}</div>
                            <div class="p-stat-value">{{ $statistikPendaftaran[$kode] ?? 0 }}</div>
                        </a>
                    @endforeach
                @else
                    <div class="p-stat">
                        <div class="p-stat-label">Pendaftaran masuk</div>
                        <div class="p-stat-value">{{ $statistikPendaftaran->sum() }}</div>
                    </div>
                    <div class="p-stat">
                        <div class="p-stat-label">Lowongan dibuka</div>
                        <div class="p-stat-value">{{ $jumlahLowonganDibuka }}</div>
                    </div>
                    <div class="p-stat">
                        <div class="p-stat-label">Berita terbit</div>
                        <div class="p-stat-value">{{ $jumlahBerita }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
