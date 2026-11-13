@extends('layouts.pdlk')

@section('title', 'Kelola Lowongan | Monitoring PDLK')
@section('breadcrumb', 'Kelola Lowongan')
@section('page-title', 'Lowongan PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Lowongan</h2>
                <p class="p-card-subtitle">Lowongan yang diterbitkan tampil di halaman publik beserta kriterianya.</p>
            </div>
            <a href="{{ route('kelola.lowongan.create') }}" class="p-btn p-btn-primary">+ Tambah Lowongan</a>
        </div>

        @if ($lowongan->isEmpty())
            <div class="p-empty">Belum ada lowongan.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Lowongan</th><th>Bentuk</th><th>Batas Daftar</th><th>Status</th><th>Pendaftar</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($lowongan as $item)
                            <tr>
                                <td><strong>{{ $item->judul }}</strong><span class="p-muted">{{ $item->mitra->nama ?? '-' }}</span></td>
                                <td>{{ $item->jenisKegiatan->nama ?? '-' }}</td>
                                <td>{{ $item->tanggal_tutup?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    @if (!$item->is_published)
                                        <span class="p-badge p-badge-draft">Disembunyikan</span>
                                    @elseif ($item->dibuka)
                                        <span class="p-badge p-badge-dibuka">Dibuka</span>
                                    @else
                                        <span class="p-badge p-badge-ditutup">Ditutup</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kelola.pendaftaran.index', ['lowongan' => $item->id]) }}">
                                        <strong>{{ $item->pendaftaran_count }}</strong> pendaftar
                                    </a>
                                    @if ($item->pendaftar_baru_count)
                                        <span class="p-muted">{{ $item->pendaftar_baru_count }} belum diproses</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.lowongan.edit', $item) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.lowongan.destroy', $item) }}" style="display:inline"
                                          onsubmit="return confirm('Hapus lowongan ini? Jika sudah ada pendaftar, lowongan hanya disembunyikan.')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $lowongan])
        @endif
    </div>
</div>
@endsection
