@extends('layouts.publik')

@section('title', 'Rekapitulasi Data PDLK')

@push('styles')
<style>
    @media print {
        header, footer, #filter-rekap-box, .no-print { display: none !important; }
        .pt-\[116px\] { padding-top: 0 !important; }
    }
</style>
@endpush

@section('fullwidth')
@php
    $tahun = $tahun ?? '';
    $semester = $semester ?? '';
    $daftarTahun = $daftarTahun ?? collect();
    $daftarSemester = $daftarSemester ?? collect();
    $ringkasan = $ringkasan ?? (object) ['total' => 0, 'flagship' => 0, 'mandiri' => 0, 'mitra_terlibat' => 0, 'prodi_terlibat' => 0];
    $perJenis = $perJenis ?? collect();
    $trenPeriode = $trenPeriode ?? collect();
    $prodiList = $prodiList ?? collect();
    $jenisList = $jenisList ?? collect();
    $matriksProdi = $matriksProdi ?? [];
    $topMitra = $topMitra ?? collect();

    $totalPeserta = (int) ($ringkasan->total ?? 0);
    $totalFlagship = (int) ($ringkasan->flagship ?? 0);
    $totalMandiri = (int) ($ringkasan->mandiri ?? 0);
    $persenFlagship = $totalPeserta > 0 ? round(($totalFlagship / $totalPeserta) * 100, 1) : 0;
    $persenMandiri = $totalPeserta > 0 ? round(($totalMandiri / $totalPeserta) * 100, 1) : 0;

    $jenisTerbanyak = $perJenis->first();
    $persenJenisTerbanyak = ($jenisTerbanyak && $totalPeserta > 0)
        ? round(($jenisTerbanyak->total / $totalPeserta) * 100, 1)
        : 0;

    $periodePuncak = $trenPeriode->sortByDesc('total')->first();
    $totalTren = (int) $trenPeriode->sum('total');

    $labelPeriodeTeks = trim(
        ($semester !== '' ? ucfirst($semester) . ' ' : '') . ($tahun !== '' ? $tahun : '')
    ) ?: 'Semua Periode Akademik';

    $maxMitra = max(1, (int) ($topMitra->max('total') ?? 0));
@endphp

