@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Jadwal Monev | Monitoring PDLK')
@section('breadcrumb', 'Monev / Jadwal')
@section('page-title', 'Jadwal Monev')

@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Jadwal per Periode</h2>
                <p class="p-card-subtitle">
                    Monev tengah dibuka minggu ke-8 sampai ke-9, lalu monev akhir. Selama jadwal dibuka dan belum dikirim,
                    dashboard mahasiswa aktif pada periode tersebut <strong>terkunci</strong> sampai monev diisi.
                </p>
            </div>
            <a href="{{ route('kelola.jadwal-monev.create') }}" class="p-btn p-btn-primary">+ Tambah Jadwal</a>
        </div>

        @if ($jadwal->isEmpty())
            <div class="p-empty">Belum ada jadwal monev.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Kuesioner</th><th>Periode</th><th>Dibuka</th><th>Tenggat</th><th>Terkirim</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($jadwal as $s)
                            @php
                                $sekarang = now()->toDateTimeString();
                                $status = $s->is_closed ? ['Ditutup', 'p-badge-ditutup'] : ($s->open_at > $sekarang ? ['Belum dibuka', 'p-badge-draft'] : ($s->deadline_at < $sekarang ? ['Lewat tenggat', 'p-badge-processing'] : ['Dibuka', 'p-badge-dibuka']));
                            @endphp
                            <tr>
                                <td><strong>{{ Label::of(Label::MONEV_TYPE, $s->type) }}</strong><span class="p-muted">{{ $s->title }}</span></td>
                                <td>{{ $s->period_name }}</td>
                                <td>{{ tgl($s->open_at, 'd/m/Y H:i') }}</td>
                                <td>{{ tgl($s->deadline_at, 'd/m/Y H:i') }}</td>
                                <td><a href="{{ route('rekap-monev.index', ['jadwal' => $s->id]) }}">{{ $s->jumlah_terkirim }} / {{ $s->jumlah_peserta }}</a></td>
                                <td><span class="p-badge {{ $status[1] }}">{{ $status[0] }}</span></td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.jadwal-monev.edit', $s->id) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.jadwal-monev.destroy', $s->id) }}" style="display:inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Hapus</button>
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
