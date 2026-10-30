
@extends('layouts.pdlk')

@section('title', 'Dashboard Mahasiswa | Monitoring PDLK')
@section('breadcrumb', 'Portal / Dashboard')
@section('page-title', 'Dashboard Mahasiswa')

@section('content')

<style>
    /* Semua gaya dibatasi pada Dashboard Mahasiswa */
    .student-dashboard {
        --student-blue: #348db8;
        --student-blue-dark: #267ba5;
        --student-text: #26364d;
        --student-muted: #718096;
        --student-border: #e1e7ef;
        --student-bg-soft: #f5f8fc;
        color: var(--student-text);
    }

    .student-dashboard * {
        box-sizing: border-box;
    }

    /* Sambutan */
    .student-dashboard .student-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        min-height: 205px;
        padding: 32px;
        margin-bottom: 28px;
        overflow: hidden;
        border-radius: 5px;
        background: linear-gradient(115deg, #287eaa, #3699c3);
        color: #fff;
    }

    .student-dashboard .student-hero::before {
        position: absolute;
        top: -100px;
        right: 65px;
        width: 270px;
        height: 270px;
        border: 28px solid rgba(255, 255, 255, .07);
        border-radius: 50%;
        content: "";
        pointer-events: none;
    }

    .student-dashboard .student-hero-content {
        position: relative;
        z-index: 1;
        max-width: 740px;
    }

    .student-dashboard .student-hero-tag {
        display: inline-block;
        padding: 6px 11px;
        margin-bottom: 13px;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 20px;
        background: rgba(255, 255, 255, .12);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .student-dashboard .student-hero h2 {
        margin: 0 0 10px;
        color: #fff;
        font-size: clamp(23px, 2.3vw, 30px);
        font-weight: 700;
        line-height: 1.4;
    }

    .student-dashboard .student-hero p {
        max-width: 650px;
        margin: 0;
        color: rgba(255, 255, 255, .94);
        font-size: 13px;
        line-height: 1.9;
    }

    .student-dashboard .student-hero-icon {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 88px;
        width: 88px;
        height: 88px;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 16px;
        background: rgba(255, 255, 255, .13);
        font-size: 43px;
    }

    /* Panel umum */
    .student-dashboard .student-panel {
        padding: 24px;
        margin-bottom: 26px;
        border: 1px solid var(--student-border);
        border-radius: 5px;
        background: #fff;
    }

    .student-dashboard .student-section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .student-dashboard .student-section-heading h2 {
        margin: 0 0 5px;
        color: var(--student-text);
        font-size: 19px;
        font-weight: 600;
        line-height: 1.5;
    }

    .student-dashboard .student-section-heading p {
        margin: 0;
        color: var(--student-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    /* Kartu ringkasan */
    .student-dashboard .student-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    .student-dashboard .student-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 225px;
        padding: 20px;
        border: 1px solid var(--student-border);
        border-radius: 5px;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .student-dashboard .student-card:hover {
        border-color: #c8dbea;
        box-shadow: 0 4px 14px rgba(38, 65, 95, .05);
    }

    .student-dashboard .student-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .student-dashboard .student-card-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        line-height: 1.6;
    }

    .student-dashboard .student-card-icon {
        display: flex;
        flex: 0 0 40px;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 9px;
        font-size: 19px;
    }

    .student-dashboard .student-icon-blue {
        color: #2878c8;
        background: #edf4ff;
    }

    .student-dashboard .student-icon-green {
        color: #0c9970;
        background: #e8f7f0;
    }

    .student-dashboard .student-icon-orange {
        color: #cc861b;
        background: #fff4e4;
    }

    .student-dashboard .student-card h3 {
        margin: 0 0 10px;
        color: var(--student-text);
        font-size: 18px;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .student-dashboard .student-card-description {
        margin: 0;
        color: var(--student-muted);
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .student-dashboard .student-status {
        display: inline-flex;
        align-items: center;
        align-self: flex-start;
        width: fit-content;
        max-width: 100%;
        padding: 6px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .student-dashboard .student-status-menunggu {
        color: #8d6207;
        background: #fff2cf;
    }

    .student-dashboard .student-status-diterima {
        color: #087443;
        background: #e2f7ec;
    }

    .student-dashboard .student-status-ditolak {
        color: #b4232d;
        background: #ffeaec;
    }

    .student-dashboard .student-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        align-self: flex-start;
        gap: 7px;
        max-width: 100%;
        min-height: 38px;
        padding: 9px 12px;
        margin-top: auto;
        border: 1px solid var(--student-blue);
        border-radius: 4px;
        background: var(--student-blue);
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        overflow-wrap: anywhere;
        transition: background .15s ease, border-color .15s ease;
    }

    .student-dashboard .student-action:hover {
        border-color: var(--student-blue-dark);
        background: var(--student-blue-dark);
        color: #fff;
        text-decoration: none;
    }

    .student-dashboard .student-action-spaced {
        margin-top: 18px;
    }

    .student-dashboard .student-card .student-action {
        margin-top: 18px;
    }

    .student-dashboard .student-card .student-card-description + .student-action {
        margin-top: auto;
        transform: translateY(0);
    }

    .student-dashboard .student-card .student-card-description {
        margin-bottom: 18px;
    }

    .student-dashboard .student-card .student-action {
        margin-top: auto;
    }

    /* Alur kegiatan */
    .student-dashboard .student-flow-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .student-dashboard .student-flow-card {
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--student-border);
        border-radius: 5px;
        background: #fff;
    }

    .student-dashboard .student-flow-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        height: 33px;
        margin-bottom: 16px;
        border-radius: 50%;
        background: #eaf3fb;
        color: var(--student-blue);
        font-size: 12px;
        font-weight: 700;
    }

    .student-dashboard .student-flow-card h3 {
        margin: 0 0 8px;
        color: var(--student-text);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.5;
    }

    .student-dashboard .student-flow-card p {
        margin: 0;
        color: var(--student-muted);
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .student-dashboard .student-flow-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #edf0f4;
    }

    .student-dashboard .student-flow-footer p {
        margin: 0;
        color: var(--student-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    @media (max-width: 1050px) {
        .student-dashboard .student-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .student-dashboard .student-flow-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 650px) {
        .student-dashboard .student-hero {
            align-items: flex-start;
            min-height: auto;
            padding: 23px 20px;
            gap: 14px;
        }

        .student-dashboard .student-hero h2 {
            font-size: 22px;
        }

        .student-dashboard .student-hero p {
            font-size: 12px;
        }

        .student-dashboard .student-hero-icon {
            flex-basis: 54px;
            width: 54px;
            height: 54px;
            border-radius: 10px;
            font-size: 28px;
        }

        .student-dashboard .student-panel {
            padding: 17px;
        }

        .student-dashboard .student-summary-grid,
        .student-dashboard .student-flow-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .student-dashboard .student-card {
            min-height: 0;
            padding: 18px;
        }

        .student-dashboard .student-section-heading {
            flex-direction: column;
        }

        .student-dashboard .student-flow-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .student-dashboard .student-flow-footer .student-action {
            align-self: stretch;
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>

<div class="student-dashboard">

    {{-- SAMBUTAN --}}
    <section class="student-hero">
        <div class="student-hero-content">
            <span class="student-hero-tag">
                PORTAL MAHASISWA PDLK
            </span>

            <h2>
                Selamat datang,
                {{ session('cis_user')['username'] ?? 'Mahasiswa' }}!
            </h2>

            <p>
                Kelola kegiatan Pembelajaran di Luar Kampus melalui satu portal.
                Pantau status pendaftaran, monitoring, dan laporan kegiatan Anda.
            </p>
        </div>

        <div class="student-hero-icon" aria-hidden="true">
            🎓
        </div>
    </section>

    {{-- RINGKASAN --}}
    <section class="student-panel">

        <div class="student-section-heading">
            <div>
                <h2>Ringkasan</h2>
                <p>Informasi utama kegiatan PDLK Anda.</p>
            </div>
        </div>

        <div class="student-summary-grid">

            {{-- STATUS PENDAFTARAN --}}
            <article class="student-card">
                <div class="student-card-top">
                    <span class="student-card-label">
                        Status Pendaftaran
                    </span>

                    <div class="student-card-icon student-icon-blue"
                         aria-hidden="true">
                        ◷
                    </div>
                </div>

                @if ($latestRegistration)
                    @php
                        $registrationStatus = strtolower(
                            (string) ($latestRegistration->status ?? '')
                        );

                        $statusClass = match ($registrationStatus) {
                            'menunggu' => 'student-status-menunggu',
                            'diterima' => 'student-status-diterima',
                            'ditolak' => 'student-status-ditolak',
                            default => '',
                        };

                        $statusLabel = match ($registrationStatus) {
                            'menunggu' => 'Menunggu',
                            'diterima' => 'Diterima',
                            'ditolak' => 'Ditolak',
                            default => 'Tidak diketahui',
                        };
                    @endphp

                    <div>
                        <span class="student-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>


<p class="student-card-description">
    Pendaftaran terbaru untuk
    <strong>{{ $latestRegistration->tempat_magang ?? 'Tempat PDLK' }}</strong>.@if ($registrationStatus === 'diterima')
        Pendaftaran Anda telah diterima.
    @elseif ($registrationStatus === 'ditolak')
        Pendaftaran Anda ditolak. Anda dapat melihat pilihan PDLK lainnya.
    @else
        Pendaftaran Anda masih menunggu persetujuan.
    @endif
</p>


                    @if ($registrationStatus === 'diterima')
                        <a href="{{ route('monitoring.index') }}"
                           class="student-action">
                            Buka Monitoring →
                        </a>
                    @elseif ($registrationStatus === 'ditolak')
                        <a href="{{ route('pdlk.index') }}"
                           class="student-action">
                            Lihat Pilihan PDLK →
                        </a>
                    @else
                        <a href="{{ route('pdlk.index') }}"
                           class="student-action">
                            Lihat Daftar PDLK →
                        </a>
                    @endif

                @else
                    <span class="student-status student-status-menunggu">
                        Belum mendaftar
                    </span>

                    <p class="student-card-description">
                        Anda belum memiliki pendaftaran PDLK yang tercatat di sistem.
                    </p>

                    <a href="{{ route('pdlk.index') }}"
                       class="student-action">
                        Daftar PDLK →
                    </a>
                @endif
            </article>

            {{-- AKTIVITAS --}}
            <article class="student-card">
                <div class="student-card-top">
                    <span class="student-card-label">Aktivitas</span>

                    <div class="student-card-icon student-icon-green"
                         aria-hidden="true">
                        ▤
                    </div>
                </div>

                <h3>Dalam pengembangan</h3>

                <p class="student-card-description">
                    Ringkasan kegiatan dan progres monitoring akan tersedia
                    setelah modul aktivitas dikembangkan.
                </p>
            </article>

            {{-- LAPORAN --}}
            <article class="student-card">
                <div class="student-card-top">
                    <span class="student-card-label">Laporan</span>

                    <div class="student-card-icon student-icon-orange"
                         aria-hidden="true">
                        ▧
                    </div>
                </div>

                <h3>Dalam pengembangan</h3>

                <p class="student-card-description">
                    Informasi dan hasil laporan PDLK akan ditampilkan
                    setelah modul pelaporan tersedia.
                </p>
            </article>

        </div>
    </section>

    {{-- ALUR KEGIATAN --}}
    <section class="student-panel">

        <div class="student-section-heading">
            <div>
                <h2>Alur Kegiatan PDLK</h2>
                <p>Tahapan yang dilalui mahasiswa selama mengikuti PDLK.</p>
            </div>
        </div>

        <div class="student-flow-grid">

            <article class="student-flow-card">
                <div class="student-flow-number">1</div>
                <h3>Pendaftaran</h3>
                <p>
                    Pilih jenis kegiatan PDLK dan ajukan pendaftaran.
                </p>
            </article>

            <article class="student-flow-card">
                <div class="student-flow-number">2</div>
                <h3>Seleksi</h3>
                <p>
                    Pengelola memeriksa pendaftaran dan menentukan status.
                </p>
            </article>

            <article class="student-flow-card">
                <div class="student-flow-number">3</div>
                <h3>Pelaksanaan</h3>
                <p>
                    Jalankan kegiatan dan isi monitoring sesuai periode.
                </p>
            </article>

            <article class="student-flow-card">
                <div class="student-flow-number">4</div>
                <h3>Evaluasi</h3>
                <p>
                    Lengkapi evaluasi akhir sesuai ketentuan kegiatan PDLK.
                </p>
            </article>

        </div>

        <div class="student-flow-footer">
            <p>
                Siap mengelola kegiatan PDLK Anda?
            </p>

            <a href="{{ route('pdlk.index') }}"
               class="student-action">
                Buka Daftar PDLK →
            </a>
        </div>

    </section>

</div>

@endsection
