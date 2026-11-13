@extends('layouts.publik')

@section('title', 'Lowongan PDLK')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Lowongan PDLK</h1>
        <p>Baca deskripsi dan kriteria setiap lowongan sebelum memutuskan untuk mendaftar.</p>
    </div>
</section>
@endsection

@section('content')
    <form method="GET" class="p-toolbar">
        <div class="p-field">
            <label for="q">Cari</label>
            <input id="q" name="q" class="p-input" value="{{ $q }}" placeholder="Judul, posisi, atau nama mitra">
        </div>
        <div class="p-field">
            <label for="jenis">Bentuk kegiatan</label>
            <select id="jenis" name="jenis" class="p-select">
                <option value="">Semua bentuk</option>
                @foreach ($jenis as $j)
                    <option value="{{ $j->slug }}" @selected(request('jenis') === $j->slug)>{{ $j->nama }}</option>
                @endforeach
            </select>
        </div>
        <button class="p-btn p-btn-primary">Terapkan</button>
    </form>

    @if ($lowongan->isEmpty())
        <div class="p-card p-empty">Tidak ada lowongan yang sesuai.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($lowongan as $item)
                @include('partials.kartu-lowongan', ['item' => $item])
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $lowongan])
    @endif
@endsection