<div class="flex flex-col w-full">

    {{-- 1. HERO BANNER --}}
    <section class="relative w-full overflow-hidden bg-gradient-to-r from-[#003580] to-[#0055A5] pt-8 pb-16 md:pb-20">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <nav class="flex items-center gap-2 text-sm text-[#8cc5dd] mb-3">
                <a class="hover:underline text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                <span>/</span>
                <span class="text-white font-medium">Rekapitulasi Data</span>
            </nav>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#46166c] text-white text-xs font-semibold uppercase tracking-wider mb-2 shadow-sm">
                <i class="bi bi-bar-chart-line-fill text-xs"></i>
                <span>DATA PDLK</span>
            </div>

            <h1 class="text-white font-bold text-3xl md:text-4xl tracking-tight mt-1 mb-2">
                Rekapitulasi Data PDLK
            </h1>
            <p class="text-blue-100 text-sm md:text-base max-w-3xl leading-relaxed">
                Statistik partisipasi mahasiswa, tren antar-semester, serta persebaran peserta di berbagai mitra dan program studi Institut Teknologi Del.
            </p>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-1 mt-6">
            <svg class="w-full h-10 md:h-14 block" preserveAspectRatio="none" viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,24 Q360,56 720,24 T1440,24 L1440,60 L0,60 Z" fill="#ffffff"></path>
            </svg>
        </div>
    </section>

    {{-- 2. KARTU FILTER --}}
    <div id="filter-rekap-box" class="relative z-20 max-w-7xl mx-auto px-margin-mobile md:px-margin -mt-8 md:-mt-10 w-full">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-5 md:p-6">
            <form method="GET" action="{{ route('rekapitulasi') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide" for="filter-tahun">Tahun Akademik:</label>
                        <select name="tahun" id="filter-tahun"
                                class="border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-[#003580] font-medium bg-surface-container-low focus:outline-none focus:ring-1 focus:ring-[#1a81c2]">
                            <option value="">Semua Tahun</option>
                            @foreach ($daftarTahun as $dt)
                                <option value="{{ $dt->academic_year }}" @selected($tahun === $dt->academic_year)>{{ $dt->academic_year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide" for="filter-semester">Semester:</label>
                        <select name="semester" id="filter-semester"
                                class="border border-slate-200 rounded-lg px-3.5 py-2 text-sm text-[#003580] font-medium bg-surface-container-low focus:outline-none focus:ring-1 focus:ring-[#1a81c2]">
                            <option value="">Semua Semester</option>
                            @foreach ($daftarSemester as $ds)
                                <option value="{{ $ds->semester }}" @selected(strtolower($semester) === strtolower($ds->semester))>{{ $ds->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="bg-[#003580] hover:bg-[#00173d] text-white px-5 py-2 rounded-lg font-medium text-sm inline-flex items-center gap-2 transition-colors shadow-sm">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Tampilkan</span>
                    </button>

                    @if ($tahun !== '' || $semester !== '')
                        <a href="{{ route('rekapitulasi') }}" class="text-xs font-semibold text-[#46166c] hover:underline ml-1">Reset</a>
                    @endif
                </div>

                <div>
                    <button type="button" onclick="window.print()"
                            class="w-full md:w-auto border border-[#003580] text-[#003580] hover:bg-[#003580] hover:text-white px-4 py-2 rounded-lg font-medium text-sm inline-flex items-center justify-center gap-2 transition-all shadow-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>Cetak / Unduh PDF</span>
                    </button>
                </div>
            </form>

            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs text-slate-500">
                <i class="bi bi-info-circle text-[#1a81c2]"></i>
                <span>Menampilkan data periode: <strong class="text-[#003580]">{{ $labelPeriodeTeks }}</strong></span>
            </div>
        </div>
    </div>

    {{-- 3. KARTU STATISTIK (5 KARTU) --}}
    <section class="w-full bg-white py-8">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-5">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 border-t-4 border-[#1a81c2] flex flex-col justify-between transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Peserta</span>
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-[#1a81c2] flex items-center justify-center text-lg">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-[#003580] leading-none mb-1">{{ $totalPeserta }}</div>
                        <div class="text-xs text-slate-500">Peserta Aktif &amp; Selesai</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 border-t-4 border-[#46166c] flex flex-col justify-between transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">PDLK Flagship</span>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-[#46166c] flex items-center justify-center text-lg">
                            <i class="bi bi-award-fill"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-[#46166c] leading-none mb-1">{{ $totalFlagship }}</div>
                        <div class="text-xs text-slate-500">Skema Nasional ({{ $persenFlagship }}%)</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 border-t-4 border-[#1a81c2] flex flex-col justify-between transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">PDLK Mandiri</span>
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-[#1a81c2] flex items-center justify-center text-lg">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-[#003580] leading-none mb-1">{{ $totalMandiri }}</div>
                        <div class="text-xs text-slate-500">Kemitraan Institut ({{ $persenMandiri }}%)</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 border-t-4 border-[#46166c] flex flex-col justify-between transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mitra Terlibat</span>
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-[#46166c] flex items-center justify-center text-lg">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-[#46166c] leading-none mb-1">{{ $ringkasan->mitra_terlibat ?? 0 }}</div>
                        <div class="text-xs text-slate-500">Mitra Penempatan Peserta</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 border-t-4 border-[#1a81c2] flex flex-col justify-between transition-transform hover:-translate-y-1 col-span-2 md:col-span-1">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Program Studi</span>
                        <div class="w-10 h-10 rounded-lg bg-sky-50 text-[#1a81c2] flex items-center justify-center text-lg">
                            <i class="bi bi-bank"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-[#003580] leading-none mb-1">{{ $ringkasan->prodi_terlibat ?? 0 }}</div>
                        <div class="text-xs text-slate-500">dari {{ $prodiList->count() }} Prodi Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. SECTION GRAFIK UTAMA --}}
    <section class="w-full bg-[#dce3eb] py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="mb-8">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">DISTRIBUSI DATA</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Komposisi Kegiatan &amp; Skema Program</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                {{-- Horizontal Bar Chart --}}
                <div class="lg:col-span-7 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-[#003580] text-lg leading-tight">Peserta per Jenis Kegiatan</h3>
                                <p class="text-xs text-slate-500 mt-1">Distribusi {{ $totalPeserta }} peserta pada {{ $jenisList->count() }} bentuk kegiatan</p>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-sky-50 text-[#1a81c2] text-xs font-semibold">{{ $jenisList->count() }} Kategori</span>
                        </div>
                        <div class="relative w-full h-[280px]">
                            <canvas id="chartKegiatan"></canvas>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        @if ($jenisTerbanyak && $totalPeserta > 0)
                            <span>Kegiatan terbanyak: <strong class="text-[#003580]">{{ $jenisTerbanyak->name }} ({{ $persenJenisTerbanyak }}%)</strong></span>
                        @else
                            <span>Belum ada peserta tercatat pada periode terpilih.</span>
                        @endif
                        <span class="text-[#1a81c2] font-semibold">Data UPT PP-ESTEM</span>
                    </div>
                </div>

                {{-- Donut Chart --}}
                <div class="lg:col-span-5 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-[#003580] text-lg leading-tight">Skema Program</h3>
                                <p class="text-xs text-slate-500 mt-1">Perbandingan Flagship vs Mandiri</p>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-purple-50 text-[#46166c] text-xs font-semibold">Rasio Skema</span>
                        </div>
                        <div class="relative w-full h-[230px] flex items-center justify-center">
                            @if ($totalFlagship + $totalMandiri > 0)
                                <canvas id="chartSkema"></canvas>
                            @else
                                <p class="text-sm text-slate-400 text-center">Belum ada data kategori skema program.</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-slate-100">
                        <div class="p-2.5 rounded-lg bg-sky-50 border border-sky-100 flex flex-col">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="w-3 h-3 rounded-full bg-[#1a81c2]"></span>
                                <span class="text-xs font-semibold text-[#003580]">PDLK Flagship</span>
                            </div>
                            <span class="text-lg font-bold text-[#003580]">{{ $totalFlagship }} <small class="text-xs font-normal text-slate-500">({{ $persenFlagship }}%)</small></span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Program Nasional</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-purple-50 border border-purple-100 flex flex-col">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="w-3 h-3 rounded-full bg-[#46166c]"></span>
                                <span class="text-xs font-semibold text-[#46166c]">PDLK Mandiri</span>
                            </div>
                            <span class="text-lg font-bold text-[#46166c]">{{ $totalMandiri }} <small class="text-xs font-normal text-slate-500">({{ $persenMandiri }}%)</small></span>
                            <span class="text-[11px] text-slate-500 mt-0.5">Kemitraan Institut</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. SECTION TREN ANTAR PERIODE --}}
    <section class="w-full bg-white py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="mb-8">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">HISTORI &amp; PERTUMBUHAN</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Tren Partisipasi Peserta Antar Periode</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
            </div>

            <div class="bg-white p-6 md:p-8 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-bold text-[#003580] text-lg">Jumlah Peserta per Semester ({{ $trenPeriode->count() }} Periode Terakhir)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbandingan peserta Flagship, Mandiri, dan Total per periode akademik</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-[#1a81c2] border border-sky-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1a81c2]"></span> Flagship
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-50 text-[#46166c] border border-purple-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#46166c]"></span> Mandiri
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-[#003580] border border-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8cc5dd]"></span> Garis Total
                        </span>
                    </div>
                </div>

                @if ($trenPeriode->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-12">Belum ada data periode akademik di database.</p>
                @else
                    <div class="relative w-full h-[320px]">
                        <canvas id="chartTrend"></canvas>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-5 border-t border-slate-100 text-center">
                        <div class="p-3 bg-surface-container-low rounded-xl">
                            <div class="text-xs text-slate-500 mb-1 font-medium">Periode Tercatat</div>
                            <div class="text-xl font-bold text-[#003580]">{{ $trenPeriode->count() }} Periode</div>
                            <div class="text-[11px] text-slate-500">Data Periode Aktif</div>
                        </div>
                        <div class="p-3 bg-surface-container-low rounded-xl">
                            <div class="text-xs text-slate-500 mb-1 font-medium">Periode Partisipasi Tertinggi</div>
                            <div class="text-xl font-bold text-[#46166c]">{{ $periodePuncak?->name ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $periodePuncak?->total ?? 0 }} Peserta</div>
                        </div>
                        <div class="p-3 bg-surface-container-low rounded-xl">
                            <div class="text-xs text-slate-500 mb-1 font-medium">Akumulasi {{ $trenPeriode->count() }} Periode</div>
                            <div class="text-xl font-bold text-emerald-700">{{ $totalTren }} Peserta</div>
                            <div class="text-[11px] text-slate-500">Total Tercatat</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 6. SECTION TABEL PER PROGRAM STUDI --}}
    <section class="w-full bg-[#dce3eb] py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">AGREGASI AKADEMIK</span>
                    <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Rekapitulasi Partisipasi per Program Studi</h2>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                </div>
                <div class="text-xs text-slate-600 flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-lg border border-slate-200">
                    <i class="bi bi-info-circle text-[#1a81c2]"></i>
                    <span>Tabel dapat digeser ke samping pada layar kecil</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-[#003580] text-white text-xs font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-left">Program Studi</th>
                                @foreach ($jenisList as $jl)
                                    <th class="py-3.5 px-3 text-center">{{ $jl->name }}</th>
                                @endforeach
                                <th class="py-3.5 px-4 text-center bg-[#002155]">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-on-surface font-medium">
                            @php $totalPerKolom = []; @endphp
                            @forelse ($prodiList as $prodi)
                                @php $totalBaris = 0; @endphp
                                <tr class="{{ $loop->even ? 'bg-slate-50/60 hover:bg-slate-100/70' : 'hover:bg-slate-50' }} transition-colors">
                                    <td class="py-3 px-4 font-semibold text-[#003580] whitespace-nowrap">
                                        {{ trim(($prodi->level ? $prodi->level . ' ' : '') . $prodi->name) }}
                                    </td>
                                    @foreach ($jenisList as $jl)
                                        @php
                                            $jml = (int) ($matriksProdi[$prodi->id][$jl->id] ?? 0);
                                            $totalBaris += $jml;
                                            $totalPerKolom[$jl->id] = ($totalPerKolom[$jl->id] ?? 0) + $jml;
                                        @endphp
                                        <td class="py-3 px-3 text-center {{ $jml === 0 ? 'text-slate-400' : '' }}">{{ $jml }}</td>
                                    @endforeach
                                    <td class="py-3 px-4 text-center font-bold text-[#003580] bg-sky-50/50">{{ $totalBaris }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $jenisList->count() + 2 }}" class="py-8 text-center text-slate-500">
                                        Belum ada data program studi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($prodiList->isNotEmpty())
                            <tfoot>
                                <tr class="font-bold bg-[#8cc5dd]/30 text-[#003580] border-t-2 border-[#003580]">
                                    <td class="py-3.5 px-4 uppercase tracking-wider">TOTAL KESELURUHAN</td>
                                    @php $grandTotal = 0; @endphp
                                    @foreach ($jenisList as $jl)
                                        @php
                                            $colSum = (int) ($totalPerKolom[$jl->id] ?? 0);
                                            $grandTotal += $colSum;
                                        @endphp
                                        <td class="py-3.5 px-3 text-center font-bold text-base">{{ $colSum }}</td>
                                    @endforeach
                                    <td class="py-3.5 px-4 text-center font-bold text-lg text-white bg-[#003580]">{{ $grandTotal }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. SECTION 10 MITRA DENGAN PESERTA TERBANYAK --}}
    <section class="w-full bg-white py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="mb-8">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">KOLABORASI INDUSTRI</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">10 Mitra dengan Partisipasi Peserta Terbanyak</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-6">
                @if ($topMitra->isEmpty())
                    <div class="py-10 text-center text-slate-500 text-sm">
                        Belum ada data partisipasi mitra pada periode terpilih.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($topMitra as $tm)
                            @php
                                $rank = $loop->iteration;
                                $isTop3 = $rank <= 3;
                                $barPct = max(5, round(($tm->total / $maxMitra) * 100));
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-xl bg-slate-50/70 hover:bg-slate-100/80 transition-all border border-slate-100 gap-3">
                                <div class="flex items-center gap-3.5 min-w-[260px]">
                                    <div class="w-8 h-8 rounded-full text-white font-bold flex items-center justify-center text-sm shrink-0 shadow-sm {{ $isTop3 ? 'bg-[#46166c]' : 'bg-[#1a81c2]' }}">
                                        {{ $rank }}
                                    </div>
                                    <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-xl shrink-0 {{ $isTop3 ? 'text-[#46166c]' : 'text-[#1a81c2]' }}">
                                        <i class="bi bi-buildings"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[#003580] text-sm leading-tight m-0">{{ $tm->name }}</h4>
                                        @if ($tm->field)
                                            <span class="text-xs text-slate-500">{{ $tm->field }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 sm:px-6 flex items-center gap-3">
                                    <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                        <div class="bg-[#1a81c2] h-full rounded-full" style="width: {{ $barPct }}%;"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 flex items-center justify-end">
                                    <span class="px-3 py-1 rounded-full bg-sky-50 text-[#003580] font-bold text-xs border border-sky-100">{{ $tm->total }} Peserta</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- 8. FOOTER NOTE --}}
    <div class="w-full py-4 text-center text-xs text-slate-500 italic flex items-center justify-center gap-1.5 bg-surface">
        <i class="bi bi-clock-history text-[#1a81c2]"></i>
        <span>Data dihitung langsung dari peserta PDLK aktif &amp; selesai pada database UPT PP-ESTEM IT Del.</span>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function initCharts() {
        const labelsKegiatan = @json($perJenis->pluck('name')->values());
        const dataKegiatan = @json($perJenis->pluck('total')->map(fn ($v) => (int) $v)->values());

        const totalFlagship = {{ $totalFlagship }};
        const totalMandiri = {{ $totalMandiri }};

        const labelsTren = @json($trenPeriode->pluck('name')->values());
        const dataTrenTotal = @json($trenPeriode->pluck('total')->map(fn ($v) => (int) $v)->values());
        const dataTrenFlagship = @json($trenPeriode->pluck('flagship')->map(fn ($v) => (int) $v)->values());
        const dataTrenMandiri = @json($trenPeriode->pluck('mandiri')->map(fn ($v) => (int) $v)->values());

        const render = () => {
            if (typeof Chart === 'undefined') {
                setTimeout(render, 100);
                return;
            }

            const ctxKegiatan = document.getElementById('chartKegiatan');
            if (ctxKegiatan) {
                new Chart(ctxKegiatan, {
                    type: 'bar',
                    data: {
                        labels: labelsKegiatan,
                        datasets: [{
                            label: 'Jumlah Mahasiswa',
                            data: dataKegiatan,
                            backgroundColor: '#1a81c2',
                            borderRadius: 6,
                            barThickness: 18
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: '#002155', padding: 10, cornerRadius: 8 }
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { family: 'Hanken Grotesk', size: 11 } } },
                            y: { grid: { display: false }, ticks: { font: { family: 'Hanken Grotesk', size: 11, weight: '500' } } }
                        }
                    }
                });
            }

            const ctxSkema = document.getElementById('chartSkema');
            if (ctxSkema && (totalFlagship + totalMandiri > 0)) {
                new Chart(ctxSkema, {
                    type: 'doughnut',
                    data: {
                        labels: ['PDLK Mandiri', 'PDLK Flagship'],
                        datasets: [{
                            data: [totalMandiri, totalFlagship],
                            backgroundColor: ['#46166c', '#1a81c2'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#002155',
                                callbacks: {
                                    label: function (ctx) {
                                        const total = totalFlagship + totalMandiri;
                                        const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                        return ` ${ctx.label}: ${ctx.parsed} Mahasiswa (${pct}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            const ctxTrend = document.getElementById('chartTrend');
            if (ctxTrend && labelsTren.length > 0) {
                new Chart(ctxTrend, {
                    type: 'bar',
                    data: {
                        labels: labelsTren,
                        datasets: [
                            {
                                type: 'line',
                                label: 'Total Partisipasi',
                                data: dataTrenTotal,
                                borderColor: '#8cc5dd',
                                backgroundColor: '#8cc5dd',
                                borderWidth: 3,
                                pointRadius: 5,
                                pointBackgroundColor: '#003580',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                tension: 0.25,
                                order: 1
                            },
                            {
                                type: 'bar',
                                label: 'PDLK Flagship',
                                data: dataTrenFlagship,
                                backgroundColor: '#1a81c2',
                                borderRadius: 4,
                                barThickness: 28,
                                order: 2
                            },
                            {
                                type: 'bar',
                                label: 'PDLK Mandiri',
                                data: dataTrenMandiri,
                                backgroundColor: '#46166c',
                                borderRadius: 4,
                                barThickness: 28,
                                order: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: '#002155', padding: 10, cornerRadius: 8 }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Hanken Grotesk', size: 11, weight: '500' } } },
                            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, font: { family: 'Hanken Grotesk', size: 11 } } }
                        }
                    }
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', render);
        } else {
            render();
        }
    })();
</script>
@endpush
