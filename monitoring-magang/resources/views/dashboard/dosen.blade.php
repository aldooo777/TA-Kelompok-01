
@extends('layouts.pdlk')

@section('title', 'Dashboard Dosen | Monitoring PDLK')
@section('breadcrumb', 'Portal / Dashboard')
@section('page-title', 'Dashboard Dosen')

@section('content')

@php
    $cisUser = session('cis_user', []);

    $namaDosen = $cisUser['nama']
        ?? $cisUser['name']
        ?? $cisUser['username']
        ?? 'Bapak/Ibu Dosen';

    $stats = [
        [
            'label' => 'Total Pendaftaran',
            'value' => $registrationStats['total'] ?? 0,
            'description' => 'Seluruh pengajuan yang tercatat.',
            'icon' => '▤',
            'class' => 'dosen-blue',
        ],
        [
            'label' => 'Menunggu Persetujuan',
            'value' => $registrationStats['menunggu'] ?? 0,
            'description' => 'Pengajuan yang belum diproses.',
            'icon' => '◷',
            'class' => 'dosen-orange',
        ],
        [
            'label' => 'Diterima',
            'value' => $registrationStats['diterima'] ?? 0,
            'description' => 'Pengajuan yang telah diterima.',
            'icon' => '✓',
            'class' => 'dosen-green',
        ],
        [
            'label' => 'Ditolak',
            'value' => $registrationStats['ditolak'] ?? 0,
            'description' => 'Pengajuan yang telah ditolak.',
            'icon' => '×',
            'class' => 'dosen-red',
        ],
    ];

    $menuDosen = [
        [
            'judul' => 'Daftar Pendaftaran',
            'ikon' => '▤',
            'deskripsi' => 'Periksa pengajuan mahasiswa dan perbarui status pendaftaran.',
            'status' => 'Tersedia',
            'tersedia' => true,
        ],
        [
            'judul' => 'Mahasiswa Bimbingan',
            'ikon' => '♙',
            'deskripsi' => 'Lihat mahasiswa yang menjadi tanggung jawab pembimbing.',
            'status' => 'Tahap berikutnya',
            'tersedia' => false,
        ],
        [
            'judul' => 'Monitoring Mahasiswa',
            'ikon' => '◷',
            'deskripsi' => 'Pantau perkembangan dan kendala mahasiswa selama PDLK.',
            'status' => 'Tahap berikutnya',
            'tersedia' => false,
        ],
        [
            'judul' => 'Laporan dan Penilaian',
            'ikon' => '☆',
            'deskripsi' => 'Periksa laporan dan hasil evaluasi kegiatan PDLK.',
            'status' => 'Tahap berikutnya',
            'tersedia' => false,
        ],
    ];
@endphp

