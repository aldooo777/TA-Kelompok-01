@extends('layouts.pdlk')

@section('title', 'Kelola Postingan | Monitoring PDLK')
@section('breadcrumb', 'Kelola Postingan')
@section('page-title', 'Berita & Pengumuman')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Postingan</h2>
                <p class="p-card-subtitle">Postingan yang diterbitkan tampil di halaman publik tanpa perlu login.</p>
            </div>
            <a href="{{ route('kelola.postingan.create') }}" class="p-btn p-btn-primary">+ Buat Postingan</a>
        </div>

        <div class="p-toolbar">
            <a href="{{ route('kelola.postingan.index') }}" class="p-btn p-btn-sm {{ request('jenis') ? 'p-btn-light' : 'p-btn-primary' }}">Semua</a>
            @foreach (\App\Models\Postingan::JENIS as $kode => $label)
                <a href="{{ route('kelola.postingan.index', ['jenis' => $kode]) }}"
                   class="p-btn p-btn-sm {{ request('jenis') === $kode ? 'p-btn-primary' : 'p-btn-light' }}">{{ $label }}</a>
            @endforeach
        </div>

        @if ($postingan->isEmpty())
            <div class="p-empty">Belum ada postingan. Klik <strong>Buat Postingan</strong> untuk memulai.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Foto</th><th>Judul</th><th>Jenis</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($postingan as $item)
                            <tr>
                                <td style="width:84px">
                                    @if ($item->foto_url)
                                        <img src="{{ $item->foto_url }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:4px">
                                    @else
                                        <span class="p-muted">—</span>
                                    @endif
                                </td>
                                <td><strong>{{ $item->judul }}</strong><span class="p-muted">{{ \Illuminate\Support\Str::limit($item->narasi, 80) }}</span></td>
                                <td>{{ $item->jenis_label }}</td>
                                <td>
                                    @if ($item->is_published)
                                        <span class="p-badge p-badge-terbit">Terbit</span>
                                    @else
                                        <span class="p-badge p-badge-draft">Draf</span>
                                    @endif
                                </td>
                                <td>{{ ($item->published_at ?? $item->created_at)->format('d/m/Y') }}</td>
                                <td style="white-space:nowrap">
                                    @if ($item->is_published)
                                        <a href="{{ route('berita.show', $item->slug) }}" target="_blank" class="p-btn p-btn-light p-btn-sm">Lihat</a>
                                    @endif
                                    <a href="{{ route('kelola.postingan.edit', $item) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.postingan.destroy', $item) }}" style="display:inline"
                                          onsubmit="return confirm('Hapus postingan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $postingan])
        @endif
    </div>
</div>
@endsection
