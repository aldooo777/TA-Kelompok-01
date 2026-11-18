@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Rekap Monev | Monitoring PDLK')
@section('breadcrumb', 'Monev / Rekap')
@section('page-title', 'Rekap Monev')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    @if (!$jadwal)
        <div class="p-card p-empty">Belum ada jadwal monev.</div>
    @else
        <form method="GET" class="p-card">
            <div class="p-toolbar" style="margin:0">
                <div class="p-field">
                    <label for="jadwal">Jadwal monev</label>
                    <select id="jadwal" name="jadwal" class="p-select">
                        @foreach ($jadwalList as $s)
                            <option value="{{ $s->id }}" @selected($s->id === $jadwal->id)>{{ Label::of(Label::MONEV_TYPE, $s->type) }} — {{ $s->period_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-field">
                    <label for="prodi">Program studi</label>
                    <select id="prodi" name="prodi" class="p-select">
                        <option value="">Semua prodi</option>
                        @foreach ($prodiList as $p)
                            <option value="{{ $p->id }}" @selected((int) request('prodi') === (int) $p->id)>{{ $p->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-field" style="max-width:220px">
                    <label for="isi">Tampilkan</label>
                    <select id="isi" name="isi" class="p-select">
                        <option value="">Semua mahasiswa</option>
                        <option value="belum" @selected(request('isi') === 'belum')>Belum mengirim</option>
                    </select>
                </div>
                <button class="p-btn p-btn-primary">Terapkan</button>
                <a href="{{ route('rekap-monev.ekspor', ['jadwalId' => $jadwal->id] + request()->only('prodi')) }}" class="p-btn p-btn-light">⬇ Unduh CSV</a>
            </div>
        </form>

        <div class="p-stats">
            <div class="p-stat"><div class="p-stat-label">Mahasiswa wajib isi</div><div class="p-stat-value">{{ $statistik['total'] }}</div></div>
            <div class="p-stat"><div class="p-stat-label">Sudah mengirim</div><div class="p-stat-value">{{ $statistik['terkirim'] }} <span class="p-muted" style="font-size:15px">({{ $statistik['persen'] }}%)</span></div></div>
            <div class="p-stat"><div class="p-stat-label">Terlambat</div><div class="p-stat-value">{{ $statistik['terlambat'] }}</div></div>
            <div class="p-stat"><div class="p-stat-label">Masih draf</div><div class="p-stat-value">{{ $statistik['draf'] }}</div></div>
            <div class="p-stat"><div class="p-stat-label">Belum mengisi</div><div class="p-stat-value">{{ $statistik['belum'] }}</div></div>
        </div>

        <div class="p-card">
            <h2 class="p-card-title">Per Program Studi</h2>
            <div class="p-table-wrap" style="margin-top:12px">
                <table class="p-table">
                    <thead><tr><th>Prodi</th><th>Wajib isi</th><th>Terkirim</th><th>Persentase</th></tr></thead>
                    <tbody>
                        @forelse ($perProdi as $kode => $r)
                            <tr>
                                <td><strong>{{ $kode }}</strong></td><td>{{ $r['total'] }}</td><td>{{ $r['terkirim'] }}</td>
                                <td>
                                    @php $pct = $r['total'] ? round($r['terkirim'] / $r['total'] * 100) : 0; @endphp
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div style="flex:1;max-width:200px;height:8px;background:#eef1f5;border-radius:4px;overflow:hidden">
                                            <div style="width:{{ $pct }}%;height:100%;background:var(--accent)"></div>
                                        </div>{{ $pct }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-empty">Tidak ada mahasiswa pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($rataSkala->isNotEmpty())
            <div class="p-card">
                <h2 class="p-card-title">Rata-rata Jawaban Skala</h2>
                <div class="p-table-wrap" style="margin-top:12px">
                    <table class="p-table">
                        <thead><tr><th>Pertanyaan</th><th>Rata-rata</th><th>Jumlah jawaban</th></tr></thead>
                        <tbody>
                            @foreach ($rataSkala as $r)
                                <tr><td>{{ $r->question_text }}</td><td><strong>{{ $r->rata }}</strong></td><td>{{ $r->n }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="p-card">
            <h2 class="p-card-title">Daftar Mahasiswa</h2>
            <p class="p-card-subtitle">Yang belum mengirim ditampilkan paling atas supaya mudah diingatkan.</p>
            <div class="p-table-wrap" style="margin-top:12px">
                <table class="p-table">
                    <thead><tr><th>Mahasiswa</th><th>Prodi</th><th>Mitra</th><th>Status monev</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse ($baris as $b)
                            <tr>
                                <td><strong>{{ $b->name }}</strong><span class="p-muted">{{ $b->nim }}</span></td>
                                <td>{{ $b->prodi_code ?? '-' }}</td>
                                <td>{{ $b->partner_name ?? '-' }}</td>
                                <td>
                                    @if ($b->response_status === 'submitted')
                                        <span class="p-badge p-badge-passed">Terkirim</span>
                                        <span class="p-muted">{{ tgl($b->submitted_at, 'd/m/Y H:i') }}{{ $b->submitted_at > $jadwal->deadline_at ? ' · terlambat' : '' }}</span>
                                    @elseif ($b->response_status === 'draft')
                                        <span class="p-badge p-badge-processing">Draf</span>
                                    @else
                                        <span class="p-badge p-badge-failed">Belum diisi</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($b->response_id)
                                        <a href="{{ route('rekap-monev.jawaban', $b->response_id) }}" class="p-btn p-btn-light p-btn-sm">Lihat jawaban</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-empty">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
