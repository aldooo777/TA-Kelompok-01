@extends('layouts.pdlk')

@section('title', 'Dashboard Mahasiswa | SI PDLK IT Del')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #003580 0%, #0055A5 100%);
        border-radius: 20px; padding: 28px 32px; color: #ffffff;
        position: relative; overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 53, 128, 0.14); margin-bottom: 24px;
    }
    .welcome-banner::before {
        content: ""; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px);
        background-size: 16px 16px; pointer-events: none;
    }
    .banner-watermark { position: absolute; right: 20px; bottom: -20px; width: 220px; height: 220px; opacity: 0.08; pointer-events: none; z-index: 1; }
    .banner-waves-svg { position: absolute; bottom: 0; left: 0; width: 100%; height: 52px; pointer-events: none; z-index: 2; }
    .banner-content-wrap { position: relative; z-index: 3; }

    .chip-date {
        background: rgba(255, 255, 255, 0.16); backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.28); padding: 5px 14px; border-radius: 20px;
        font-size: 0.8rem; font-weight: 500; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px;
    }
    .welcome-title { font-size: 1.65rem; font-weight: 700; margin-bottom: 4px; line-height: 1.3; }
    .welcome-subtitle { font-size: 0.92rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 18px; }
    .period-card-box {
        background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.22); border-radius: 14px;
        padding: 12px 18px; display: inline-block; max-width: 380px;
    }
    .period-card-box .label-period { color: var(--accent); font-size: 0.7rem; font-weight: 700; letter-spacing: 0.7px; text-transform: uppercase; margin-bottom: 2px; }
    .period-card-box .period-name { font-size: 0.98rem; font-weight: 700; color: #ffffff; margin-bottom: 2px; }
    .period-card-box .period-date { font-size: 0.76rem; color: rgba(255, 255, 255, 0.85); margin-bottom: 0; }

    .stat-card {
        border-radius: 16px; padding: 20px 18px; color: #ffffff; position: relative; overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08); transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%; display: flex; align-items: center; justify-content: space-between;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0, 53, 128, 0.15); }
    .stat-card-1 { background: linear-gradient(135deg, #1a81c2 0%, #0055A5 100%); }
    .stat-card-2 { background: linear-gradient(135deg, #0055A5 0%, #003580 100%); }
    .stat-card-3 { background: linear-gradient(135deg, #46166c 0%, #664780 100%); }
    .stat-card-4 { background: linear-gradient(135deg, #664780 0%, #8b5fb0 100%); }
    .stat-number { font-size: 2.1rem; font-weight: 700; line-height: 1; margin-bottom: 4px; }
    .stat-title { font-size: 0.88rem; font-weight: 600; line-height: 1.25; margin-bottom: 2px; }
    .stat-subtitle { font-size: 0.72rem; opacity: 0.85; margin-bottom: 0; }
    .stat-icon-circle {
        width: 48px; height: 48px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.45rem; flex-shrink: 0;
    }

    .dashboard-box {
        background: #ffffff; border-radius: 16px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden; margin-bottom: 24px; border: 1px solid rgba(0, 53, 128, 0.06);
    }
    .card-header-styled { padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; }
    .card-header-primary { background-color: var(--primary); color: #ffffff; }
    .card-header-purple { background-color: var(--purple-dark); color: #ffffff; }
    .card-header-secondary { background-color: var(--navbar-bg); color: #ffffff; }
    .card-header-styled .header-title { font-size: 0.98rem; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
    .card-header-link { color: rgba(255, 255, 255, 0.9); font-size: 0.8rem; text-decoration: none; font-weight: 500; }
    .card-header-link:hover { color: #ffffff; text-decoration: underline; }
    .dashboard-box-body { padding: 20px; }

    .company-logo-circle {
        width: 52px; height: 52px; background: #f0f4fa; border: 1.5px solid #dce3eb; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: var(--primary); flex-shrink: 0;
    }
    .badge-pdlk-type { background-color: var(--purple-dark); color: #ffffff; font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
    .badge-pdlk-schema { background-color: var(--purple-mid); color: #ffffff; font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; }

    .kegiatan-grid-info {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px 16px;
        background: #f8fafd; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; margin: 18px 0;
    }
    .kegiatan-info-item { display: flex; align-items: flex-start; gap: 10px; }
    .kegiatan-info-item .item-icon { font-size: 1.1rem; color: var(--navbar-bg); margin-top: 1px; }
    .kegiatan-info-item .item-label { font-size: 0.72rem; color: var(--text-muted); margin-bottom: 2px; text-transform: uppercase; font-weight: 500; }
    .kegiatan-info-item .item-value { font-size: 0.84rem; font-weight: 600; color: #2b303a; line-height: 1.25; }

    .progress-custom-wrap { margin-bottom: 18px; }
    .progress-labels { display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
    .progress-bar-track { height: 10px; background-color: #e2e8f0; border-radius: 10px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: linear-gradient(90deg, #1a81c2 0%, #46166c 100%); border-radius: 10px; transition: width 0.6s ease; }

    .status-action-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding-top: 6px; }
    .chip-status-ongoing {
        background-color: rgba(26, 129, 194, 0.12); color: #1a81c2; border: 1px solid rgba(26, 129, 194, 0.35);
        font-size: 0.76rem; font-weight: 600; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
    }
    .chip-status-mode {
        background-color: rgba(0, 85, 165, 0.12); color: #0055A5; border: 1px solid rgba(0, 85, 165, 0.35);
        font-size: 0.76rem; font-weight: 600; padding: 5px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
    }

    .btn-del-outline {
        border: 1.5px solid var(--primary); color: var(--primary); font-weight: 600; font-size: 0.82rem;
        padding: 7px 16px; border-radius: 8px; background: transparent; transition: all 0.2s ease;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-del-outline:hover { background-color: var(--primary); color: #ffffff; }
    .btn-del-primary {
        background-color: var(--primary); color: #ffffff; font-weight: 600; font-size: 0.82rem;
        padding: 7px 18px; border-radius: 8px; border: 1.5px solid var(--primary); transition: all 0.2s ease;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-del-primary:hover { background-color: var(--secondary); border-color: var(--secondary); color: #ffffff; }

    .monev-subcard {
        border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #ffffff;
        height: 100%; display: flex; flex-direction: column; justify-content: space-between;
    }
    .monev-subcard.disabled-card { background: #fbfbfd; border-style: dashed; opacity: 0.85; }

    .profile-card-center { text-align: center; padding: 8px 4px; }
    .avatar-circle-lg {
        width: 76px; height: 76px; margin: 0 auto 12px; background: #ffffff; border-radius: 50%;
        padding: 4px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .avatar-circle-lg-inner {
        width: 100%; height: 100%; border-radius: 50%;
        background: linear-gradient(135deg, #1a81c2 0%, #46166c 100%); color: #ffffff;
        font-size: 1.6rem; font-weight: 700; display: flex; align-items: center; justify-content: center;
    }
    .profile-name { font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 3px; }
    .profile-nim { font-size: 0.84rem; font-weight: 600; color: #2b303a; margin-bottom: 2px; }
    .profile-prodi { font-size: 0.82rem; color: var(--text-muted); margin-bottom: 10px; }
    .badge-status-aktif {
        background-color: #198754; color: #ffffff; font-size: 0.72rem; font-weight: 600;
        padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
    }
    .profile-meta-row {
        display: grid; grid-template-columns: 1fr; gap: 10px; text-align: center;
        padding-top: 14px; margin-top: 14px; border-top: 1px solid #edf2f7;
    }
    }
    .profile-meta-item .meta-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 1px; text-transform: uppercase; font-weight: 500; }
    .profile-meta-item .meta-val { font-size: 0.78rem; font-weight: 600; color: #2b303a; word-break: break-all; }

    .pendaftaran-item, .lowongan-item { padding: 12px 0; border-bottom: 1px solid #f1f4f8; }
    .pendaftaran-item:last-child, .lowongan-item:last-child { border-bottom: none; padding-bottom: 0; }
    .pendaftaran-item:first-child, .lowongan-item:first-child { padding-top: 0; }
    .pendaftaran-title { font-size: 0.86rem; font-weight: 600; color: #2b303a; margin-bottom: 2px; line-height: 1.3; }
    .pendaftaran-company { font-size: 0.76rem; color: var(--text-muted); margin-bottom: 4px; }

    .mini-stepper { display: flex; align-items: center; justify-content: space-between; position: relative; margin: 10px 0 6px; padding: 0 4px; }
    .mini-stepper::before { content: ""; position: absolute; top: 10px; left: 14px; right: 14px; height: 2px; background-color: #e2e8f0; z-index: 1; }
    .mini-step { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; gap: 4px; }
    .step-dot {
        width: 20px; height: 20px; border-radius: 50%; background: #e2e8f0; color: #6c757d;
        font-size: 0.65rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff;
    }
    .mini-step.completed .step-dot { background: #198754; color: #ffffff; }
    .mini-step.current .step-dot { background: var(--secondary); color: #ffffff; box-shadow: 0 0 0 3px rgba(0, 85, 165, 0.2); }
    .step-text { font-size: 0.66rem; font-weight: 600; color: var(--text-muted); }
    .mini-step.current .step-text { color: var(--secondary); font-weight: 700; }

    @media (max-width: 991px) {
        .kegiatan-grid-info { grid-template-columns: repeat(2, 1fr); }
        .banner-watermark { display: none; }
    }
    @media (max-width: 767px) {
        .kegiatan-grid-info { grid-template-columns: 1fr; }
        .stat-number { font-size: 1.8rem; }
        .welcome-banner { padding: 22px 18px; }
        .welcome-title { font-size: 1.35rem; }
    }
</style>
@endpush

@section('content')
@php
    $user = $user ?? null;
    $nim = $nim ?? null;
    $prodiMahasiswa = $prodiMahasiswa ?? null;
    $periodeAktif = $periodeAktif ?? null;
    $pendaftaranList = $pendaftaranList ?? collect();
    $statPendaftaran = $statPendaftaran ?? (object) ['total' => 0, 'diproses' => 0, 'lulus' => 0];
    $pesertaAktif = $pesertaAktif ?? null;
    $monev = $monev ?? collect();
    $lowonganTerbaru = $lowonganTerbaru ?? collect();

    $cisUser = session('cis_user', []);
    $namaMahasiswa = trim((string) ($user?->name ?? $cisUser['nama'] ?? $cisUser['name'] ?? $cisUser['username'] ?? 'Mahasiswa'));
    $parts = preg_split('/\s+/', $namaMahasiswa, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $inisialMhs = strtoupper(substr($parts[0] ?? 'M', 0, 1) . (count($parts) > 1 ? substr($parts[count($parts) - 1], 0, 1) : ''));
    $emailMhs = $user?->email ?? ($cisUser['email'] ?? '-');

    $hariIni = \Carbon\Carbon::now('Asia/Jakarta');
    $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$hariIni->dayOfWeek] ?? '';

    $totalDaftar = (int) ($statPendaftaran->total ?? 0);
    $totalDiproses = (int) ($statPendaftaran->diproses ?? 0);
    $totalLulus = (int) ($statPendaftaran->lulus ?? 0);
    $monevTertunda = (sprint(3) && $monev->isNotEmpty())
        ? $monev->filter(fn ($m) => $m->response_status !== 'submitted' && $m->open_at <= now()->toDateTimeString())->count()
        : 0;

    $persenDurasi = null;
    $teksMinggu = null;
    if ($pesertaAktif && $pesertaAktif->start_date && $pesertaAktif->end_date) {
        $mulai = \Carbon\Carbon::parse($pesertaAktif->start_date, 'Asia/Jakarta')->startOfDay();
        $selesai = \Carbon\Carbon::parse($pesertaAktif->end_date, 'Asia/Jakarta')->endOfDay();
        $totalHari = max(1, $mulai->diffInDays($selesai) + 1);
        $hariBerjalan = max(0, min($totalHari, $mulai->diffInDays($hariIni, false) + 1));
        $persenDurasi = (int) round(($hariBerjalan / $totalHari) * 100);
        $totalMinggu = max(1, (int) ceil($totalHari / 7));
        $mingguKe = max(1, min($totalMinggu, (int) ceil($hariBerjalan / 7)));
        $teksMinggu = "Minggu ke-{$mingguKe} dari {$totalMinggu} · {$persenDurasi}%";
    }
@endphp

@include('partials.flash')

<div class="row g-4">

    {{-- ==================== KOLOM KIRI (±68%) ==================== --}}
    <div class="col-lg-8">

        {{-- 1. BANNER SAPAAN --}}
        <div class="welcome-banner">
            <svg class="banner-watermark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="46" stroke="#ffffff" stroke-width="4" />
                <circle cx="50" cy="50" r="40" stroke="#ffffff" stroke-width="1.5" />
                <rect x="26" y="25" width="48" height="50" rx="4" fill="#ffffff" />
                <path d="M30 42C40 39 45 45 55 42C65 39 70 44 70 44" stroke="#003580" stroke-width="4" stroke-linecap="round"/>
                <path d="M30 50C40 47 45 53 55 50C65 47 70 52 70 52" stroke="#003580" stroke-width="4" stroke-linecap="round"/>
                <path d="M30 58C40 55 45 61 55 58C65 55 70 60 70 60" stroke="#003580" stroke-width="4" stroke-linecap="round"/>
            </svg>

            <svg class="banner-waves-svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,40 C150,90 350,10 500,50 C650,90 900,10 1200,45 L1200,120 L0,120 Z" fill="#8cc5dd" opacity="0.35"></path>
                <path d="M0,65 C200,20 400,95 650,55 C900,15 1050,75 1200,55 L1200,120 L0,120 Z" fill="#1a81c2" opacity="0.40"></path>
            </svg>

            <div class="banner-content-wrap">
                <div class="chip-date">
                    <i class="bi bi-calendar3"></i> {{ tgl($hariIni->toDateString()) }}
                </div>
                <h1 class="welcome-title">Selamat Datang, {{ $namaMahasiswa }}!</h1>
                <p class="welcome-subtitle">Selamat beraktivitas di hari {{ $namaHari }}!</p>

                @if ($periodeAktif)
                    <div class="period-card-box">
                        <div class="label-period">PERIODE AKTIF</div>
                        <div class="period-name">{{ $periodeAktif->name }}</div>
                        @if ($periodeAktif->start_date || $periodeAktif->end_date)
                            <p class="period-date">
                                <i class="bi bi-clock me-1"></i>
                                {{ $periodeAktif->start_date ? tgl($periodeAktif->start_date) : '-' }} s/d {{ $periodeAktif->end_date ? tgl($periodeAktif->end_date) : '-' }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. KARTU STATISTIK (4 KOLOM) --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-1">
                    <div>
                        <div class="stat-number">{{ $totalDaftar }}</div>
                        <div class="stat-title">Pendaftaran Saya</div>
                        <div class="stat-subtitle">Total lowongan didaftar</div>
                    </div>
                    <div class="stat-icon-circle"><i class="bi bi-send"></i></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-2">
                    <div>
                        <div class="stat-number">{{ $totalDiproses }}</div>
                        <div class="stat-title">Sedang Diproses</div>
                        <div class="stat-subtitle">Menunggu hasil seleksi</div>
                    </div>
                    <div class="stat-icon-circle"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-3">
                    <div>
                        <div class="stat-number">{{ $totalLulus }}</div>
                        <div class="stat-title">Lulus Seleksi</div>
                        <div class="stat-subtitle">{{ $totalLulus > 0 ? 'Pendaftaran diterima' : 'Belum ada kelulusan' }}</div>
                    </div>
                    <div class="stat-icon-circle"><i class="bi bi-patch-check"></i></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card stat-card-4">
                    <div>
                        <div class="stat-number">{{ $monevTertunda }}</div>
                        <div class="stat-title">Monev Tertunda</div>
                        <div class="stat-subtitle">{{ $monevTertunda > 0 ? 'Belum dikirim' : 'Tidak ada tagihan' }}</div>
                    </div>
                    <div class="stat-icon-circle"><i class="bi bi-clipboard-check"></i></div>
                </div>
            </div>
        </div>

        {{-- 3. KARTU "KEGIATAN PDLK SAYA" --}}
        <div class="dashboard-box" id="kegiatan-saya">
            <div class="card-header-styled card-header-primary">
                <h2 class="header-title">
                    <i class="bi bi-briefcase-fill"></i> Kegiatan PDLK Saya
                </h2>
                @if ($pesertaAktif)
                    @php
                        $badgeColor = match ($pesertaAktif->status) {
                            'active' => 'bg-success',
                            'completed' => 'bg-info text-dark',
                            default => 'bg-danger',
                        };
                    @endphp
                    <span class="badge {{ $badgeColor }} px-3 py-1 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                        {{ \App\Support\Label::of(\App\Support\Label::PARTICIPANT_STATUS, $pesertaAktif->status) }}
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-1 rounded-pill" style="font-size: 0.75rem; font-weight: 600;">
                        <i class="bi bi-clock me-1"></i> Belum Aktif
                    </span>
                @endif
            </div>

            <div class="dashboard-box-body">
                @if ($pesertaAktif)
                    <div class="d-flex align-items-center gap-3">
                        <div class="company-logo-circle"><i class="bi bi-building"></i></div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="fw-bold" style="font-size: 1.05rem; color: var(--primary);">{{ $pesertaAktif->partner_name ?? '-' }}</span>
                                @if ($pesertaAktif->activity_name)
                                    <span class="badge-pdlk-type">{{ $pesertaAktif->activity_name }}</span>
                                @endif
                                @if ($pesertaAktif->program_category)
                                    <span class="badge-pdlk-schema">
                                        {{ $pesertaAktif->program_category === 'flagship' ? 'Flagship' : 'Mandiri' }}
                                    </span>
                                @endif
                            </div>
                            <div class="fw-semibold text-dark" style="font-size: 0.95rem;">
                                {{ $pesertaAktif->position ?: ($pesertaAktif->activity_name ?? 'Peserta PDLK') }}
                            </div>
                        </div>
                    </div>

                    <div class="kegiatan-grid-info">
                        <div class="kegiatan-info-item">
                            <i class="bi bi-calendar-range item-icon"></i>
                            <div>
                                <div class="item-label">Periode</div>
                                <div class="item-value">{{ $pesertaAktif->period_name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="kegiatan-info-item">
                            <i class="bi bi-geo-alt item-icon"></i>
                            <div>
                                <div class="item-label">Lokasi</div>
                                <div class="item-value">{{ $pesertaAktif->location ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="kegiatan-info-item">
                            <i class="bi bi-person-check item-icon"></i>
                            <div>
                                <div class="item-label">Mentor Industri</div>
                                <div class="item-value">{{ $pesertaAktif->mentor_name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="kegiatan-info-item">
                            <i class="bi bi-mortarboard item-icon"></i>
                            <div>
                                <div class="item-label">Dosen Pembimbing</div>
                                <div class="item-value">{{ $pesertaAktif->advisor_name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="kegiatan-info-item">
                            <i class="bi bi-calendar-plus item-icon"></i>
                            <div>
                                <div class="item-label">Tanggal Mulai</div>
                                <div class="item-value">{{ $pesertaAktif->start_date ? tgl($pesertaAktif->start_date) : '-' }}</div>
                            </div>
                        </div>
                        <div class="kegiatan-info-item">
                            <i class="bi bi-calendar-check item-icon"></i>
                            <div>
                                <div class="item-label">Tanggal Selesai</div>
                                <div class="item-value">{{ $pesertaAktif->end_date ? tgl($pesertaAktif->end_date) : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($persenDurasi !== null)
                        <div class="progress-custom-wrap">
                            <div class="progress-labels">
                                <span>Progres Durasi Kegiatan</span>
                                <span style="color: var(--primary);">{{ $teksMinggu }}</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" style="width: {{ $persenDurasi }}%;"></div>
                            </div>
                        </div>
                    @endif

                    @if ($pesertaAktif->status === 'inactive' && $pesertaAktif->inactive_reason)
                        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.82rem;">
                            <strong>Keterangan:</strong> {{ $pesertaAktif->inactive_reason }}
                        </div>
                    @endif

                    <div class="status-action-row">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($pesertaAktif->stage)
                                <span class="chip-status-ongoing">
                                    <i class="bi bi-play-circle-fill"></i> Tahap: {{ \App\Support\Label::of(\App\Support\Label::STAGE, $pesertaAktif->stage) }}
                                </span>
                            @endif
                            @if ($pesertaAktif->work_mode)
                                <span class="chip-status-mode">
                                    <i class="bi bi-building"></i> Mode: {{ \App\Support\Label::of(\App\Support\Label::WORK_MODE, $pesertaAktif->work_mode) }}
                                </span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @if (sprint(2) && $pesertaAktif->status === 'active')
                                <a href="{{ route('mahasiswa.progres.edit') }}" class="btn-del-outline">
                                    <i class="bi bi-pencil-square"></i> Perbarui Progres
                                </a>
                            @endif
                            @if (sprint(3) && $pesertaAktif->status === 'active')
                                <a href="{{ route('monitoring.index') }}" class="btn-del-primary">
                                    <i class="bi bi-clipboard-check"></i> Buka Monev
                                </a>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px; border-radius: 50%; background-color: rgba(140, 197, 221, 0.3); color: #003580; font-size: 2.2rem;">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">Anda belum terdaftar sebagai peserta PDLK</h3>
                        <p class="text-muted mb-4 mx-auto" style="font-size: 0.88rem; max-width: 440px;">
                            Temukan lowongan yang sesuai dan daftarkan diri Anda, atau hubungi koordinator PDLK prodi untuk pendataan jalur mandiri.
                        </p>
                        <a class="btn-del-primary px-4 py-2" href="{{ route('portal.lowongan.index') }}" style="font-size: 0.88rem;">
                            <i class="bi bi-search"></i> Cari Lowongan
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- 4. KARTU "MONITORING & EVALUASI" --}}
        <div class="dashboard-box">
            <div class="card-header-styled card-header-purple">
                <h2 class="header-title">
                    <i class="bi bi-clipboard2-check-fill"></i> Monitoring &amp; Evaluasi (Monev)
                </h2>
                @if ($periodeAktif)
                    <span style="font-size: 0.78rem; opacity: 0.9;">{{ $periodeAktif->name }}</span>
                @endif
            </div>

            <div class="dashboard-box-body">
                @if (! $pesertaAktif)
                    <div class="monev-subcard disabled-card py-4 text-center d-flex flex-column align-items-center justify-content-center" style="border-left: 4px solid #6c757d;">
                        <div class="mb-3 text-muted" style="font-size: 2.2rem;"><i class="bi bi-clipboard-data text-secondary"></i></div>
                        <div class="fw-semibold text-dark mb-1" style="font-size: 0.96rem;">Monitoring &amp; Evaluasi Belum Tersedia</div>
                        <div class="text-muted" style="font-size: 0.84rem; max-width: 420px;">
                            Monev akan tersedia setelah Anda tercatat sebagai peserta PDLK.
                        </div>
                    </div>
                @elseif (! sprint(3) || $monev->isEmpty())
                    <div class="monev-subcard disabled-card py-4 text-center d-flex flex-column align-items-center justify-content-center" style="border-left: 4px solid #6c757d;">
                        <div class="mb-3 text-muted" style="font-size: 2.2rem;"><i class="bi bi-calendar3 text-secondary"></i></div>
                        <div class="fw-semibold text-dark mb-1" style="font-size: 0.96rem;">Belum Ada Jadwal Monev Aktif</div>
                        <div class="text-muted" style="font-size: 0.84rem; max-width: 420px;">
                            Jadwal monev tengah dan akhir akan muncul di sini setelah dibuka oleh UPT PP-ESTEM.
                        </div>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($monev as $m)
                            @php
                                $sudahKirim = $m->response_status === 'submitted';
                                $belumBuka = $m->open_at > now()->toDateTimeString();
                                $borderLeft = $sudahKirim ? '#198754' : ($belumBuka ? '#6c757d' : '#ffc107');
                            @endphp
                            <div class="col-md-6">
                                <div class="monev-subcard {{ $belumBuka ? 'disabled-card' : '' }}" style="border-left: 4px solid {{ $borderLeft }};">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold" style="font-size: 0.96rem; color: var(--primary);">
                                                {{ $m->type === 'mid' ? 'Monev Tengah' : 'Monev Akhir' }}
                                            </span>
                                            @if ($sudahKirim)
                                                <span class="badge bg-success px-2 py-1 rounded" style="font-size: 0.72rem;">
                                                    <i class="bi bi-check-circle-fill"></i> Terkirim
                                                </span>
                                            @elseif ($belumBuka)
                                                <span class="badge bg-secondary px-2 py-1 rounded" style="font-size: 0.72rem;">
                                                    <i class="bi bi-lock-fill"></i> Belum Dibuka
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark px-2 py-1 rounded" style="font-size: 0.72rem; font-weight: 600;">
                                                    <i class="bi bi-file-earmark-diff"></i> {{ $m->response_status === 'draft' ? 'Draft' : 'Belum Diisi' }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($belumBuka)
                                            <div class="alert alert-light border py-2 px-3 mb-2" style="font-size: 0.78rem; color: #555;">
                                                <i class="bi bi-calendar3 me-1 text-primary"></i> Dibuka <strong>{{ tgl($m->open_at, 'd M Y') }}</strong>
                                            </div>
                                        @elseif ($m->deadline_at)
                                            <div class="text-muted" style="font-size: 0.78rem; margin-bottom: 8px;">
                                                <i class="bi bi-calendar-event me-1"></i> Tenggat:
                                                <span class="fw-semibold {{ $sudahKirim ? 'text-dark' : 'text-danger' }}">{{ tgl($m->deadline_at, 'd M Y') }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="pt-3 mt-2 border-top">
                                        @if ($belumBuka)
                                            <button class="btn btn-secondary w-100 disabled" style="font-size: 0.8rem; padding: 6px 12px;" disabled>
                                                <i class="bi bi-lock"></i> Formulir Terkunci
                                            </button>
                                        @else
                                            <a href="{{ route('monitoring.index') }}" class="btn-del-primary w-100 justify-content-center" style="font-size: 0.8rem; padding: 6px 12px;">
                                                <i class="bi {{ $sudahKirim ? 'bi-eye' : 'bi-arrow-right-circle' }}"></i>
                                                {{ $sudahKirim ? 'Lihat Jawaban' : 'Isi Monev' }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ==================== KOLOM KANAN (±32%) ==================== --}}
    <div class="col-lg-4">

        {{-- 5. KARTU "PROFIL SAYA" --}}
        <div class="dashboard-box">
            <div class="card-header-styled card-header-primary">
                <h2 class="header-title">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </h2>
            </div>
            <div class="dashboard-box-body">
                <div class="profile-card-center">
                    <div class="avatar-circle-lg" style="outline: 2px solid #1a81c2; border: 2px solid #46166c;">
                        <div class="avatar-circle-lg-inner">{{ $inisialMhs }}</div>
                    </div>

                    <h3 class="profile-name">{{ $namaMahasiswa }}</h3>
                    @if ($nim)
                        <div class="profile-nim">NIM: {{ $nim }}</div>
                    @endif
                    <div class="profile-prodi">{{ $prodiMahasiswa ? $prodiMahasiswa . ' · ' : '' }}Institut Teknologi Del</div>

                    @if ($pesertaAktif && $pesertaAktif->status === 'active')
                        <span class="badge-status-aktif"><i class="bi bi-check-circle-fill"></i> Peserta PDLK Aktif</span>
                    @elseif ($pesertaAktif && $pesertaAktif->status === 'completed')
                        <span class="badge bg-info text-dark px-3 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                            <i class="bi bi-award-fill me-1"></i> PDLK Selesai
                        </span>
                    @else
                        <span class="badge bg-secondary text-white px-3 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="bi bi-clock"></i> Belum Menjadi Peserta
                        </span>
                    @endif

                    <div class="profile-meta-row">
                        <div class="profile-meta-item">
                            <div class="meta-label">Email Institusi</div>
                            <div class="meta-val">{{ $emailMhs }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. KARTU "PROGRES PENDAFTARAN" --}}
        <div class="dashboard-box">
            <div class="card-header-styled card-header-secondary">
                <h2 class="header-title">
                    <i class="bi bi-clock-history"></i> Progres Pendaftaran
                </h2>
                @if (sprint(2))
                    <a href="{{ route('mahasiswa.pendaftaran.index') }}" class="card-header-link">Lihat semua →</a>
                @endif
            </div>

            <div class="dashboard-box-body">
                @if ($pendaftaranList->isEmpty())
                    <div class="text-center py-3 text-muted" style="font-size: 0.84rem;">
                        Belum ada pendaftaran lowongan.
                        <div class="mt-2">
                            <a href="{{ route('portal.lowongan.index') }}" class="btn-del-outline" style="font-size: 0.78rem; padding: 5px 12px;">
                                Lihat Lowongan
                            </a>
                        </div>
                    </div>
                @else
                    @foreach ($pendaftaranList as $item)
                        @php
                            $st = $item->status;
                            $badgeClass = match ($st) {
                                'passed' => 'bg-success',
                                'failed' => 'bg-danger',
                                'processing' => 'bg-primary',
                                default => 'bg-warning text-dark',
                            };
                            $labelStatus = !sprint(2)
                                ? 'Terkirim'
                                : \App\Support\Label::of(\App\Support\Label::APPLICATION_STATUS, $st);
                        @endphp
                        <div class="pendaftaran-item">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <div>
                                    <div class="pendaftaran-title">{{ $item->vacancy_title }}</div>
                                    <div class="pendaftaran-company">{{ $item->partner_name }}</div>
                                </div>
                                <span class="badge rounded-pill px-2 py-1 {{ !sprint(2) ? 'bg-success' : $badgeClass }}" style="font-size: 0.7rem;">
                                    {{ $labelStatus }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar3 me-1"></i> Daftar: {{ tgl($item->created_at) }}
                            </div>

                            @if (! sprint(2))
                                <div class="mt-2">
                                    <a href="{{ route('mahasiswa.pendaftaran.dokumen', $item->id) }}" target="_blank" style="font-size: 0.74rem; color: var(--primary); font-weight: 600;">
                                        Lihat Dokumen Terkirim →
                                    </a>
                                </div>
                            @elseif ($loop->first)
                                @php
                                    $step2Selesai = in_array($st, ['passed', 'failed'], true);
                                    $step2Aktif = $st === 'processing';
                                @endphp
                                <div class="mini-stepper">
                                    <div class="mini-step completed">
                                        <div class="step-dot"><i class="bi bi-check"></i></div>
                                        <span class="step-text">Terdaftar</span>
                                    </div>
                                    <div class="mini-step {{ $step2Selesai ? 'completed' : ($step2Aktif ? 'current' : '') }}">
                                        <div class="step-dot">{!! $step2Selesai ? '<i class="bi bi-check"></i>' : '2' !!}</div>
                                        <span class="step-text">Diproses</span>
                                    </div>
                                    <div class="mini-step {{ $step2Selesai ? 'current' : '' }}">
                                        <div class="step-dot"><i class="bi {{ $st === 'failed' ? 'bi-x' : 'bi-patch-check-fill' }}"></i></div>
                                        <span class="step-text">Hasil</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- 7. KARTU "LOWONGAN TERBARU" --}}
        <div class="dashboard-box">
            <div class="card-header-styled card-header-purple">
                <h2 class="header-title">
                    <i class="bi bi-megaphone-fill"></i> Lowongan Terbaru
                </h2>
                <a href="{{ route('portal.lowongan.index') }}" class="card-header-link">Lihat semua →</a>
            </div>

            <div class="dashboard-box-body">
                @forelse ($lowonganTerbaru as $low)
                    @php
                        $sisaHari = $low->registration_deadline
                            ? \Carbon\Carbon::today('Asia/Jakarta')->diffInDays(\Carbon\Carbon::parse($low->registration_deadline, 'Asia/Jakarta')->startOfDay(), false)
                            : null;
                        $mendesak = $sisaHari !== null && $sisaHari >= 0 && $sisaHari <= 3;
                    @endphp
                    <div class="lowongan-item">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge-pdlk-type" style="font-size: 0.68rem; padding: 2px 7px; {{ $loop->even ? 'background-color: var(--navbar-bg);' : '' }}">
                                {{ $low->activity_name }}
                            </span>
                            @if ($mendesak)
                                <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.68rem; font-weight: 600;">
                                    <i class="bi bi-alarm-fill me-1"></i> {{ $sisaHari === 0 ? 'Hari ini' : $sisaHari . ' hari lagi' }}
                                </span>
                            @elseif ($sisaHari !== null)
                                <span class="text-muted" style="font-size: 0.72rem;">{{ $sisaHari }} hari lagi</span>
                            @endif
                        </div>
                        <a href="{{ route('portal.lowongan.show', $low->slug) }}" class="fw-bold d-block text-decoration-none" style="font-size: 0.85rem; color: #2b303a; line-height: 1.3;">
                            {{ $low->title }}
                        </a>
                        <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 2px;">{{ $low->partner_name }}</div>
                        @if ($low->registration_deadline)
                            <div class="{{ $mendesak ? 'text-danger fw-semibold' : 'text-muted' }}" style="font-size: 0.72rem;">
                                Batas: {{ tgl($low->registration_deadline) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-3 text-muted" style="font-size: 0.84rem;">
                        Belum ada lowongan yang sedang dibuka saat ini.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
