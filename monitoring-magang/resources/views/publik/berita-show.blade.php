@extends('layouts.publik')

@section('title', $berita->title)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('berita.index') }}">Berita</a></div>
        <h1>{{ $berita->title }}</h1>
        <p>{{ tgl($berita->published_at, 'l, d F Y') }}</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    @if ($berita->cover_image)
        <div class="p-detail-media"><img src="{{ file_url($berita->cover_image) }}" alt="{{ $berita->title }}"></div>
    @endif

    <div class="p-card"><div class="p-prose">{{ $berita->content }}</div></div>

    @include('partials.lampiran', ['lampiran' => $lampiran])


    @if ($lainnya->isNotEmpty())
        <section>
            <div class="pub-section-head"><h2>Berita Lainnya</h2></div>
            <div class="p-grid-cards">
                @foreach ($lainnya as $item)
                    @include('partials.kartu-postingan', ['item' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
