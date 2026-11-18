@extends('layouts.publik')

@section('title', $galeri->title)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('galeri.index') }}">Galeri</a></div>
        <h1>{{ $galeri->title }}</h1>
        <p>{{ tgl($galeri->event_date, 'l, d F Y') }}</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    @if ($galeri->description)
        <div class="p-card"><div class="p-prose">{{ $galeri->description }}</div></div>
    @endif

    @if ($items->isEmpty())
        <div class="p-card p-empty">Belum ada foto atau video.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($items as $item)
                <figure class="p-post" style="margin:0;cursor:default">
                    <div class="p-post-media">
                        @if ($item->media_type === 'video')
                            <video src="{{ file_url($item->file_path) }}" controls style="width:100%;height:100%;object-fit:cover"></video>
                        @else
                            <a href="{{ file_url($item->file_path) }}" target="_blank"><img src="{{ file_url($item->file_path) }}" alt="{{ $item->caption ?? $galeri->title }}" loading="lazy"></a>
                        @endif
                    </div>
                    @if ($item->caption)
                        <figcaption class="p-post-body"><span class="p-post-text">{{ $item->caption }}</span></figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    @endif
</div>
@endsection
