@extends('layouts.pdlk')

@php
    use App\Support\Peran;
    $peran = Peran::aktif();
    $cisUser = session('cis_user', []);
    $nama = trim((string) ($user?->name ?? $cisUser['nama'] ?? $cisUser['name'] ?? $cisUser['username'] ?? 'Pengguna'));
    $email = $user?->email ?? ($cisUser['email'] ?? '-');

    $parts = preg_split('/\s+/', $nama, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $inisial = strtoupper(substr($parts[0] ?? 'U', 0, 1) . (count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''));

    $hariIni = \Carbon\Carbon::now('Asia/Jakarta');
    $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hariIni->dayOfWeek] ?? '';
@endphp

@section('title', 'Dashboard | Sistem Informasi PDLK')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard ' . Peran::label())

@if ($peran === 'upt_staff')
@section('content')
@php
    $periodeAktif = $periodeAktif ?? null;
    $skemaPeserta = $skemaPeserta ?? (object) ['total' => 0, 'flagship' => 0, 'mandiri' => 0];
    $pesertaPerJenis = $pesertaPerJenis ?? collect();
    $pelamarTerbaru = $pelamarTerbaru ?? collect();
    $riwayatStatus = $riwayatStatus ?? collect();
    $jadwalMonev = $jadwalMonev ?? collect();
    $lowonganSegeraTutup = $lowonganSegeraTutup ?? collect();
    $mitraStat = $mitraStat ?? (object) ['total' => 0, 'cooperating' => 0, 'in_process' => 0];

    $pesertaAktifTotal = (int) ($statistikPeserta['active'] ?? 0);
    $pelamarMenunggu = (int) (($statistikPendaftaran['registered'] ?? 0) + ($statistikPendaftaran['processing'] ?? 0));
    $jumlahJenisAktif = $pesertaPerJenis->where('total', '>', 0)->count();

    $skemaTotal = (int) ($skemaPeserta->total ?? 0);
    $skemaFlagship = (int) ($skemaPeserta->flagship ?? 0);
    $skemaMandiri = (int) ($skemaPeserta->mandiri ?? 0);
    $pctFlagship = $skemaTotal > 0 ? round(($skemaFlagship / $skemaTotal) * 100) : 0;
    $pctMandiri = $skemaTotal > 0 ? round(($skemaMandiri / $skemaTotal) * 100) : 0;
@endphp

