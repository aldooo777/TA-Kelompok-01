@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Pengumuman Hasil | Monitoring PDLK')
@section('breadcrumb', 'Informasi / Pengumuman Hasil')
@section('page-title', 'Pengumuman Hasil Seleksi')

@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Pengumuman Hasil</h2>
                <p class="p-card-subtitle">Umumkan mahasiswa yang lulus seleksi mitra. Untuk lowongan internal, daftar nama bisa diisi otomatis dari pendaftar berstatus Lulus.</p>
            </div>
            <a href="{{ route('kelola.hasil.create') }}" class="p-btn p-btn-primary">+ Buat Pengumuman Hasil</a>
        </div>
        @if ($hasil->isEmpty())
            <div class="p-empty">Belum ada pengumuman hasil.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Judul</th><th>Lowongan asal</th><th>Jumlah lulus</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($hasil as $h)
                            <tr>
                                <td><strong>{{ $h->title }}</strong></td>
                                <td>{{ $h->lowongan_title ?? '-' }}</td>
                                <td>{{ $h->jumlah }}</td>
                                <td><span class="p-badge p-badge-{{ $h->status }}">{{ Label::of(Label::ANNOUNCEMENT_STATUS, $h->status) }}</span></td>
                                <td style="white-space:nowrap">
                                    @if ($h->status === 'published')<a href="{{ route('hasil.show', $h->slug) }}" target="_blank" class="p-btn p-btn-light p-btn-sm">Lihat</a>@endif
                                    <a href="{{ route('kelola.hasil.edit', $h->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.hasil.destroy', $h->id) }}" style="display:inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf @method('DELETE')<button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $hasil])
        @endif
    </div>
</div>
@endsection
