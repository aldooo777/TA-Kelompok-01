@extends('layouts.publik')

@section('title', 'Berita & Pengumuman')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Berita &amp; Pengumuman</h1>
        <p>Informasi kegiatan dan pengumuman PDLK dari UPT PP-ESTEM.</p>
    </div>
</section>
@endsection

@section('content')
    <div class="p-toolbar">
        <a href="{{ route('berita.index') }}" class="p-btn p-btn-sm {{ request('jenis') ? 'p-btn-light' : 'p-btn-primary' }}">Semua</a>
        @foreach (\App\Models\Postingan::JENIS as $kode => $label)
            <a href="{{ route('berita.index', ['jenis' => $kode]) }}"
               class="p-btn p-btn-sm {{ request('jenis') === $kode ? 'p-btn-primary' : 'p-btn-light' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($postingan->isEmpty())
        <div class="p-card p-empty">Belum ada postingan.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($postingan as $item)
                @include('partials.kartu-postingan', ['item' => $item])
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $postingan])
    @endif
@endsection
