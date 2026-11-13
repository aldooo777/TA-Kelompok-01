@extends('layouts.publik')

@section('title', $postingan->judul)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('berita.index') }}">Berita &amp; Pengumuman</a> / {{ $postingan->jenis_label }}</div>
        <h1>{{ $postingan->judul }}</h1>
        <p>{{ $postingan->published_at?->translatedFormat('l, d F Y') }}</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    @if ($postingan->foto_url)
        <div class="p-detail-media"><img src="{{ $postingan->foto_url }}" alt="{{ $postingan->judul }}"></div>
    @endif

    <div class="p-card"><div class="p-prose">{{ $postingan->narasi }}</div></div>

    @if ($lainnya->isNotEmpty())
        <section>
            <div class="pub-section-head"><h2>Postingan Lainnya</h2></div>
            <div class="p-grid-cards">
                @foreach ($lainnya as $item)
                    @include('partials.kartu-postingan', ['item' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
