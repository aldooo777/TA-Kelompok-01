@extends('layouts.publik')

@section('title', 'Pengumuman Hasil Seleksi')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Pengumuman Hasil Seleksi</h1>
        <p>Daftar mahasiswa yang dinyatakan lulus seleksi mitra PDLK.</p>
    </div>
</section>
@endsection

@section('content')
    @if ($hasil->isEmpty())
        <div class="p-card p-empty">Belum ada pengumuman hasil.</div>
    @else
        <div class="p-stack">
            @foreach ($hasil as $h)
                <a href="{{ route('hasil.show', $h->slug) }}" class="p-card" style="color:inherit;display:block">
                    <div class="p-post-meta"><span>{{ tgl($h->published_at) }}</span><span>{{ $h->jumlah }} mahasiswa</span></div>
                    <h3 class="p-post-title" style="margin-top:6px">{{ $h->title }}</h3>
                    @if ($h->summary)<p class="p-post-text" style="margin-top:6px">{{ $h->summary }}</p>@endif
                </a>
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $hasil])
    @endif
@endsection