<div class="flex flex-col w-full font-body-md text-on-surface">
    @include('partials.flash')

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start w-full">

        {{-- ==================== KOLOM KIRI (±68%) ==================== --}}
        <div class="xl:col-span-8 flex flex-col gap-space-lg min-w-0">

            {{-- 1. BANNER SAPAAN STAF --}}
            <div class="relative overflow-hidden rounded-[20px] bg-gradient-to-r from-primary to-secondary text-on-primary shadow-md p-6 sm:p-8">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#ffffff 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>
                <div class="absolute -right-6 -bottom-10 pointer-events-none select-none text-white/5 font-display-lg text-[160px] leading-none font-bold">DEL</div>

                <div class="absolute bottom-0 left-0 right-0 pointer-events-none overflow-hidden h-12">
                    <svg class="absolute bottom-0 w-full h-10 text-secondary-container opacity-35" preserveAspectRatio="none" viewBox="0 0 1200 120">
                        <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,40 L1200,120 L0,120 Z" fill="currentColor"></path>
                    </svg>
                    <svg class="absolute bottom-0 w-full h-8 text-primary-fixed opacity-40" preserveAspectRatio="none" viewBox="0 0 1200 120">
                        <path d="M0,40 C300,120 450,10 700,70 C950,130 1100,20 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col justify-between gap-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm text-white font-label-sm tracking-wide">
                            <i class="bi bi-calendar3"></i>
                            <span>{{ $namaHari }}, {{ tgl($hariIni->toDateString()) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-white/80 font-label-sm">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Panel Staf UPT PP-ESTEM</span>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="flex flex-col max-w-xl">
                            <h1 class="font-headline-lg text-white font-bold leading-tight drop-shadow-sm m-0">
                                Selamat Datang, {{ $nama }}!
                            </h1>
                            <p class="font-body-md text-white/90 mt-1 mb-0">
                                Panel Pengelolaan &amp; Operasional PDLK UPT PP-ESTEM Institut Teknologi Del
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                            <a href="{{ route('kelola.lowongan.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-label-md bg-white/20 hover:bg-white text-white hover:text-primary transition-all duration-150 shadow-sm backdrop-blur-sm no-underline">
                                <i class="bi bi-plus-circle"></i><span>Lowongan</span>
                            </a>
                            @if (sprint(2))
                                <a href="{{ route('peserta.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-label-md bg-white/20 hover:bg-white text-white hover:text-primary transition-all duration-150 shadow-sm backdrop-blur-sm no-underline">
                                    <i class="bi bi-person-plus"></i><span>Peserta</span>
                                </a>
                            @endif
                            <a href="{{ route('kelola.postingan.create') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-label-md bg-white/20 hover:bg-white text-white hover:text-primary transition-all duration-150 shadow-sm backdrop-blur-sm no-underline">
                                <i class="bi bi-megaphone"></i><span>Berita</span>
                            </a>
                        </div>
                    </div>

                    @if ($periodeAktif)
                        <div class="pt-2">
                            <div class="inline-flex flex-wrap items-center gap-3 px-4 py-2 rounded-lg bg-white/10 backdrop-blur-md">
                                <span class="font-label-sm tracking-wider uppercase text-secondary-fixed">PERIODE AKTIF</span>
                                <span class="text-white font-headline-sm font-semibold">{{ $periodeAktif->name }}</span>
                                @if ($periodeAktif->start_date || $periodeAktif->end_date)
                                    <span class="hidden sm:inline text-white/60">•</span>
                                    <span class="text-white/85 font-body-sm">
                                        {{ $periodeAktif->start_date ? tgl($periodeAktif->start_date) : '-' }} s/d {{ $periodeAktif->end_date ? tgl($periodeAktif->end_date) : '-' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. KARTU STATISTIK (4 KOLOM) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-[#1a81c2] to-secondary text-white p-5 shadow-sm hover:-translate-y-1 transition-all duration-150">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-white/90">Peserta Aktif</span>
                        <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center text-white text-lg"><i class="bi bi-people"></i></div>
                    </div>
                    <div class="mt-2">
                        <div class="font-display-lg font-bold leading-none">{{ $pesertaAktifTotal }}</div>
                        <div class="font-label-sm text-white/80 mt-1">Di {{ $jumlahJenisAktif }} jenis kegiatan</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-secondary to-primary text-white p-5 shadow-sm hover:-translate-y-1 transition-all duration-150">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-white/90">Pelamar Menunggu</span>
                        <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center text-white text-lg"><i class="bi bi-person-lines-fill"></i></div>
                    </div>
                    <div class="mt-2">
                        <div class="font-display-lg font-bold leading-none">{{ $pelamarMenunggu }}</div>
                        <div class="font-label-sm text-white/80 mt-1">Perlu verifikasi &amp; seleksi</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-tertiary to-[#664780] text-white p-5 shadow-sm hover:-translate-y-1 transition-all duration-150">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-white/90">Lowongan Dibuka</span>
                        <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center text-white text-lg"><i class="bi bi-megaphone"></i></div>
                    </div>
                    <div class="mt-2">
                        <div class="font-display-lg font-bold leading-none">{{ $jumlahLowonganDibuka }}</div>
                        <div class="font-label-sm text-white/80 mt-1">{{ $jumlahLowonganMendesak }} mendekati tenggat</div>
                    </div>
                </div>

                <div class="relative overflow-hidden rounded-xl bg-gradient-to-br from-[#664780] to-[#8b5fb0] text-white p-5 shadow-sm hover:-translate-y-1 transition-all duration-150">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-white/90">Berita Terbit</span>
                        <div class="w-10 h-10 rounded-full bg-white/15 flex items-center justify-center text-white text-lg"><i class="bi bi-newspaper"></i></div>
                    </div>
                    <div class="mt-2">
                        <div class="font-display-lg font-bold leading-none">{{ $jumlahBerita }}</div>
                        <div class="font-label-sm text-white/80 mt-1">Publikasi informasi aktif</div>
                    </div>
                </div>
            </div>

            {{-- 3. KARTU GRAFIK & ANALITIK --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-primary px-6 py-4 flex items-center justify-between text-on-primary">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center text-white">
                            <i class="bi bi-bar-chart-line text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-headline-sm font-semibold text-white leading-tight m-0">Analisis Distribusi Kegiatan &amp; Skema PDLK</h2>
                            <p class="font-body-sm text-white/80 m-0">{{ $periodeAktif?->name ?? 'Semua Periode Aktif' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('rekapitulasi') }}" class="hidden sm:flex items-center gap-1.5 bg-white/10 hover:bg-white/20 px-3 py-1 rounded-md text-white font-label-sm no-underline">
                        <i class="bi bi-graph-up"></i><span>Rekap Lengkap</span>
                    </a>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-7 flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-label-md text-primary uppercase tracking-wide">Peserta Aktif per Jenis Kegiatan</span>
                            <span class="font-label-sm text-on-surface-variant">Total: {{ $pesertaAktifTotal }} Mhs</span>
                        </div>
                        <div class="relative h-[240px] w-full">
                            <canvas id="barChartKegiatan"></canvas>
                        </div>
                    </div>

                    <div class="lg:col-span-5 flex flex-col justify-center">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-label-md text-primary uppercase tracking-wide">Skema Program PDLK</span>
                            <span class="font-label-sm text-on-surface-variant">2 Kategori</span>
                        </div>
                        <div class="relative h-[200px] w-full flex items-center justify-center">
                            @if ($skemaTotal > 0)
                                <canvas id="doughnutChartSkema"></canvas>
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="font-headline-md font-bold text-primary leading-none">{{ $skemaTotal }}</span>
                                    <span class="font-label-sm text-on-surface-variant">Peserta</span>
                                </div>
                            @else
                                <p class="text-xs text-on-surface-variant text-center">Belum ada peserta aktif.</p>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-col gap-2 bg-surface-container-low p-3 rounded-lg">
                            <div class="flex items-center justify-between font-body-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-sm bg-[#1a81c2]"></span>
                                    <span class="text-on-surface font-medium">PDLK Flagship</span>
                                </div>
                                <span class="font-label-md text-primary">{{ $pctFlagship }}% ({{ $skemaFlagship }} mhs)</span>
                            </div>
                            <div class="flex items-center justify-between font-body-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-sm bg-[#46166c]"></span>
                                    <span class="text-on-surface font-medium">PDLK Mandiri</span>
                                </div>
                                <span class="font-label-md text-tertiary">{{ $pctMandiri }}% ({{ $skemaMandiri }} mhs)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. KARTU "PELAMAR PERLU DIPROSES" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-[#1a81c2] px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                            <i class="bi bi-person-check text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-headline-sm font-semibold text-white leading-tight m-0">Pelamar Perlu Diproses</h2>
                            <p class="font-body-sm text-white/80 m-0">Antrian pendaftaran terbaru yang menunggu tindak lanjut</p>
                        </div>
                    </div>
                    @if (sprint(2))
                        <a class="font-label-md text-white hover:underline flex items-center gap-1 no-underline" href="{{ route('kelola.pendaftaran.index') }}">
                            <span>Lihat semua</span><i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    @if ($pelamarTerbaru->isEmpty())
                        <div class="p-8 text-center text-on-surface-variant text-sm">
                            Belum ada pendaftar yang menunggu proses saat ini.
                        </div>
                    @else
                        <table class="w-full text-left font-body-sm text-on-surface">
                            <thead>
                                <tr class="bg-primary text-white font-label-md tracking-wide">
                                    <th class="py-3 px-4">Nama Pelamar</th>
                                    <th class="py-3 px-3">NIM</th>
                                    <th class="py-3 px-3">Program Studi</th>
                                    <th class="py-3 px-3">Lowongan Dituju</th>
                                    <th class="py-3 px-3">Tanggal Daftar</th>
                                    <th class="py-3 px-3">Status</th>
                                    @if (sprint(2))
                                        <th class="py-3 px-4 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                @foreach ($pelamarTerbaru as $pel)
                                    @php
                                        $isProses = $pel->status === 'processing';
                                    @endphp
                                    <tr class="{{ $loop->even ? 'bg-surface-container-low' : 'bg-surface-container-lowest' }} hover:bg-surface-container transition-colors">
                                        <td class="py-3.5 px-4 font-semibold text-primary">{{ $pel->name }}</td>
                                        <td class="py-3.5 px-3 font-mono text-on-surface-variant font-medium">{{ $pel->nim }}</td>
                                        <td class="py-3.5 px-3 text-on-surface-variant">{{ $pel->prodi }}</td>
                                        <td class="py-3.5 px-3">
                                            <div class="font-medium text-on-surface">{{ $pel->vacancy_title }}</div>
                                            <div class="text-[11px] text-on-surface-variant">{{ $pel->partner_name }}</div>
                                        </td>
                                        <td class="py-3.5 px-3 text-on-surface-variant whitespace-nowrap">{{ tgl($pel->created_at) }}</td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold {{ $isProses ? 'bg-blue-100 text-secondary' : 'bg-amber-100 text-amber-800' }}">
                                                <i class="bi {{ $isProses ? 'bi-file-earmark-medical' : 'bi-hourglass-split' }}"></i>
                                                <span>{{ \App\Support\Label::of(\App\Support\Label::APPLICATION_STATUS, $pel->status) }}</span>
                                            </span>
                                        </td>
                                        @if (sprint(2))
                                            <td class="py-3.5 px-4 text-center">
                                                <a href="{{ route('kelola.pendaftaran.index', ['q' => $pel->nim]) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-primary hover:bg-[#002660] text-white text-xs font-semibold transition-colors shadow-sm no-underline">
                                                    <i class="bi bi-check2-circle"></i><span>Proses</span>
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- 5. KARTU "PERUBAHAN STATUS PESERTA TERBARU" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-tertiary px-6 py-4 flex items-center justify-between text-on-tertiary">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white">
                            <i class="bi bi-clock-history text-lg"></i>
                        </div>
                        <div>
                            <h2 class="font-headline-sm font-semibold text-white leading-tight m-0">Perubahan Status Peserta Terbaru</h2>
                            <p class="font-body-sm text-white/80 m-0">Riwayat mutasi status peserta PDLK</p>
                        </div>
                    </div>
                    <span class="font-label-sm bg-white/10 px-3 py-1 rounded text-white">Riwayat DB</span>
                </div>

                <div class="p-6">
                    @if ($riwayatStatus->isEmpty())
                        <p class="text-center text-on-surface-variant text-sm py-4 m-0">Belum ada riwayat perubahan status peserta.</p>
                    @else
                        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-surface-container-high">
                            @foreach ($riwayatStatus as $r)
                                @php
                                    $dotClass = match ($r->new_status) {
                                        'inactive' => 'bg-error',
                                        'active', 'completed' => 'bg-emerald-600',
                                        default => 'bg-amber-500',
                                    };
                                    $badgeClass = match ($r->new_status) {
                                        'inactive' => 'bg-error-container text-on-error-container',
                                        'active', 'completed' => 'bg-emerald-100 text-emerald-800',
                                        default => 'bg-amber-100 text-amber-800',
                                    };
                                    $oldLabel = $r->old_status ? \App\Support\Label::of(\App\Support\Label::PARTICIPANT_STATUS, $r->old_status) : 'Baru';
                                    $newLabel = \App\Support\Label::of(\App\Support\Label::PARTICIPANT_STATUS, $r->new_status);
                                @endphp
                                <div class="relative">
                                    <div class="absolute -left-[27px] top-1 w-3.5 h-3.5 rounded-full {{ $dotClass }} ring-4 ring-white"></div>
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-headline-sm font-semibold text-primary">{{ $r->participant_name }}</span>
                                            <span class="font-body-sm text-on-surface-variant">({{ $r->nim }}{{ $r->prodi ? ' · ' . $r->prodi : '' }})</span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold {{ $badgeClass }}">
                                                {{ $oldLabel }} → {{ $newLabel }}
                                            </span>
                                        </div>
                                        <span class="font-label-sm text-on-surface-variant flex items-center gap-1">
                                            <i class="bi bi-clock"></i> {{ tgl($r->created_at, 'd M Y, H:i') }}
                                        </span>
                                    </div>
                                    @if ($r->reason)
                                        <p class="font-body-sm text-on-surface mt-1 bg-surface-container-low p-2.5 rounded-lg mb-0">
                                            <strong class="font-label-sm text-on-surface-variant uppercase">Alasan:</strong> {{ $r->reason }}
                                        </p>
                                    @endif
                                    @if ($r->actor_name)
                                        <div class="font-label-sm text-on-surface-variant mt-1 flex items-center gap-1.5">
                                            <i class="bi bi-shield-check text-primary"></i>
                                            <span>Diubah oleh: <strong class="text-on-surface">{{ $r->actor_name }}</strong></span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN (±32%) ==================== --}}
        <div class="xl:col-span-4 flex flex-col gap-space-lg min-w-0">

            {{-- 6. KARTU "PROFIL SAYA" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-primary px-5 py-3.5 flex items-center gap-2.5 text-on-primary">
                    <i class="bi bi-person-circle text-lg"></i>
                    <h2 class="font-headline-sm font-semibold text-white m-0">Profil Saya</h2>
                </div>
                <div class="p-6 flex flex-col items-center text-center">
                    <div class="relative p-1 rounded-full bg-gradient-to-tr from-tertiary to-secondary shadow-md mb-3">
                        <div class="w-20 h-20 rounded-full bg-primary flex items-center justify-center text-white font-headline-xl font-bold border-2 border-white">
                            {{ $inisial }}
                        </div>
                    </div>
                    <h3 class="font-headline-sm font-bold text-primary m-0">{{ $nama }}</h3>
                    <span class="mt-1 px-3 py-0.5 rounded font-label-sm uppercase tracking-wider bg-tertiary text-on-tertiary">
                        {{ Peran::label() }}
                    </span>
                    <p class="font-body-sm text-on-surface-variant mt-2 mb-0">UPT PP-ESTEM Institut Teknologi Del</p>

                    <div class="w-full h-px bg-surface-container my-4"></div>

                    <div class="grid grid-cols-1 w-full gap-2 text-left font-body-sm">
                        <div class="flex items-start gap-2 bg-surface-container-low p-2.5 rounded-lg">
                            <i class="bi bi-envelope text-secondary text-base shrink-0 mt-0.5"></i>
                            <div class="min-w-0">
                                <span class="font-label-sm text-on-surface-variant block">Email Institusi</span>
                                <span class="text-on-surface font-semibold truncate block">{{ $email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. KARTU "JADWAL MONEV" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-tertiary px-5 py-3.5 flex items-center justify-between text-on-tertiary">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-calendar-check text-lg"></i>
                        <h2 class="font-headline-sm font-semibold text-white m-0">Jadwal Monev PDLK</h2>
                    </div>
                    @if (sprint(3))
                        <a class="font-label-sm text-white/90 hover:text-white hover:underline flex items-center gap-1 no-underline" href="{{ route('kelola.jadwal-monev.index') }}">
                            <span>Kelola</span><i class="bi bi-chevron-right text-[10px]"></i>
                        </a>
                    @endif
                </div>

                <div class="p-5 flex flex-col gap-4">
                    @if ($jadwalMonev->isEmpty())
                        <p class="text-xs text-on-surface-variant text-center py-4 m-0">Belum ada jadwal monev yang dibuat.</p>
                    @else
                        @foreach ($jadwalMonev as $jm)
                            @php
                                $nowStr = now('Asia/Jakarta')->toDateTimeString();
                                $berjalan = !$jm->is_closed && $jm->open_at <= $nowStr && $jm->deadline_at >= $nowStr;
                                $belumBuka = $jm->open_at > $nowStr;
                                $pctKirim = $pesertaAktifTotal > 0 ? min(100, round(($jm->jumlah_kirim / $pesertaAktifTotal) * 100)) : 0;
                            @endphp
                            <div class="rounded-lg bg-surface-container-low p-4 flex flex-col gap-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-label-lg font-bold text-primary">{{ $jm->type === 'mid' ? 'Monev Tengah' : 'Monev Akhir' }}</span>
                                    @if ($berjalan)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">Berjalan</span>
                                    @elseif ($belumBuka)
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-surface-variant text-on-surface-variant">Belum Dibuka</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-200 text-slate-700">Ditutup</span>
                                    @endif
                                </div>

                                @if ($berjalan)
                                    <div>
                                        <div class="flex justify-between font-label-sm text-on-surface mb-1">
                                            <span>Pengisian Mahasiswa</span>
                                            <span class="font-bold text-primary">{{ $pctKirim }}% ({{ $jm->jumlah_kirim }} / {{ $pesertaAktifTotal }})</span>
                                        </div>
                                        <div class="w-full bg-surface-container-high h-2.5 rounded-full overflow-hidden">
                                            <div class="bg-gradient-to-r from-secondary to-tertiary h-full rounded-full" style="width: {{ $pctKirim }}%;"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between font-body-sm text-on-surface-variant pt-1">
                                    <span>Jadwal:</span>
                                    <span class="font-semibold text-on-surface">{{ tgl($jm->open_at, 'd M') }} – {{ tgl($jm->deadline_at, 'd M Y') }}</span>
                                </div>

                                @if (sprint(3))
                                    <a href="{{ route('rekap-monev.index') }}"
                                       class="mt-1 w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded bg-primary hover:bg-[#002660] text-white font-label-md transition-colors shadow-sm no-underline">
                                        <i class="bi bi-clipboard-data"></i><span>Lihat Rekap Monev</span>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- 8. KARTU "LOWONGAN SEGERA DITUTUP" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-secondary px-5 py-3.5 flex items-center justify-between text-on-secondary">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-hourglass-bottom text-lg"></i>
                        <h2 class="font-headline-sm font-semibold text-white m-0">Lowongan Segera Ditutup</h2>
                    </div>
                    <a class="font-label-sm text-white/90 hover:text-white hover:underline flex items-center gap-1 no-underline" href="{{ route('kelola.lowongan.index') }}">
                        <span>Kelola</span><i class="bi bi-chevron-right text-[10px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-surface-container">
                    @forelse ($lowonganSegeraTutup as $low)
                        @php
                            $sisaHari = \Carbon\Carbon::today('Asia/Jakarta')->diffInDays(\Carbon\Carbon::parse($low->registration_deadline, 'Asia/Jakarta')->startOfDay(), false);
                            $badgeStyle = $sisaHari <= 3 ? 'bg-red-100 text-error' : ($sisaHari <= 7 ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-secondary');
                        @endphp
                        <div class="p-4 hover:bg-surface-container-low transition-colors flex flex-col gap-1.5">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('kelola.lowongan.edit', $low->id) }}" class="font-label-lg font-bold text-primary hover:text-secondary no-underline">
                                    {{ $low->title }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold shrink-0 {{ $badgeStyle }}">
                                    {{ $sisaHari === 0 ? 'Hari ini' : $sisaHari . ' hari lagi' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between font-body-sm text-on-surface-variant">
                                <span>{{ $low->partner_name }}</span>
                                <span class="font-medium text-primary"><i class="bi bi-person-fill text-xs"></i> {{ $low->jumlah_pelamar }} Pelamar</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-on-surface-variant">Tidak ada lowongan aktif dengan batas waktu terdekat.</div>
                    @endforelse
                </div>
            </div>

            {{-- 9. KARTU "MITRA PDLK" --}}
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="bg-primary px-5 py-3.5 flex items-center justify-between text-on-primary">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-building text-lg"></i>
                        <h2 class="font-headline-sm font-semibold text-white m-0">Mitra Industri PDLK</h2>
                    </div>
                    <a class="font-label-sm text-white/90 hover:text-white hover:underline flex items-center gap-1 no-underline" href="{{ route('kelola.master.index', 'mitra') }}">
                        <span>Semua Mitra</span><i class="bi bi-chevron-right text-[10px]"></i>
                    </a>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-emerald-50 rounded-lg p-3 text-center">
                            <span class="font-headline-md font-bold text-emerald-700 leading-none">{{ $mitraStat->cooperating ?? 0 }}</span>
                            <span class="font-label-sm text-emerald-800 block mt-1">Bekerja Sama</span>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-3 text-center">
                            <span class="font-headline-md font-bold text-amber-700 leading-none">{{ $mitraStat->in_process ?? 0 }}</span>
                            <span class="font-label-sm text-amber-800 block mt-1">Dalam Proses</span>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <span class="font-headline-md font-bold text-secondary leading-none">{{ $mitraStat->total ?? 0 }}</span>
                            <span class="font-label-sm text-secondary block mt-1">Total Mitra</span>
                        </div>
                    </div>

                    <a href="{{ route('kelola.master.index', 'mitra') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded font-label-md text-primary bg-transparent hover:bg-primary hover:text-white transition-all duration-150 shadow-sm no-underline"
                       style="border: 1.5px solid #003580;">
                        <i class="bi bi-sliders"></i>
                        <span>Kelola Mitra Kerjasama</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const labelsBar = @json($pesertaPerJenis->pluck('name')->values());
        const dataBar = @json($pesertaPerJenis->pluck('total')->map(fn ($v) => (int) $v)->values());

        const ctxBar = document.getElementById('barChartKegiatan');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labelsBar,
                    datasets: [{
                        label: 'Jumlah Peserta',
                        data: dataBar,
                        backgroundColor: '#1a81c2',
                        hoverBackgroundColor: '#003580',
                        borderRadius: 4,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#002155',
                            padding: 10,
                            callbacks: { label: (ctx) => ctx.parsed.y + ' Mahasiswa' }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0, 53, 128, 0.06)' }, ticks: { precision: 0, color: '#747782' } },
                        x: { grid: { display: false }, ticks: { color: '#434651' } }
                    }
                }
            });
        }

        const totalSkema = {{ $skemaTotal }};
        const ctxDoughnut = document.getElementById('doughnutChartSkema');
        if (ctxDoughnut && totalSkema > 0) {
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: ['PDLK Flagship', 'PDLK Mandiri'],
                    datasets: [{
                        data: [{{ $skemaFlagship }}, {{ $skemaMandiri }}],
                        backgroundColor: ['#1a81c2', '#46166c'],
                        borderWidth: 0,
                        cutout: '72%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#002155',
                            padding: 10,
                            callbacks: {
                                label: function (ctx) {
                                    const pct = totalSkema > 0 ? Math.round((ctx.parsed / totalSkema) * 100) : 0;
                                    return `${ctx.label}: ${ctx.parsed} mhs (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

@else
{{-- TAMPILAN KOORDINATOR PDLK PRODI & KAPRODI --}}
@section('content')
@php
    $namaProdiCakupan = $namaProdiCakupan ?? 'Program Studi';
    $periodeAktif = $periodeAktif ?? null;
    $daftarPesertaProdi = $daftarPesertaProdi ?? collect();
    $pesertaPerJenis = $pesertaPerJenis ?? collect();
    $sebaranMitraProdi = $sebaranMitraProdi ?? collect();
    $skorMonevBagian = $skorMonevBagian ?? collect();
    $belumMonevProdi = $belumMonevProdi ?? collect();
    $riwayatStatus = $riwayatStatus ?? collect();
    $jenisKegiatanList = $jenisKegiatanList ?? collect();

    $jmlAktif = (int) ($statistikPeserta['active'] ?? 0);
    $jmlTidakAktif = (int) ($statistikPeserta['inactive'] ?? 0);
    $jmlSelesai = (int) ($statistikPeserta['completed'] ?? 0);
    $jmlBelumMonev = $belumMonevProdi->count();

    $totalJenisProdi = (int) $pesertaPerJenis->sum('total');
    $rataMonevProdi = $skorMonevBagian->isNotEmpty()
        ? number_format((float) $skorMonevBagian->avg('rata_rata'), 2)
        : null;
    $maxMitraProdi = max(1, (int) ($sebaranMitraProdi->max('total') ?? 0));
@endphp

<div class="flex flex-col w-full">
    @include('partials.flash')

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ==================== KOLOM KIRI (±68%) ==================== --}}
        <div class="w-full lg:w-[68%] flex flex-col gap-6 min-w-0">

            {{-- 1. BANNER SAPAAN --}}
            <div class="relative overflow-hidden rounded-[20px] bg-gradient-to-r from-[#003580] via-[#004899] to-[#0055A5] text-white p-6 sm:p-8 shadow-md">
                <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(#ffffff 1.2px, transparent 1.2px); background-size: 16px 16px;"></div>
                <div class="absolute -right-6 -bottom-8 opacity-10 pointer-events-none select-none">
                    <svg class="w-64 h-64 text-white fill-current" viewBox="0 0 24 24">
                        <path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2zm0 13.54L3.6 11.2 2 12l10 5 10-5-1.6-.8L12 15.54zM12 20l-8.4-4.2L2 16.6 12 22l10-5.4-1.6-.8L12 20z"></path>
                    </svg>
                </div>
                <div class="absolute -bottom-1 left-0 right-0 h-10 pointer-events-none overflow-hidden">
                    <svg class="absolute bottom-0 w-full h-8 text-[#8cc5dd] opacity-35" preserveAspectRatio="none" viewBox="0 0 1200 120">
                        <path d="M0,0 C150,90 350,-40 500,60 C650,160 900,10 1200,40 L1200,120 L0,120 Z" fill="currentColor"></path>
                    </svg>
                    <svg class="absolute bottom-0 w-full h-6 text-[#1a81c2] opacity-40" preserveAspectRatio="none" viewBox="0 0 1200 120">
                        <path d="M0,40 C300,110 600,0 900,50 C1050,75 1150,20 1200,30 L1200,120 L0,120 Z" fill="currentColor"></path>
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex flex-col gap-2.5 max-w-xl">
                        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-medium w-fit border border-white/20">
                            <i class="bi bi-calendar3"></i>
                            <span>{{ $namaHari }}, {{ tgl($hariIni->toDateString()) }}</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white leading-tight m-0">
                            Selamat Datang, {{ $nama }}!
                        </h1>
                        <div class="flex flex-wrap items-center gap-2 pt-0.5">
                            <span class="inline-block bg-white/15 backdrop-blur-sm border border-white/30 text-white text-xs px-2.5 py-0.5 rounded-full font-medium">
                                {{ Peran::label() }} · {{ $namaProdiCakupan }}
                            </span>
                            <p class="text-white/90 text-sm leading-relaxed m-0">
                                @if (! sprint(2))
                                    Fitur pemantauan mahasiswa PDLK prodi aktif mulai Sprint 2.
                                @else
                                    Pantau pelaksanaan PDLK mahasiswa di program studi Anda.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row md:flex-col items-start md:items-end gap-3 shrink-0">
                        @if ($periodeAktif)
                            <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-xl p-3.5 text-left md:text-right min-w-[210px] shadow-sm">
                                <span class="text-[#8cc5dd] text-[10px] font-bold uppercase tracking-wider block">PERIODE AKTIF</span>
                                <p class="text-base font-bold text-white tracking-wide m-0">{{ $periodeAktif->name }}</p>
                                @if ($periodeAktif->start_date || $periodeAktif->end_date)
                                    <p class="text-xs text-white/80 m-0">
                                        {{ $periodeAktif->start_date ? tgl($periodeAktif->start_date) : '-' }} s/d {{ $periodeAktif->end_date ? tgl($periodeAktif->end_date) : '-' }}
                                    </p>
                                @endif
                            </div>
                        @endif
                        @if (sprint(3))
                            <a href="{{ route('rekap-monev.index') }}"
                               class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white border border-white/40 text-xs font-semibold px-4 py-2.5 rounded-lg transition-all backdrop-blur-sm shadow-sm no-underline">
                                <i class="bi bi-download text-sm"></i>
                                <span>Rekap Monev Prodi</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. 4 KARTU STATISTIK --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl bg-gradient-to-br from-[#1a81c2] to-[#0055A5] text-white p-4 shadow-sm hover:-translate-y-1 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white/90 uppercase tracking-wide">Peserta Aktif</span>
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0"><i class="bi bi-person-check text-lg"></i></div>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-bold tracking-tight leading-none">{{ $jmlAktif }}</span>
                        <p class="text-[11px] text-white/80 mt-1 mb-0">Mahasiswa sedang berkegiatan</p>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-[#0055A5] to-[#003580] text-white p-4 shadow-sm hover:-translate-y-1 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white/90 uppercase tracking-wide">Tidak Aktif</span>
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0"><i class="bi bi-person-x text-lg"></i></div>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-bold tracking-tight leading-none">{{ $jmlTidakAktif }}</span>
                        <p class="text-[11px] text-white/80 mt-1 mb-0">Dihentikan / kendala khusus</p>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-[#46166c] to-[#664780] text-white p-4 shadow-sm hover:-translate-y-1 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white/90 uppercase tracking-wide">Selesai</span>
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0"><i class="bi bi-trophy text-lg"></i></div>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-bold tracking-tight leading-none">{{ $jmlSelesai }}</span>
                        <p class="text-[11px] text-white/80 mt-1 mb-0">Telah menuntaskan PDLK</p>
                    </div>
                </div>

                <div class="rounded-xl bg-gradient-to-br from-[#664780] to-[#8b5fb0] text-white p-4 shadow-sm hover:-translate-y-1 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-white/90 uppercase tracking-wide">Belum Isi Monev</span>
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0"><i class="bi bi-exclamation-diamond text-lg"></i></div>
                    </div>
                    <div class="mt-3">
                        <span class="text-3xl font-bold tracking-tight leading-none">{{ $jmlBelumMonev }}</span>
                        <p class="text-[11px] text-white/80 mt-1 mb-0">{{ sprint(3) ? 'Jadwal monev berjalan' : 'Aktif mulai Sprint 3' }}</p>
                    </div>
                </div>
            </div>

            {{-- 3. KARTU "MAHASISWA PDLK PRODI" --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-t-[3px] border-[#46166c]">
                <div class="bg-[#003580] px-5 py-3.5 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-people-fill text-lg"></i>
                        <h2 class="font-semibold text-base tracking-wide text-white m-0">Mahasiswa PDLK · {{ $namaProdiCakupan }}</h2>
                    </div>
                    @if (sprint(2))
                        <a class="text-xs font-medium text-[#8cc5dd] hover:text-white transition-colors flex items-center gap-1 no-underline" href="{{ route('peserta.index') }}">
                            <span>Lihat semua</span><i class="bi bi-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="p-4 bg-gray-50/70 border-b border-gray-200">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-5 relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400"><i class="bi bi-search"></i></span>
                            <input class="w-full bg-white pl-9 pr-3 py-2 text-xs rounded border border-gray-300 text-gray-700 focus:outline-none focus:border-[#0055A5]" id="searchInputProdi" placeholder="Cari NIM, Nama, atau Mitra..." type="text">
                        </div>
                        <div class="sm:col-span-3">
                            <select class="w-full bg-white px-3 py-2 text-xs rounded border border-gray-300 text-gray-700 focus:outline-none focus:border-[#0055A5]" id="filterKegiatanProdi">
                                <option value="">Semua Jenis Kegiatan</option>
                                @foreach ($jenisKegiatanList as $jk)
                                    <option value="{{ $jk->name }}">{{ $jk->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <select class="w-full bg-white px-3 py-2 text-xs rounded border border-gray-300 text-gray-700 focus:outline-none focus:border-[#0055A5]" id="filterStatusProdi">
                                <option value="">Semua Status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button class="p-2 text-gray-500 hover:text-[#003580] bg-white border border-gray-300 rounded hover:bg-gray-100 w-full flex items-center justify-center" id="btnResetFilterProdi" title="Reset Filter" type="button">
                                <i class="bi bi-arrow-counterclockwise text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @if ($daftarPesertaProdi->isEmpty())
                        <div class="p-8 text-center text-gray-500 text-xs">
                            Belum ada mahasiswa PDLK yang tercatat untuk {{ $namaProdiCakupan }}.
                        </div>
                    @else
                        <table class="w-full text-left text-xs text-gray-700">
                            <thead class="bg-[#003580] text-white uppercase text-[11px] font-semibold tracking-wider">
                                <tr>
                                    <th class="py-3 px-3">NIM</th>
                                    <th class="py-3 px-3">Nama Mahasiswa</th>
                                    <th class="py-3 px-3">Jenis Kegiatan</th>
                                    <th class="py-3 px-3">Mitra</th>
                                    <th class="py-3 px-3">Tahap</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    @if (sprint(2))
                                        <th class="py-3 px-3 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="studentTableBodyProdi">
                                @foreach ($daftarPesertaProdi as $p)
                                    @php
                                        $stLabel = \App\Support\Label::of(\App\Support\Label::PARTICIPANT_STATUS, $p->status);
                                        $stBadge = match ($p->status) {
                                            'active' => 'bg-emerald-100 text-emerald-800',
                                            'inactive' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-blue-100 text-[#003580]',
                                        };
                                        $katLabel = $p->program_category === 'flagship' ? 'Flagship' : ($p->program_category === 'independent' ? 'Mandiri' : '');
                                    @endphp
                                    <tr class="{{ $loop->even ? 'bg-gray-50/50' : '' }} hover:bg-blue-50/40 transition-colors">
                                        <td class="py-3 px-3 font-semibold text-[#003580] whitespace-nowrap">{{ $p->nim }}</td>
                                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap">{{ $p->name }}</td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="inline-block bg-blue-50 text-[#003580] px-2 py-0.5 rounded text-[11px] font-medium border border-blue-200">
                                                {{ $p->activity_name ?? 'PDLK' }}{{ $katLabel ? ' (' . $katLabel . ')' : '' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-gray-700">{{ $p->partner_name ?? '-' }}</td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="text-gray-800 font-medium">{{ $p->stage ? \App\Support\Label::of(\App\Support\Label::STAGE, $p->stage) : '-' }}</span>
                                            @if ($p->work_mode)
                                                <span class="text-[10px] text-gray-500 block">{{ \App\Support\Label::of(\App\Support\Label::WORK_MODE, $p->work_mode) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $stBadge }}">{{ $stLabel }}</span>
                                        </td>
                                        @if (sprint(2))
                                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a href="{{ route('peserta.show', $p->id) }}"
                                                       class="p-1 px-2 rounded border border-[#003580] text-[#003580] hover:bg-[#003580] hover:text-white transition-colors no-underline" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if ($peran === 'pdlk_coordinator')
                                                        <a href="{{ route('peserta.edit', $p->id) }}"
                                                           class="p-1 px-2 rounded bg-[#003580] text-white hover:bg-[#002660] transition-colors flex items-center gap-1 no-underline" title="Ubah">
                                                            <i class="bi bi-pencil-square"></i>
                                                            <span class="hidden sm:inline text-[11px]">Ubah</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- 4. KARTU GRAFIK ANALISIS PRODI --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-t-[3px] border-[#46166c]">
                <div class="bg-[#46166c] px-5 py-3.5 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-bar-chart-line-fill text-lg"></i>
                        <h2 class="font-semibold text-base tracking-wide text-white m-0">Analisis Distribusi &amp; Evaluasi Prodi</h2>
                    </div>
                    <span class="text-xs text-purple-200 bg-white/10 px-2 py-0.5 rounded">Data Prodi</span>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5 m-0">
                                <span class="w-2 h-2 rounded-full bg-[#1a81c2]"></span>
                                Peserta per Jenis Kegiatan
                            </h3>
                            <span class="text-[11px] text-gray-400 font-medium">Total: {{ $totalJenisProdi }} Peserta</span>
                        </div>
                        <div class="h-56 relative w-full">
                            <canvas id="chartKegiatanProdi"></canvas>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5 m-0">
                                <span class="w-2 h-2 rounded-full bg-[#46166c]"></span>
                                Rata-rata Skor Monev per Bagian
                            </h3>
                            @if ($rataMonevProdi)
                                <span class="text-[11px] text-gray-400 font-medium">Rata-rata: {{ $rataMonevProdi }}</span>
                            @endif
                        </div>
                        <div class="h-56 relative w-full flex items-center justify-center">
                            @if ($skorMonevBagian->isEmpty())
                                <p class="text-xs text-gray-400 text-center">Belum ada data skor monev untuk prodi ini.</p>
                            @else
                                <canvas id="chartMonevProdi"></canvas>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==================== KOLOM KANAN (±32%) ==================== --}}
        <div class="w-full lg:w-[32%] flex flex-col gap-6">

            {{-- 5. KARTU "PROFIL KOORDINATOR" (Tanpa info login terakhir) --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-t-[3px] border-[#003580]">
                <div class="bg-[#003580] px-4 py-3 flex items-center gap-2 text-white">
                    <i class="bi bi-person-badge text-base"></i>
                    <h2 class="font-semibold text-sm tracking-wide text-white m-0">Profil Pengelola Prodi</h2>
                </div>
                <div class="p-5 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-[#003580] text-white flex items-center justify-center font-bold text-2xl ring-4 ring-[#1a81c2] ring-offset-2 ring-offset-white shadow-md">
                        {{ $inisial }}
                    </div>
                    <h3 class="mt-3.5 text-base font-bold text-gray-900 leading-tight m-0">{{ $nama }}</h3>
                    <div class="flex flex-wrap items-center justify-center gap-1.5 mt-3">
                        <span class="bg-blue-50 text-[#003580] border border-blue-200 px-2.5 py-0.5 rounded text-[11px] font-semibold">
                            {{ Peran::label() }}
                        </span>
                        <span class="bg-purple-50 text-[#46166c] border border-purple-200 px-2.5 py-0.5 rounded text-[11px] font-semibold">
                            {{ $namaProdiCakupan }}
                        </span>
                    </div>

                    <div class="w-full mt-4 pt-4 border-t border-gray-100 flex flex-col gap-2 text-xs text-left">
                        <div class="flex items-center justify-between text-gray-600 gap-2">
                            <span class="text-gray-400 flex items-center gap-1.5 shrink-0"><i class="bi bi-envelope"></i> Email:</span>
                            <span class="font-medium text-gray-800 truncate">{{ $email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. KARTU "PERLU PERHATIAN" --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-t-[3px] border-[#664780]">
                <div class="bg-[#664780] px-4 py-3 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-amber-300"></i>
                        <h2 class="font-semibold text-sm tracking-wide text-white m-0">Perlu Perhatian</h2>
                    </div>
                    <span class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] font-bold">
                        {{ $jmlBelumMonev + $riwayatStatus->count() }}
                    </span>
                </div>

                <div class="p-4 flex flex-col gap-3">
                    @if (sprint(3))
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                <span>Belum Kirim Monev</span>
                            </div>
                            @if ($belumMonevProdi->isEmpty())
                                <p class="text-xs text-gray-400 m-0">Semua peserta aktif sudah mengisi monev berjalan.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($belumMonevProdi as $bm)
                                        <div class="p-2.5 rounded-lg bg-rose-50/60 border border-rose-100 flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-900 truncate m-0">{{ $bm->name }}</p>
                                                <p class="text-[11px] text-gray-500 truncate m-0">{{ $bm->nim }} · {{ $bm->partner_name ?? '-' }}</p>
                                            </div>
                                            @if ($bm->deadline_at)
                                                <span class="bg-rose-100 text-rose-800 text-[10px] font-bold px-2 py-0.5 rounded shrink-0">
                                                    {{ tgl($bm->deadline_at, 'd M') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="{{ sprint(3) ? 'pt-2 border-t border-gray-100' : '' }}">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider block mb-2">Perubahan Status Terbaru</span>
                        @forelse ($riwayatStatus->take(2) as $rs)
                            <div class="p-2.5 rounded-lg bg-amber-50/70 border border-amber-200/80 mb-2 last:mb-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-xs font-bold text-gray-900 m-0">{{ $rs->participant_name }}</p>
                                    <span class="bg-blue-100 text-[#003580] text-[10px] font-semibold px-1.5 py-0.5 rounded shrink-0">
                                        {{ \App\Support\Label::of(\App\Support\Label::PARTICIPANT_STATUS, $rs->new_status) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-amber-900 mt-1 leading-snug m-0">
                                    {{ $rs->nim }}{{ $rs->reason ? ' · ' . $rs->reason : '' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 m-0">Belum ada perubahan status terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 7. KARTU "SEBARAN MITRA PRODI" --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border-t-[3px] border-[#1a81c2]">
                <div class="bg-[#1a81c2] px-4 py-3 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-building-check text-base"></i>
                        <h2 class="font-semibold text-sm tracking-wide text-white m-0">Sebaran Mitra Prodi</h2>
                    </div>
                    <a class="text-[11px] text-blue-100 hover:text-white underline" href="{{ route('mitra') }}">Semua Mitra</a>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    @forelse ($sebaranMitraProdi as $sm)
                        @php
                            $barColor = match ($loop->iteration) {
                                1 => '#003580', 2 => '#1a81c2', 3 => '#0055A5', 4 => '#46166c', default => '#664780'
                            };
                            $pct = max(8, round(($sm->total / $maxMitraProdi) * 100));
                        @endphp
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between text-xs">
                                <span class="font-semibold text-gray-800 truncate pr-2">{{ $loop->iteration }}. {{ $sm->name }}</span>
                                <span class="font-bold shrink-0" style="color: {{ $barColor }};">{{ $sm->total }} Mhs</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ $pct }}%; background-color: {{ $barColor }};"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2 m-0">Belum ada data penempatan mitra pada prodi ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- 8. KARTU AKSI CEPAT --}}
            <div class="rounded-xl bg-gradient-to-br from-[#46166c] to-[#664780] text-white p-5 shadow-sm flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <i class="bi bi-lightning-charge-fill text-amber-300 text-lg"></i>
                    <h2 class="font-bold text-sm text-white m-0">Tindakan Cepat Prodi</h2>
                </div>
                <p class="text-xs text-purple-100 leading-snug m-0">
                    Akses cepat pengelolaan peserta program studi dan rekapitulasi pelaksanaan PDLK.
                </p>
                <div class="flex flex-col gap-2 mt-1">
                    @if (sprint(2) && $peran === 'pdlk_coordinator')
                        <a href="{{ route('peserta.create') }}"
                           class="w-full bg-white text-[#46166c] hover:bg-gray-100 font-semibold py-2.5 px-3 rounded-lg text-xs transition-colors flex items-center justify-center gap-2 shadow-sm no-underline">
                            <i class="bi bi-person-plus-fill"></i>
                            <span>+ Tambah Peserta Prodi</span>
                        </a>
                    @endif
                    @if (sprint(2))
                        <a href="{{ route('peserta.index') }}"
                           class="w-full bg-transparent border border-white/50 text-white hover:bg-white/10 font-semibold py-2.5 px-3 rounded-lg text-xs transition-colors flex items-center justify-center gap-2 no-underline">
                            <i class="bi bi-people"></i>
                            <span>Lihat Semua Mahasiswa Prodi</span>
                        </a>
                    @endif
                    @if (sprint(3))
                        <a href="{{ route('rekap-monev.index') }}"
                           class="w-full bg-transparent border border-white/50 text-white hover:bg-white/10 font-semibold py-2.5 px-3 rounded-lg text-xs transition-colors flex items-center justify-center gap-2 no-underline">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                            <span>Rekap &amp; Ekspor Monev Prodi</span>
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKegiatan = document.getElementById('chartKegiatanProdi');
        if (ctxKegiatan) {
            new Chart(ctxKegiatan, {
                type: 'bar',
                data: {
                    labels: @json($pesertaPerJenis->pluck('name')->values()),
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: @json($pesertaPerJenis->pluck('total')->map(fn ($v) => (int) $v)->values()),
                        backgroundColor: ['#1a81c2', '#0055A5', '#46166c', '#8cc5dd', '#664780', '#175eae', '#375ca8'],
                        borderRadius: 6,
                        barThickness: 26
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(0, 53, 128, 0.06)' } },
                        x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                    }
                }
            });
        }

        const ctxMonev = document.getElementById('chartMonevProdi');
        if (ctxMonev) {
            new Chart(ctxMonev, {
                type: 'bar',
                data: {
                    labels: @json($skorMonevBagian->pluck('bagian')->values()),
                    datasets: [{
                        label: 'Skor Rata-rata',
                        data: @json($skorMonevBagian->pluck('rata_rata')->map(fn ($v) => (float) $v)->values()),
                        backgroundColor: ['#46166c', '#664780', '#1a81c2', '#003580', '#375ca8', '#502176'],
                        borderRadius: 4,
                        barThickness: 16
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(0, 53, 128, 0.06)' } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        const searchInput = document.getElementById('searchInputProdi');
        const filterKegiatan = document.getElementById('filterKegiatanProdi');
        const filterStatus = document.getElementById('filterStatusProdi');
        const btnReset = document.getElementById('btnResetFilterProdi');
        const tableBody = document.getElementById('studentTableBodyProdi');

        function filterTable() {
            if (!tableBody) return;
            const query = (searchInput?.value || '').toLowerCase();
            const jenis = (filterKegiatan?.value || '').toLowerCase();
            const status = (filterStatus?.value || '').toLowerCase();

            for (const row of tableBody.getElementsByTagName('tr')) {
                const text = row.innerText.toLowerCase();
                const ok = (!query || text.includes(query)) && (!jenis || text.includes(jenis)) && (!status || text.includes(status));
                row.style.display = ok ? '' : 'none';
            }
        }

        searchInput?.addEventListener('input', filterTable);
        filterKegiatan?.addEventListener('change', filterTable);
        filterStatus?.addEventListener('change', filterTable);
        btnReset?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (filterKegiatan) filterKegiatan.value = '';
            if (filterStatus) filterStatus.value = '';
            filterTable();
        });
    });
</script>
@endpush
@endif
