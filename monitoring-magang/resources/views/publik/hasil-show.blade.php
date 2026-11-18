@extends('layouts.publik')

@section('title', $hasil->title)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('hasil.index') }}">Pengumuman Hasil</a></div>
        <h1>{{ $hasil->title }}</h1>
        <p>{{ tgl($hasil->published_at, 'l, d F Y') }}
            @if ($hasil->lowongan_slug) · Lowongan: <a href="{{ route('lowongan.show', $hasil->lowongan_slug) }}">{{ $hasil->lowongan_title }}</a>@endif
        </p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    @if ($hasil->content)
        <div class="p-card"><div class="p-prose">{{ $hasil->content }}</div></div>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Mahasiswa yang Lulus ({{ $items->count() }})</h2>
        @if ($items->isEmpty())
            <p class="p-muted">Belum ada nama.</p>
        @else
            <div class="p-table-wrap" style="margin-top:12px">
                <table class="p-table" style="min-width:480px">
                    <thead><tr><th style="width:60px">No</th><th>NIM</th><th>Nama</th><th>Program Studi</th></tr></thead>
                    <tbody>
                        @foreach ($items as $i)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $i->nim }}</td><td><strong>{{ $i->student_name }}</strong></td><td>{{ $i->prodi ?? '-' }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('partials.lampiran', ['lampiran' => $lampiran])
</div>
@endsection
