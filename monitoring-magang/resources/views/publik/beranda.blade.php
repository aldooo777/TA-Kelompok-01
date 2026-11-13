@extends('layouts.publik')

@section('title', 'Beranda')

@section('hero')
<section class="pub-hero">
    <div class="pub-container">
        <h1>Pembelajaran di Luar Kampus<br>Institut Teknologi Del</h1>
        <p>
            Temukan kesempatan magang, kerja praktik, penelitian, dan kegiatan PDLK lainnya.
            Baca kriteria setiap lowongan, lalu daftar melalui akun CIS Anda.
        </p>
        <a href="{{ route('lowongan.index') }}" class="p-btn p-btn-light">Lihat Lowongan PDLK →</a>
    </div>
</section>
@endsection

@section('content')
    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Lowongan Terbaru</h2>
            <a href="{{ route('lowongan.index') }}">Lihat semua →</a>
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
            <h2>Berita &amp; Pengumuman</h2>
            <a href="{{ route('berita.index') }}">Lihat semua →</a>
        </div>
        @if ($postingan->isEmpty())
            <div class="p-card p-empty">Belum ada berita atau pengumuman.</div>
        @else
            <div class="p-grid-cards">
                @foreach ($postingan as $item)
                    @include('partials.kartu-postingan', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>

    <section class="pub-section">
        <div class="pub-section-head">
            <h2>Bentuk Kegiatan PDLK</h2>
            <a href="{{ route('tentang') }}">Selengkapnya →</a>
        </div>
        <div class="p-grid-cards">
            @foreach ($jenisKegiatan as $jenis)
                <a href="{{ route('lowongan.index', ['jenis' => $jenis->slug]) }}" class="p-card" style="color:inherit">
                    <strong style="color:#26364d">{{ $jenis->nama }}</strong>
                    <p class="p-muted" style="margin:8px 0 0;line-height:1.5">{{ $jenis->deskripsi }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
