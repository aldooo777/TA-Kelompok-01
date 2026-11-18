@extends('layouts.pdlk')

@section('title', 'Kelola Berita | Monitoring PDLK')
@section('breadcrumb', 'Kelola Berita')
@section('page-title', 'Berita')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Berita</h2>
                <p class="p-card-subtitle">Berita yang terbit tampil di halaman publik tanpa perlu login.</p>
            </div>
            <a href="{{ route('kelola.postingan.create') }}" class="p-btn p-btn-primary">+ Buat Berita</a>
        </div>

        @if ($berita->isEmpty())
            <div class="p-empty">Belum ada berita. Klik <strong>Buat Berita</strong> untuk memulai.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Foto</th><th>Judul</th><th>Status</th><th>Tanggal</th><th>Lampiran</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($berita as $item)
                            <tr>
                                <td style="width:84px">
                                    @if ($item->cover_image)
                                        <img src="{{ file_url($item->cover_image) }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:4px">
                                    @else
                                        <span class="p-muted">—</span>
                                    @endif
                                </td>
                                <td><strong>{{ $item->title }}</strong><span class="p-muted">{{ $item->summary ?: ringkas($item->content, 80) }}</span></td>
                                <td><span class="p-badge p-badge-{{ $item->status }}">{{ \App\Support\Label::of(\App\Support\Label::ANNOUNCEMENT_STATUS, $item->status) }}</span></td>
                                <td>{{ tgl($item->published_at ?? $item->created_at, 'd/m/Y') }}</td>
                                <td>{{ $item->jumlah_lampiran }}</td>
                                <td style="white-space:nowrap">
                                    @if ($item->status === 'published')
                                        <a href="{{ route('berita.show', $item->slug) }}" target="_blank" class="p-btn p-btn-light p-btn-sm">Lihat</a>
                                    @endif
                                    <a href="{{ route('kelola.postingan.edit', $item->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.postingan.destroy', $item->id) }}" style="display:inline"
                                          onsubmit="return confirm('Hapus berita ini dari daftar?')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $berita])
        @endif
    </div>
</div>
@endsection
