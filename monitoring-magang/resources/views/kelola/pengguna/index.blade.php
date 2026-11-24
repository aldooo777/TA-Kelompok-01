@extends('layouts.pdlk')

@section('title', 'Pengguna & Peran | Sistem Informasi PDLK')
@section('breadcrumb', 'Pengguna / Pengguna & Peran')
@section('page-title', 'Pengguna & Peran')

@section('content')
<div class="p-stack">
    @include('partials.flash')
        <div class="p-card">
        <h2 class="p-card-title">Tambah dari CIS</h2>
        <p class="p-card-subtitle">Daftarkan dosen atau mahasiswa yang belum pernah login ke sistem ini.</p>

        <form method="GET" class="p-toolbar" style="margin-top:16px">
            <div class="p-field">
                <label for="jenis">Jenis</label>
                <select id="jenis" name="jenis" class="p-input">
                    <option value="dosen" @selected($jenis === 'dosen')>Dosen (cari nama)</option>
                    <option value="mahasiswa" @selected($jenis === 'mahasiswa')>Mahasiswa (cari NIM)</option>
                </select>
            </div>
            <div class="p-field">
                <label for="kata">Nama / NIM</label>
                <input id="kata" name="kata" class="p-input" value="{{ $kata }}" placeholder="Minimal 3 karakter" minlength="3" required>
            </div>
            <button class="p-btn p-btn-primary">Cari di CIS</button>
        </form>

        @if ($errorCis)
            <div class="p-alert p-alert-warning" style="margin-top:12px">{{ $errorCis }}</div>
        @elseif ($kata !== '' && $hasilCis->isEmpty())
            <p class="p-muted" style="margin-top:12px">Tidak ada data di CIS.</p>
        @endif

        @if ($hasilCis->isNotEmpty())
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead><tr><th>Nama</th><th>Prodi</th><th>Email</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($hasilCis as $h)
                            <tr>
                                <td>
                                    <strong>{{ $h['nama'] ?? '-' }}</strong>
                                    @if ($jenis === 'mahasiswa')<div class="p-muted">{{ $h['nim'] }}</div>@endif
                                </td>
                                <td>{{ $h['prodi'] ?? '-' }}</td>
                                <td>{{ $h['email'] ?? '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('kelola.pengguna.dari-cis') }}">
                                        @csrf
                                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                                        <input type="hidden" name="kata" value="{{ $kata }}">
                                        <input type="hidden" name="id" value="{{ $jenis === 'dosen' ? ($h['user_id'] ?? '') : ($h['nim'] ?? '') }}">
                                        <button class="p-btn p-btn-primary p-btn-sm">Tambah</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Pengguna CIS</h2>
        <p class="p-card-subtitle">
            Akun tercatat saat pertama kali login atau ditambahkan dari CIS. Pilih <strong>Atur Peran</strong> untuk memberi peran Staf UPT
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
