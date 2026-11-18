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
                <p class="p-card-subtitle">Lowongan yang terbit tampil di halaman publik beserta kriterianya.</p>
            </div>
            <a href="{{ route('kelola.lowongan.create') }}" class="p-btn p-btn-primary">+ Tambah Lowongan</a>
        </div>

        @if ($lowongan->isEmpty())
            <div class="p-empty">Belum ada lowongan.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Lowongan</th><th>Bentuk</th><th>Pendaftaran</th><th>Batas Daftar</th><th>Status</th><th>Pendaftar</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($lowongan as $item)
                            <tr>
                                <td><strong>{{ $item->title }}</strong><span class="p-muted">{{ $item->partner_name }}</span></td>
                                <td>{{ $item->activity_name }}</td>
                                <td>{{ \App\Support\Label::of(\App\Support\Label::REGISTRATION_TYPE, $item->registration_type) }}</td>
                                <td>{{ tgl($item->registration_deadline, 'd/m/Y') ?: '—' }}</td>
                                <td>
                                    @if ($item->status !== 'published')
                                        <span class="p-badge p-badge-draft">Draf</span>
                                    @elseif (lowongan_dibuka($item))
                                        <span class="p-badge p-badge-dibuka">Dibuka</span>
                                    @else
                                        <span class="p-badge p-badge-ditutup">Ditutup</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->registration_type === 'external')
                                        <span class="p-muted">—</span>
                                    @elseif (!sprint(2))
                                        <strong>{{ $item->jumlah_pendaftar }}</strong> pendaftar
                                    @else
                                        <a href="{{ route('kelola.pendaftaran.index', ['lowongan' => $item->id]) }}">
                                            <strong>{{ $item->jumlah_pendaftar }}</strong> pendaftar
                                        </a>
                                        @if ($item->jumlah_baru)
                                            <span class="p-muted">{{ $item->jumlah_baru }} belum diproses</span>
                                        @endif
                                    @endif
                                </td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.lowongan.edit', $item->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.lowongan.destroy', $item->id) }}" style="display:inline"
                                          onsubmit="return confirm('Arsipkan lowongan ini? Lowongan tidak tampil lagi di halaman publik.')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Arsipkan</button>
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
