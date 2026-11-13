@extends('layouts.pdlk')

@section('title', 'Kelola Pendaftar | Monitoring PDLK')
@section('breadcrumb', 'Kelola Pendaftar')
@section('page-title', 'Pendaftar Lowongan')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-stats">
        @foreach (\App\Models\Pendaftaran::STATUS as $kode => $label)
            <a class="p-stat" style="color:inherit" href="{{ route('kelola.pendaftaran.index', array_merge(request()->except('page'), ['status' => $kode])) }}">
                <div class="p-stat-label">{{ $label }}</div>
                <div class="p-stat-value">{{ $statistik[$kode] ?? 0 }}</div>
            </a>
        @endforeach
    </div>

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Daftar Pendaftar</h2>
                <p class="p-card-subtitle">
                    Unduh data untuk dikirim ke mitra, tandai <strong>Proses</strong>, lalu perbarui menjadi
                    <strong>Lulus</strong> atau <strong>Tidak Lulus</strong> sesuai hasil seleksi mitra.
                    Pendaftar yang lulus otomatis masuk ke daftar mahasiswa aktif.
                </p>
            </div>
            <a href="{{ route('kelola.pendaftaran.ekspor', request()->query()) }}" class="p-btn p-btn-light">⬇ Unduh Spreadsheet</a>
        </div>

        <form method="GET" class="p-toolbar">
            <div class="p-field">
                <label for="lowongan">Lowongan</label>
                <select id="lowongan" name="lowongan" class="p-select">
                    <option value="">Semua lowongan</option>
                    @foreach ($lowonganList as $l)
                        <option value="{{ $l->id }}" @selected((int) request('lowongan') === $l->id)>{{ $l->judul }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="status">Status</label>
                <select id="status" name="status" class="p-select">
                    <option value="">Semua status</option>
                    @foreach (\App\Models\Pendaftaran::STATUS as $kode => $label)
                        <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="q">Cari</label>
                <input id="q" name="q" class="p-input" value="{{ request('q') }}" placeholder="Nama atau NIM">
            </div>
            <button class="p-btn p-btn-primary">Terapkan</button>
        </form>

        @if ($pendaftaran->isEmpty())
            <div class="p-empty">Belum ada pendaftar.</div>
        @else
            <form method="POST" action="{{ route('kelola.pendaftaran.status-massal') }}" id="formMassal">
                @csrf @method('PATCH')
                <div class="p-toolbar" style="margin-bottom:10px">
                    <span class="p-muted">Untuk yang dicentang:</span>
                    <select name="status" class="p-select" style="max-width:200px">
                        @foreach (\App\Models\Pendaftaran::STATUS as $kode => $label)
                            <option value="{{ $kode }}" @selected($kode === 'proses')>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="p-btn p-btn-light p-btn-sm">Terapkan ke yang dipilih</button>
                </div>
            </form>

            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="pilihSemua" aria-label="Pilih semua"></th>
                            <th>Mahasiswa</th><th>Lowongan</th><th>Semester / IPK</th><th>Kontak</th><th>Dokumen</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $p)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{ $p->id }}" form="formMassal" class="pilih"></td>
                                <td>
                                    <strong>{{ $p->nama }}</strong>
                                    <span class="p-muted">{{ $p->nim }} · {{ $p->programStudi->label ?? '-' }}</span>
                                    @foreach ($p->jawaban_tambahan ?? [] as $label => $jawab)
                                        <span class="p-muted">{{ $label }}: {{ $jawab }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $p->lowongan->judul }}<span class="p-muted">{{ $p->lowongan->mitra->nama ?? '' }}</span></td>
                                <td>Smt {{ $p->semester }}<span class="p-muted">IPK {{ number_format((float) $p->ipk, 2) }}</span></td>
                                <td>{{ $p->no_hp }}<span class="p-muted">{{ $p->email }}</span></td>
                                <td><a href="{{ route('kelola.pendaftaran.dokumen', $p) }}">PDF</a></td>
                                <td style="min-width:210px">
                                    <form method="POST" action="{{ route('kelola.pendaftaran.status', $p) }}" style="display:flex;gap:6px;flex-wrap:wrap">
                                        @csrf @method('PATCH')
                                        <select name="status" class="p-select" style="min-height:32px;padding:4px 8px;max-width:130px">
                                            @foreach (\App\Models\Pendaftaran::STATUS as $kode => $label)
                                                <option value="{{ $kode }}" @selected($p->status === $kode)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="p-btn p-btn-light p-btn-sm">Simpan</button>
                                    </form>
                                    <span class="p-badge p-badge-{{ $p->status }}" style="margin-top:6px">{{ $p->status_label }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $pendaftaran])
        @endif
    </div>
</div>

<script>
    document.getElementById('pilihSemua')?.addEventListener('change', function () {
        document.querySelectorAll('.pilih').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
