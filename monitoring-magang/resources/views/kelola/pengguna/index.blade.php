@extends('layouts.pdlk')

@section('title', 'Pengguna & Peran | Monitoring PDLK')
@section('breadcrumb', 'Pengguna & Peran')
@section('page-title', 'Pengguna & Peran')

@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Pengguna CIS</h2>
                <p class="p-card-subtitle">
                    Akun tercatat otomatis saat pertama kali login. Pilih <strong>Atur Peran</strong> untuk memberi peran
                    Staf UPT atau koordinator. Peran Mahasiswa dikenali otomatis dari CIS.
                </p>
            </div>
        </div>

        <form method="GET" class="p-toolbar">
            <div class="p-field">
                <label for="q">Cari</label>
                <input id="q" name="q" class="p-input" value="{{ request('q') }}" placeholder="Nama, username, atau NIM">
            </div>
            <button class="p-btn p-btn-primary">Cari</button>
        </form>

        @if ($pengguna->isEmpty())
            <div class="p-empty">Belum ada pengguna.</div>
        @else
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Pengguna</th><th>Role CIS</th><th>Peran PDLK</th><th>Login terakhir</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($pengguna as $u)
                            <tr>
                                <td><strong>{{ $u->name }}</strong><span class="p-muted">{{ $u->username }}@if ($u->nim) · {{ $u->nim }}@endif @if ($u->prodi_code) · {{ $u->prodi_code }}@endif</span></td>
                                <td>{{ $u->cis_role ?? '-' }}</td>
                                <td>{{ $u->daftar_peran ?: '—' }}</td>
                                <td>{{ tgl($u->last_login_at, 'd/m/Y H:i') ?: 'Belum pernah' }}</td>
                                <td><a href="{{ route('kelola.pengguna.edit', $u->id) }}" class="p-btn p-btn-light p-btn-sm">Atur Peran</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $pengguna])
        @endif
    </div>
</div>
@endsection
