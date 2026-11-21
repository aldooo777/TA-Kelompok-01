@extends('layouts.pdlk')

@section('title', 'Pengguna & Peran | Sistem Informasi PDLK')
@section('breadcrumb', 'Pengguna / Pengguna & Peran')
@section('page-title', 'Pengguna & Peran')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <h2 class="p-card-title">Pengguna CIS</h2>
        <p class="p-card-subtitle">
            Akun tercatat otomatis saat pertama kali login. Pilih <strong>Atur Peran</strong> untuk memberi peran Staf UPT
            atau koordinator. Peran Mahasiswa dikenali otomatis dari akun CIS atau NIM.
        </p>

        <form method="GET" class="p-toolbar" style="margin-top:16px">
            <div class="p-field">
                <label for="q">Cari</label>
                <input id="q" name="q" class="p-input" value="{{ $q ?? request('q') }}" placeholder="Nama, username, atau NIM">
            </div>
            <button class="p-btn p-btn-primary">Cari</button>
        </form>

        <div class="p-table-wrap">
            <table class="p-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role CIS</th>
                        <th>Peran PDLK</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengguna as $u)
                        <tr>
                            <td>
                                <strong>{{ $u->name }}</strong>
                                <div class="p-muted">{{ $u->username }}@if ($u->nim) · {{ $u->nim }}@endif</div>
                            </td>
                            <td>{{ $u->cis_role ?? '-' }}</td>
                            <td>{{ $u->daftar_peran ?: '—' }}</td>
                            <td><a href="{{ route('kelola.pengguna.edit', $u->id) }}" class="p-btn p-btn-light p-btn-sm">Atur Peran</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-empty">Belum ada pengguna yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.paginasi', ['paginator' => $pengguna])
    </div>
</div>
@endsection
