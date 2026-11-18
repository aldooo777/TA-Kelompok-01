@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Kuesioner Monev | Monitoring PDLK')
@section('breadcrumb', 'Monev / Kuesioner')
@section('page-title', 'Kuesioner Monev')

@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Kuesioner</h2>
                <p class="p-card-subtitle">Buat kuesioner Monev Tengah dan Monev Akhir, lalu atur jadwalnya per periode di menu <a href="{{ route('kelola.jadwal-monev.index') }}">Jadwal Monev</a>.</p>
            </div>
            <a href="{{ route('kelola.kuesioner.create') }}" class="p-btn p-btn-primary">+ Buat Kuesioner</a>
        </div>

        @if ($kuesioner->isEmpty())
            <div class="p-empty">Belum ada kuesioner.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Jenis</th><th>Judul</th><th>Pertanyaan</th><th>Dijadwalkan</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($kuesioner as $q)
                            <tr>
                                <td>{{ Label::of(Label::MONEV_TYPE, $q->type) }}</td>
                                <td><strong>{{ $q->title }}</strong><span class="p-muted">{{ ringkas($q->description, 90) }}</span></td>
                                <td>{{ $q->jumlah_pertanyaan }}</td>
                                <td>{{ $q->jumlah_jadwal }} periode</td>
                                <td><span class="p-badge {{ $q->is_active ? 'p-badge-active' : 'p-badge-inactive' }}">{{ $q->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.kuesioner.edit', $q->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah & Pertanyaan</a>
                                    <form method="POST" action="{{ route('kelola.kuesioner.toggle', $q->id) }}" style="display:inline">
                                        @csrf @method('PATCH')
                                        <button class="p-btn p-btn-sm {{ $q->is_active ? 'p-btn-danger' : 'p-btn-light' }}">{{ $q->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
