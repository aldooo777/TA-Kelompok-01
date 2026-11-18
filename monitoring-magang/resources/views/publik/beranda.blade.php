@extends('layouts.publik')

@section('title', 'Beranda')

@push('styles')
<style>
    .beranda-hero { background: var(--accent); color: #fff; padding: 44px 0; }
    .beranda-hero h1 { margin: 0 0 10px; font-size: 30px; line-height: 1.25; }
    .beranda-hero p { margin: 0; max-width: 620px; line-height: 1.6; opacity: .95; }
    .beranda-hero .aksi { margin-top: 20px; display: flex; gap: 8px; flex-wrap: wrap; }
    .beranda-hero .p-btn-light { color: var(--accent-dark); }
    .beranda-hero .tombol-garis { border: 1px solid #fff; color: #fff; background: transparent; }
    .beranda-hero .tombol-garis:hover { background: rgba(255, 255, 255, .15); color: #fff; }
    .info-singkat { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .info-singkat .p-card { padding: 16px; }
    .info-singkat b { display: block; font-size: 24px; color: #26364d; }
    .info-singkat span { color: var(--muted); font-size: 13px; }
    @media (max-width: 820px) {
        .beranda-hero { padding: 32px 0; }
        .beranda-hero h1 { font-size: 24px; }
        .info-singkat { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('hero')
<section class="beranda-hero">
    <div class="pub-container">
        <h1>Pembelajaran di Luar Kampus<br>Institut Teknologi Del</h1>
        <p>
            Informasi kegiatan magang, kerja praktik, penelitian, dan kegiatan PDLK lainnya.
            Baca kriteria setiap lowongan, lalu daftar menggunakan akun CIS.
        </p>
        <div class="aksi">
            <a href="{{ route('lowongan.index') }}" class="p-btn p-btn-light">Lihat Lowongan</a>
            <a href="{{ route('tentang') }}" class="p-btn tombol-garis">Tentang PDLK</a>
        </div>
    </div>
</section>
@endsection

@section('content')
    <section class="pub-section">
        <div class="info-singkat">
            <div class="p-card"><b>{{ $statistik['lowongan'] }}</b><span>Lowongan sedang dibuka</span></div>
            <div class="p-card"><b>{{ $statistik['mitra'] }}</b><span>Mitra</span></div>
            <div class="p-card"><b>{{ $statistik['bentuk'] }}</b><span>Bentuk kegiatan PDLK</span></div>
        </div>
    </section>

    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Lowongan Terbaru</h2>
            <a href="{{ route('lowongan.index') }}">Lihat semua</a>
        </div>
        @if ($lowongan->isEmpty())
            <div class="p-card p-empty">Belum ada lowongan yang dipublikasikan.</div>
        @else
            <div class="p-grid-cards">
                @foreach ($lowongan as $item)
                    @include('partials.kartu-lowongan', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>

    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Berita</h2>
            <a href="{{ route('berita.index') }}">Lihat semua</a>
        </div>
        @if ($berita->isEmpty())
            <div class="p-card p-empty">Belum ada berita.</div>
        @else
            <div class="p-grid-cards">
                @foreach ($berita as $item)
                    @include('partials.kartu-postingan', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>

    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Bentuk Kegiatan PDLK</h2>
            <a href="{{ route('tentang') }}">Selengkapnya</a>
        </div>
        <div class="p-grid-cards">
            @foreach ($jenisKegiatan as $jenis)
                <a href="{{ route('lowongan.index', ['jenis' => $jenis->slug]) }}" class="p-card" style="color:inherit">
                    <strong style="color:#26364d">@if ($jenis->icon){{ $jenis->icon }} @endif{{ $jenis->name }}</strong>
                    @if ($jenis->summary)
                        <p class="p-muted" style="margin:8px 0 0;line-height:1.5">{{ $jenis->summary }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    @if ($galeri->isNotEmpty())
    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Galeri Kegiatan</h2>
            <a href="{{ route('galeri.index') }}">Lihat semua</a>
        </div>
        <div class="p-grid-cards">
            @foreach ($galeri as $item)
                @include('partials.kartu-galeri', ['item' => $item])
            @endforeach
        </div>
    </section>
    @endif
@endsection
