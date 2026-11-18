@extends('layouts.pdlk')

@section('title', 'Galeri | Monitoring PDLK')
@section('breadcrumb', 'Informasi / Galeri')
@section('page-title', 'Galeri Kegiatan')

@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Galeri</h2>
                <p class="p-card-subtitle">Galeri berstatus Terbit tampil di halaman publik.</p>
            </div>
            <a href="{{ route('kelola.galeri.create') }}" class="p-btn p-btn-primary">+ Buat Galeri</a>
        </div>

        @if ($galeri->isEmpty())
            <div class="p-empty">Belum ada galeri.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Sampul</th><th>Judul</th><th>Tanggal</th><th>Item</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($galeri as $g)
                            <tr>
                                <td style="width:84px">
                                    @if ($g->cover_image)
                                        <img src="{{ file_url($g->cover_image) }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:4px">
                                    @else <span class="p-muted">—</span> @endif
                                </td>
                                <td><strong>{{ $g->title }}</strong><span class="p-muted">{{ ringkas($g->description, 80) }}</span></td>
                                <td>{{ tgl($g->event_date, 'd/m/Y') }}</td>
                                <td>{{ $g->jumlah_item }}</td>
                                <td><span class="p-badge p-badge-{{ $g->status }}">{{ \App\Support\Label::of(\App\Support\Label::GALLERY_STATUS, $g->status) }}</span></td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.galeri.edit', $g->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.galeri.destroy', $g->id) }}" style="display:inline" onsubmit="return confirm('Hapus galeri ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $galeri])
        @endif
    </div>
</div>
@endsection
