@extends('layouts.publik')

@section('title', 'Berita')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Berita</h1>
        <p>Informasi kegiatan dan pengumuman PDLK dari UPT PP-ESTEM.</p>
    </div>
</section>
@endsection

@section('content')
    @if ($berita->isEmpty())
        <div class="p-card p-empty">Belum ada berita.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($berita as $item)
                @include('partials.kartu-postingan', ['item' => $item])
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $berita])
    @endif
@endsection
