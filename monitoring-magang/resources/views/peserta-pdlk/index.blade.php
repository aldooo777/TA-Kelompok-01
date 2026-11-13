@extends('layouts.pdlk')

@section('title', 'Mahasiswa Aktif PDLK | Monitoring PDLK')
@section('breadcrumb', 'Mahasiswa Aktif PDLK')
@section('page-title', 'Mahasiswa Aktif PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Data Mahasiswa PDLK</h2>
                <p class="p-card-subtitle">
                    Mahasiswa masuk ke daftar ini setelah lulus seleksi lowongan, atau setelah didata untuk PDLK mandiri.
                    Tidak ada pembatalan; status <strong>Tidak Aktif</strong> hanya untuk force majeure.
                </p>
            </div>
        </div>

        <form method="GET" class="p-toolbar">
            <div class="p-field">
                <label for="prodi">Program studi</label>
                <select id="prodi" name="prodi" class="p-select">
                    <option value="">Semua prodi</option>
                    @foreach ($prodiList as $pr)
                        <option value="{{ $pr->id }}" @selected((int) request('prodi') === $pr->id)>{{ $pr->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="jenis">Bentuk kegiatan</label>
                <select id="jenis" name="jenis" class="p-select">
                    <option value="">Semua bentuk</option>
                    @foreach ($jenisList as $j)
                        <option value="{{ $j->id }}" @selected((int) request('jenis') === $j->id)>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="status">Status</label>
                <select id="status" name="status" class="p-select">
                    <option value="">Semua status</option>
                    @foreach (\App\Models\PesertaPdlk::STATUS as $kode => $label)
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

        @if ($peserta->isEmpty())
            <div class="p-empty">Tidak ada data mahasiswa yang sesuai.</div>
        @else
            <p class="p-muted" style="margin:0 0 10px">Total: {{ $peserta->total() }} mahasiswa</p>
            <div class="p-table-wrap">
                <table class="p-table">
                    <thead>
                        <tr><th>Mahasiswa</th><th>Kegiatan</th><th>Sumber</th><th>Status</th>@if ($bolehUbahStatus)<th>Aksi</th>@endif</tr>
                    </thead>
                    <tbody>
                        @foreach ($peserta as $ps)
                            <tr>
                                <td><strong>{{ $ps->nama }}</strong><span class="p-muted">{{ $ps->nim }} · {{ $ps->programStudi->label ?? 'Prodi belum diisi' }}</span></td>
                                <td>{{ $ps->jenisKegiatan->nama ?? '-' }}<span class="p-muted">{{ $ps->mitra->nama ?? '-' }}@if ($ps->posisi) · {{ $ps->posisi }}@endif</span></td>
                                <td>{{ $ps->sumber === 'lowongan' ? 'Lowongan UPT' : 'Mandiri' }}</td>
                                <td>
                                    <span class="p-badge p-badge-{{ $ps->status }}">{{ $ps->status_label }}</span>
                                    @if ($ps->status === 'tidak_aktif' && $ps->alasan_tidak_aktif)
                                        <span class="p-muted">{{ $ps->alasan_tidak_aktif }}</span>
                                    @endif
                                </td>
                                @if ($bolehUbahStatus)
                                    <td style="min-width:220px">
                                        @if ($ps->status === 'aktif')
                                            <details>
                                                <summary class="p-btn p-btn-danger p-btn-sm">Tidak Aktif (force majeure)</summary>
                                                <form method="POST" action="{{ route('peserta.nonaktifkan', $ps) }}" style="margin-top:8px">
                                                    @csrf @method('PATCH')
                                                    <textarea name="alasan" class="p-textarea" rows="2" style="min-height:60px" required
                                                              placeholder="Alasan, mis. sakit (wajib)"></textarea>
                                                    <button class="p-btn p-btn-danger p-btn-sm" style="margin-top:6px"
                                                            onclick="return confirm('Ubah status {{ $ps->nama }} menjadi Tidak Aktif?')">Simpan</button>
                                                </form>
                                            </details>
                                        @else
                                            <span class="p-muted">—</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('partials.paginasi', ['paginator' => $peserta])
        @endif
    </div>
</div>
@endsection
