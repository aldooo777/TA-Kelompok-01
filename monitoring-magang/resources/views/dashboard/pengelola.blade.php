@extends('layouts.pdlk')

@php
    use App\Support\Peran;
    use App\Models\Role;
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
            @if ($peran === Role::STAF_UPT)
                Kelola informasi, lowongan, pendaftar, dan pantau mahasiswa aktif PDLK seluruh program studi.
            @else
                Pantau mahasiswa PDLK di program studi Anda.
            @endif
        </p>
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Mahasiswa PDLK</h2>
                <p class="p-card-subtitle">Ringkasan status mahasiswa aktif {{ $peran === Role::STAF_UPT ? 'seluruh prodi' : 'di prodi Anda' }}.</p>
            </div>
            <a href="{{ route('peserta.index') }}" class="p-btn p-btn-primary">Lihat Mahasiswa Aktif →</a>
        </div>
        <div class="p-stats">
            @foreach ($statistikPeserta as $kode => $jumlah)
                <a class="p-stat" style="color:inherit" href="{{ route('peserta.index', ['status' => $kode]) }}">
                    <div class="p-stat-label">{{ \App\Models\PesertaPdlk::STATUS[$kode] }}</div>
                    <div class="p-stat-value">{{ $jumlah }}</div>
                </a>
            @endforeach
        </div>
    </div>

    @if ($statistikPendaftaran !== null)
        <div class="p-card">
            <div class="p-card-header">
                <div>
                    <h2 class="p-card-title">Pendaftaran Lowongan</h2>
                    <p class="p-card-subtitle">{{ $jumlahLowonganDibuka }} lowongan sedang dibuka · {{ $jumlahPostingan }} postingan terbit.</p>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="{{ route('kelola.lowongan.create') }}" class="p-btn p-btn-light">+ Lowongan</a>
                    <a href="{{ route('kelola.postingan.create') }}" class="p-btn p-btn-light">+ Postingan</a>
                    <a href="{{ route('kelola.pendaftaran.index') }}" class="p-btn p-btn-primary">Kelola Pendaftar →</a>
                </div>
            </div>
            <div class="p-stats">
                @foreach (\App\Models\Pendaftaran::STATUS as $kode => $label)
                    <a class="p-stat" style="color:inherit" href="{{ route('kelola.pendaftaran.index', ['status' => $kode]) }}">
                        <div class="p-stat-label">{{ $label }}</div>
                        <div class="p-stat-value">{{ $statistikPendaftaran[$kode] ?? 0 }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
