@extends('layouts.pdlk')

@section('title', 'Atur Peran | Monitoring PDLK')
@section('breadcrumb', 'Pengguna & Peran')
@section('page-title', 'Atur Peran Pengguna')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <dl class="p-dl">
            <dt>Nama</dt><dd>{{ $user->name }}</dd>
            <dt>Username CIS</dt><dd>{{ $user->username }}</dd>
            <dt>Role CIS</dt><dd>{{ $user->cis_role ?? '-' }}</dd>
            @if ($user->nim)<dt>NIM</dt><dd>{{ $user->nim }}</dd>@endif
            @if (jabatan_cis($user->cis_position) !== '')<dt>Jabatan CIS</dt><dd>{{ jabatan_cis($user->cis_position) }}</dd>@endif
        </dl>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Peran Saat Ini</h2>
        @if (strcasecmp((string) $user->cis_role, 'Mahasiswa') === 0)
            <p class="p-card-subtitle">Mahasiswa (otomatis dari CIS)</p>
        @endif
        @if ($peranUser->isEmpty())
            <p class="p-muted" style="margin-top:10px">Belum ada peran tambahan.</p>
        @else
            <div class="p-table-wrap" style="margin-top:14px">
                <table class="p-table">
                    <thead><tr><th>Peran</th><th>Cakupan prodi</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($peranUser as $pr)
                            <tr>
                                <td><strong>{{ $pr->peran }}</strong></td>
                                <td>{{ $pr->prodi_code ? $pr->prodi_code . ' – ' . $pr->prodi_name : 'Seluruh institusi' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('kelola.pengguna.peran.destroy', [$user->id, $pr->id]) }}"
                                          onsubmit="return confirm('Cabut peran ini?')">
                                        @csrf @method('DELETE')
                                        <button class="p-btn p-btn-danger p-btn-sm">Cabut</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('kelola.pengguna.peran.store', $user->id) }}" class="p-card">
        @csrf
        <h2 class="p-card-title">Tambah Peran</h2>
        <div class="p-form-grid" style="margin-top:14px">
            <div class="p-field">
                <label for="role_id">Peran <span class="p-required">*</span></label>
                <select id="role_id" name="role_id" class="p-select" required>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}" data-code="{{ $r->code }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="study_program_id">Program studi <span class="p-optional">(wajib untuk koordinator)</span></label>
                <select id="study_program_id" name="study_program_id" class="p-select">
                    <option value="">— Seluruh institusi —</option>
                    @foreach ($prodiList as $p)
                        <option value="{{ $p->id }}">{{ $p->level }} {{ $p->name }}</option>
                    @endforeach
                </select>
                @error('study_program_id')<p class="p-error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="p-form-actions">
            <a href="{{ route('kelola.pengguna.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">Tambah Peran</button>
        </div>
    </form>
</div>
@endsection
