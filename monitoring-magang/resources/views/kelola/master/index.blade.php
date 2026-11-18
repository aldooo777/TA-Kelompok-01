@extends('layouts.pdlk')

@section('title', $def['title'] . ' | Monitoring PDLK')
@section('breadcrumb', 'Data Master / ' . $def['title'])
@section('page-title', $def['title'])

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar {{ $def['title'] }}</h2>
                <p class="p-card-subtitle">
                    {{ $def['note'] ?? 'Data yang dinonaktifkan tidak tampil di pilihan form dan halaman publik, tetapi tetap tersimpan.' }}
                </p>
            </div>
            @unless ($def['no_create'] ?? false)
                <a href="{{ route('kelola.master.create', $def['key']) }}" class="p-btn p-btn-primary">+ Tambah</a>
            @endunless
        </div>

        <form method="GET" class="p-toolbar">
            <div class="p-field">
                <label for="q">Cari</label>
                <input id="q" name="q" class="p-input" value="{{ request('q') }}">
            </div>
            <div class="p-field" style="max-width:200px">
                <label for="status">Tampilkan</label>
                <select id="status" name="status" class="p-select">
                    <option value="">Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                    <option value="semua" @selected(request('status') === 'semua')>Semua</option>
                </select>
            </div>
            <button class="p-btn p-btn-primary">Terapkan</button>
        </form>

        @if ($rows->isEmpty())
            <div class="p-empty">Belum ada data.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr>
                            @foreach ($def['list'] as $kolom)
                                <th>{{ $def['fields'][$kolom]['label'] }}</th>
                            @endforeach
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                @foreach ($def['list'] as $i => $kolom)
                                    <td>
                                        @if ($i === 0 || $kolom === 'name')
                                            <strong>{{ \App\Support\MasterData::tampil($def['fields'][$kolom], $row->$kolom) }}</strong>
                                        @else
                                            {{ \App\Support\MasterData::tampil($def['fields'][$kolom], $row->$kolom) }}
                                        @endif
                                    </td>
                                @endforeach
                                <td>
                                    <span class="p-badge {{ $row->is_active ? 'p-badge-active' : 'p-badge-inactive' }}">
                                        {{ $row->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td style="white-space:nowrap">
                                    <a href="{{ route('kelola.master.edit', [$def['key'], $row->id]) }}" class="p-btn p-btn-light p-btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('kelola.master.toggle', [$def['key'], $row->id]) }}" style="display:inline">
                                        @csrf @method('PATCH')
                                        <button class="p-btn p-btn-sm {{ $row->is_active ? 'p-btn-danger' : 'p-btn-light' }}">
                                            {{ $row->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $rows])
        @endif
    </div>
</div>
@endsection