<style>
    .dosen-dashboard {
        --dosen-blue: #348db8;
        --dosen-blue-dark: #287ba5;
        --dosen-text: #26364d;
        --dosen-muted: #718096;
        --dosen-border: #e1e7ef;

        color: var(--dosen-text);
    }

    .dosen-dashboard,
    .dosen-dashboard * {
        box-sizing: border-box;
    }

    /* Banner sambutan */
    .dosen-dashboard .dosen-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        min-height: 190px;
        padding: 30px;
        margin-bottom: 26px;
        overflow: hidden;
        border-radius: 5px;
        background: linear-gradient(115deg, #287eaa, #3699c3);
        color: #fff;
    }

    .dosen-dashboard .dosen-hero::after {
        position: absolute;
        top: -100px;
        right: 65px;
        width: 250px;
        height: 250px;
        border: 27px solid rgba(255, 255, 255, .08);
        border-radius: 50%;
        content: "";
        pointer-events: none;
    }

    .dosen-dashboard .dosen-hero-content {
        position: relative;
        z-index: 1;
        max-width: 750px;
    }

    .dosen-dashboard .dosen-hero-tag {
        display: inline-block;
        padding: 6px 11px;
        margin-bottom: 14px;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 20px;
        background: rgba(255, 255, 255, .12);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .dosen-dashboard .dosen-hero h2 {
        margin: 0 0 10px;
        color: #fff;
        font-size: clamp(22px, 2.3vw, 29px);
        font-weight: 700;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .dosen-dashboard .dosen-hero p {
        max-width: 650px;
        margin: 0;
        color: rgba(255, 255, 255, .94);
        font-size: 13px;
        line-height: 1.9;
    }

    .dosen-dashboard .dosen-hero-icon {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 78px;
        width: 78px;
        height: 78px;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 12px;
        background: rgba(255, 255, 255, .13);
        font-size: 37px;
    }

    /* Panel */
    .dosen-dashboard .dosen-panel {
        padding: 24px;
        margin-bottom: 26px;
        border: 1px solid var(--dosen-border);
        border-radius: 5px;
        background: #fff;
    }

    .dosen-dashboard .dosen-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }

    .dosen-dashboard .dosen-section-heading h3 {
        margin: 0 0 5px;
        color: var(--dosen-text);
        font-size: 19px;
        font-weight: 600;
        line-height: 1.5;
    }

    .dosen-dashboard .dosen-section-heading p {
        margin: 0;
        color: var(--dosen-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    /* Tombol */
    .dosen-dashboard .dosen-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 39px;
        padding: 9px 13px;
        border: 1px solid var(--dosen-blue);
        border-radius: 4px;
        background: var(--dosen-blue);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        text-decoration: none;
        transition: background .15s ease;
    }

    .dosen-dashboard .dosen-button:hover {
        border-color: var(--dosen-blue-dark);
        background: var(--dosen-blue-dark);
        color: #fff;
        text-decoration: none;
    }

    .dosen-dashboard .dosen-button:focus-visible {
        outline: 3px solid rgba(52, 141, 184, .25);
        outline-offset: 3px;
    }

    /* Statistik */
    .dosen-dashboard .dosen-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: stretch;
        gap: 15px;
    }

    .dosen-dashboard .dosen-stat-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 180px;
        padding: 19px;
        border: 1px solid var(--dosen-border);
        border-radius: 5px;
        background: #fff;
        transition: border-color .15s ease;
    }

    .dosen-dashboard .dosen-stat-card:hover {
        border-color: #c9dce9;
    }

    .dosen-dashboard .dosen-stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 18px;
    }

    .dosen-dashboard .dosen-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.6;
    }

    .dosen-dashboard .dosen-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 700;
    }

    .dosen-dashboard .dosen-blue {
        color: #2878c8;
        background: #edf4ff;
    }

    .dosen-dashboard .dosen-orange {
        color: #bd7b17;
        background: #fff4e4;
    }

    .dosen-dashboard .dosen-green {
        color: #0c9970;
        background: #e8f7f0;
    }

    .dosen-dashboard .dosen-red {
        color: #c23b45;
        background: #ffeaec;
    }

    .dosen-dashboard .dosen-stat-value {
        margin-bottom: 8px;
        color: var(--dosen-text);
        font-size: 28px;
        font-weight: 700;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    .dosen-dashboard .dosen-stat-description {
        margin: auto 0 0;
        color: var(--dosen-muted);
        font-size: 11px;
        line-height: 1.8;
    }

    /* Menu dosen */
    .dosen-dashboard .dosen-menu-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .dosen-dashboard .dosen-menu-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--dosen-border);
        border-radius: 5px;
        background: #fff;
    }

    .dosen-dashboard .dosen-menu-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 5px;
        background: #e7f3fa;
        color: var(--dosen-blue);
        font-size: 20px;
    }

    .dosen-dashboard .dosen-menu-content {
        min-width: 0;
        flex: 1;
    }

    .dosen-dashboard .dosen-menu-content h4 {
        margin: 1px 0 7px;
        color: var(--dosen-text);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
    }

    .dosen-dashboard .dosen-menu-content p {
        margin: 0 0 12px;
        color: var(--dosen-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    .dosen-dashboard .dosen-menu-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 3px;
        background: #f1f3f7;
        color: #727f93;
        font-size: 10px;
        font-weight: 600;
    }

    .dosen-dashboard .dosen-menu-status-available {
        background: #e2f7ec;
        color: #087443;
    }

    /* Informasi */
    .dosen-dashboard .dosen-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 17px;
        border-left: 4px solid var(--dosen-blue);
        border-radius: 3px;
        background: #e8f3f9;
        color: #506477;
    }

    .dosen-dashboard .dosen-info-icon {
        flex: 0 0 auto;
        color: var(--dosen-blue);
        font-size: 17px;
    }

    .dosen-dashboard .dosen-info strong {
        display: block;
        margin-bottom: 4px;
        color: var(--dosen-text);
        font-size: 12px;
    }

    .dosen-dashboard .dosen-info p {
        margin: 0;
        color: #596d80;
        font-size: 12px;
        line-height: 1.8;
    }

    /* Responsif */
    @media (max-width: 1100px) {
        .dosen-dashboard .dosen-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 750px) {
        .dosen-dashboard .dosen-menu-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (max-width: 600px) {
        .dosen-dashboard .dosen-hero {
            min-height: 0;
            padding: 22px 19px;
            gap: 12px;
        }

        .dosen-dashboard .dosen-hero h2 {
            font-size: 22px;
        }

        .dosen-dashboard .dosen-hero p {
            font-size: 12px;
        }

        .dosen-dashboard .dosen-hero-icon {
            flex-basis: 48px;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            font-size: 25px;
        }

        .dosen-dashboard .dosen-panel {
            padding: 16px;
        }

        .dosen-dashboard .dosen-stats-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        .dosen-dashboard .dosen-stat-card {
            min-height: 150px;
            padding: 17px;
        }

        .dosen-dashboard .dosen-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .dosen-dashboard .dosen-section-heading .dosen-button {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .dosen-dashboard .dosen-stat-card,
        .dosen-dashboard .dosen-button {
            transition: none;
        }
    }
</style>

<div class="dosen-dashboard">

    {{-- Banner sambutan --}}
    <section class="dosen-hero">
        <div class="dosen-hero-content">

            <span class="dosen-hero-tag">
                PORTAL DOSEN PEMBIMBING
            </span>

            <h2>
                Selamat datang, {{ $namaDosen }}!
            </h2>

            <p>
                Kelola pendaftaran dan dukung pemantauan kegiatan
                Pembelajaran di Luar Kampus mahasiswa melalui satu portal.
            </p>

        </div>

        <div class="dosen-hero-icon" aria-hidden="true">
            👨‍🏫
        </div>
    </section>

    {{-- Ringkasan pendaftaran --}}
    <section class="dosen-panel">

        <div class="dosen-section-heading">
            <div>
                <h3>Ringkasan Pendaftaran PDLK</h3>
                <p>
                    Jumlah pengajuan mahasiswa berdasarkan status saat ini.
                </p>
            </div>

            <a
                href="{{ route('registration.index') }}"
                class="dosen-button"
            >
                Kelola Pendaftaran →
            </a>
        </div>

        <div class="dosen-stats-grid">

            @foreach ($stats as $stat)
                <article class="dosen-stat-card">

                    <div class="dosen-stat-top">
                        <span class="dosen-stat-label">
                            {{ $stat['label'] }}
                        </span>

                        <span
                            class="dosen-stat-icon {{ $stat['class'] }}"
                            aria-hidden="true"
                        >
                            {{ $stat['icon'] }}
                        </span>
                    </div>

                    <div class="dosen-stat-value">
                        {{ $stat['value'] }}
                    </div>

                    <p class="dosen-stat-description">
                        {{ $stat['description'] }}
                    </p>

                </article>
            @endforeach

        </div>
    </section>

    {{-- Menu dosen --}}
    <section class="dosen-panel">

        <div class="dosen-section-heading">
            <div>
                <h3>Menu Dosen</h3>
                <p>
                    Akses fungsi pengelolaan kegiatan PDLK sesuai
                    ketersediaan modul.
                </p>
            </div>
        </div>

        <div class="dosen-menu-grid">

            @foreach ($menuDosen as $menu)
                <article class="dosen-menu-card">

                    <div class="dosen-menu-icon" aria-hidden="true">
                        {{ $menu['ikon'] }}
                    </div>

                    <div class="dosen-menu-content">

                        <h4>{{ $menu['judul'] }}</h4>

                        <p>{{ $menu['deskripsi'] }}</p>

                        @if ($menu['tersedia'])
                            <a
                                href="{{ route('registration.index') }}"
                                class="dosen-button"
                            >
                                Buka Menu →
                            </a>
                        @else
                            <span class="dosen-menu-status">
                                {{ $menu['status'] }}
                            </span>
                        @endif

                    </div>

                </article>
            @endforeach

        </div>
    </section>

    {{-- Informasi --}}
    <section class="dosen-info" role="note">

        <span class="dosen-info-icon" aria-hidden="true">ⓘ</span>

        <div>
            <strong>Informasi Sistem</strong>

            <p>
                Statistik pendaftaran ditampilkan berdasarkan data yang
                tersedia pada sistem. Menu monitoring mahasiswa, laporan,
                dan penilaian akan diaktifkan setelah modul terkait selesai
                dikembangkan.
            </p>
        </div>

    </section>

</div>

@endsection
