@extends('layouts.publik')

@section('title', $lowongan->judul)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('lowongan.index') }}">Lowongan</a> / {{ $lowongan->jenisKegiatan->nama ?? '' }}</div>
        <h1>{{ $lowongan->judul }}</h1>
        <p>{{ $lowongan->mitra->nama ?? '' }}@if ($lowongan->lokasi) · {{ $lowongan->lokasi }}@endif</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    <div class="p-card">
        <dl class="p-dl">
            <dt>Mitra</dt><dd>{{ $lowongan->mitra->nama ?? '-' }}</dd>
            <dt>Bentuk kegiatan</dt><dd>{{ $lowongan->jenisKegiatan->nama ?? '-' }}</dd>
            @if ($lowongan->posisi)<dt>Posisi</dt><dd>{{ $lowongan->posisi }}</dd>@endif
            @if ($lowongan->kuota)<dt>Kuota</dt><dd>{{ $lowongan->kuota }} orang</dd>@endif
            <dt>Periode pendaftaran</dt>
            <dd>
                {{ $lowongan->tanggal_buka?->translatedFormat('d F Y') ?? 'Sekarang' }}
                – {{ $lowongan->tanggal_tutup?->translatedFormat('d F Y') ?? 'Sampai kuota terpenuhi' }}
                @if ($lowongan->dibuka)
                    <span class="p-badge p-badge-dibuka">Dibuka</span>
                @else
                    <span class="p-badge p-badge-ditutup">Ditutup</span>
                @endif
            </dd>
        </dl>
    </div>

    @if ($lowongan->poster_url)
        <div class="p-detail-media"><img src="{{ $lowongan->poster_url }}" alt="Poster {{ $lowongan->judul }}"></div>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Deskripsi</h2>
        <div class="p-prose" style="margin-top:12px">{{ $lowongan->deskripsi }}</div>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Kriteria Pendaftar</h2>
        <div class="p-prose" style="margin-top:12px">{{ $lowongan->kriteria }}</div>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Cara Mendaftar</h2>
        <p class="p-card-subtitle">
            Siapkan satu file PDF yang menggabungkan CV, portofolio, surat keterangan, dan transkrip/KHS.
            Seleksi dilakukan oleh mitra; UPT PP-ESTEM akan meneruskan data pendaftar dan memperbarui status pendaftaran Anda.
        </p>
        <div style="margin-top:16px">
            @if ($lowongan->dibuka)
                <a href="{{ route('mahasiswa.pendaftaran.create', $lowongan->slug) }}" class="p-btn p-btn-primary">
                    {{ session()->has('cis_user') ? 'Daftar Sekarang →' : 'Login untuk Mendaftar →' }}
                </a>
            @else
                <button class="p-btn p-btn-light" disabled>Pendaftaran ditutup</button>
            @endif
        </div>
    </div>
</div>
@endsection
