@extends('layouts.publik')

@section('title', 'Galeri')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Galeri Kegiatan</h1>
        <p>Dokumentasi kegiatan PDLK dan UPT PP-ESTEM.</p>
    </div>
</section>
@endsection

@section('content')
    @if ($galeri->isEmpty())
        <div class="p-card p-empty">Belum ada galeri.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($galeri as $item)
                @include('partials.kartu-galeri', ['item' => $item])
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $galeri])
    @endif
@endsection
