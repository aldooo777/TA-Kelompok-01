
@extends('layouts.pdlk')

@section('title', 'Daftar PDLK | Monitoring PDLK')
@section('breadcrumb', 'Daftar PDLK')
@section('page-title', 'Daftar PDLK')

@section('content')

@php
    $kegiatan = [
        [
            'slug' => 'magang',
            'nama' => 'Magang',
            'ikon' => '💼',
            'deskripsi' => 'Mengikuti kegiatan magang di perusahaan atau instansi.',
        ],
        [
            'slug' => 'kerja-praktik',
            'nama' => 'Kerja Praktik',
            'ikon' => '🏢',
            'deskripsi' => 'Melaksanakan kerja praktik sesuai bidang studi dan kebutuhan program.',
        ],
        [
            'slug' => 'penelitian',
            'nama' => 'Penelitian',
            'ikon' => '🔬',
            'deskripsi' => 'Melaksanakan penelitian untuk mendukung pengembangan ilmu pengetahuan.',
        ],
        [
            'slug' => 'pertukaran-pelajar',
            'nama' => 'Pertukaran Pelajar',
            'ikon' => '🌐',
            'deskripsi' => 'Mengikuti program pembelajaran di institusi atau perguruan tinggi lain.',
        ],
        [
            'slug' => 'kkn',
            'nama' => 'Kuliah Kerja Nyata (KKN)',
            'ikon' => '🤝',
            'deskripsi' => 'Melaksanakan kegiatan pengabdian dan pembelajaran bersama masyarakat.',
        ],
        [
            'slug' => 'studi-independen',
            'nama' => 'Studi Independen',
            'ikon' => '📚',
            'deskripsi' => 'Mengikuti pembelajaran atau proyek secara mandiri sesuai tujuan kegiatan.',
        ],
        [
            'slug' => 'kewirausahaan',
            'nama' => 'Kewirausahaan',
            'ikon' => '💡',
            'deskripsi' => 'Mengembangkan ide bisnis dan menjalankan kegiatan kewirausahaan.',
        ],
    ];
@endphp

<style>
    .pdlk-page {
        --pdlk-text: #26364d;
        --pdlk-muted: #718096;
        --pdlk-border: #e1e7ef;
        --pdlk-blue: #348db8;
        --pdlk-green: #00a65a;
        color: var(--pdlk-text);
    }

    .pdlk-page,
    .pdlk-page * {
        box-sizing: border-box;
    }

    /* Peringatan */
    .pdlk-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 24px;
        padding: 14px 16px;
        border: 1px solid #f2d28b;
        border-left: 4px solid #e5a000;
        border-radius: 4px;
        background: #fff8e7;
        color: #775719;
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .pdlk-alert-icon {
        flex: 0 0 auto;
        font-size: 18px;
    }

    .pdlk-alert strong {
        display: block;
        margin-bottom: 3px;
        color: #674910;
    }

    /* Pengantar halaman */
    .pdlk-intro {
        margin-bottom: 24px;
    }

    .pdlk-intro h2 {
        margin: 0 0 7px;
        color: var(--pdlk-text);
        font-size: 21px;
        font-weight: 600;
        line-height: 1.5;
    }

    .pdlk-intro p {
        margin: 0;
        color: var(--pdlk-muted);
        font-size: 13px;
        line-height: 1.8;
    }

    /* Grid kartu kegiatan */
    .pdlk-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        align-items: stretch;
    }

    /* Kartu hanya sebagai wadah informasi */
    .pdlk-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        min-height: 225px;
        padding: 20px;
        border: 1px solid var(--pdlk-border);
        border-radius: 5px;
        background: #fff;
        cursor: default;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .pdlk-card:hover {
        border-color: #c9dce9;
        box-shadow: 0 4px 12px rgba(38, 65, 95, .05);
    }

    .pdlk-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 44px;
        width: 44px;
        height: 44px;
        margin-bottom: 15px;
        border-radius: 4px;
        background: #e7f3fa;
        font-size: 23px;
    }

    .pdlk-card h3 {
        margin: 0 0 9px;
        color: var(--pdlk-text);
        font-size: 15px;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .pdlk-card p {
        flex: 1;
        width: 100%;
        margin: 0 0 20px;
        color: var(--pdlk-muted);
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    /* Tombol pilih kegiatan */
    .pdlk-card-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        align-self: flex-start;
        gap: 6px;
        min-height: 38px;
        max-width: 100%;
        margin-top: auto;
        padding: 9px 12px;
        border: 1px solid var(--pdlk-green);
        border-radius: 3px;
        background: var(--pdlk-green);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        overflow-wrap: anywhere;
        transition: background .15s ease, border-color .15s ease;
    }

    .pdlk-card-action:hover {
        border-color: #008d4c;
        background: #008d4c;
        color: #fff;
        text-decoration: none;
    }

    .pdlk-card-action:focus-visible {
        outline: 3px solid #9be7bf;
        outline-offset: 3px;
    }

    /* Tablet */
    @media (max-width: 1000px) {
        .pdlk-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Ponsel */
    @media (max-width: 600px) {
        .pdlk-intro h2 {
            font-size: 19px;
        }

        .pdlk-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        .pdlk-card {
            min-height: 0;
            padding: 18px;
        }

        .pdlk-card p {
            margin-bottom: 18px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .pdlk-card,
        .pdlk-card-action {
            transition: none;
        }
    }
</style>

<section class="panel pdlk-page">

    {{-- Notifikasi --}}
    @if (session('warning'))
        <div class="pdlk-alert" role="alert">
            <span class="pdlk-alert-icon" aria-hidden="true">⚠️</span>

            <div>
                <strong>Informasi Pendaftaran PDLK</strong>
                {{ session('warning') }}
            </div>
        </div>
    @endif

    {{-- Pengantar --}}
    <div class="pdlk-intro">
        <h2>Pilih Jenis Kegiatan PDLK</h2>

        <p>
            Pilih kegiatan Pembelajaran di Luar Kampus yang akan Anda ikuti
            untuk melanjutkan proses pendaftaran.
        </p>
    </div>

    {{-- Pilihan kegiatan --}}
    <div class="pdlk-grid">

        @foreach ($kegiatan as $item)
            <article class="pdlk-card">

                <div class="pdlk-icon" aria-hidden="true">
                    {{ $item['ikon'] }}
                </div>

                <h3>{{ $item['nama'] }}</h3>

                <p>{{ $item['deskripsi'] }}</p>

                <a
                    href="{{ route('pdlk.pilih', ['jenis' => $item['slug']]) }}"
                    class="pdlk-card-action"
                    aria-label="Pilih kegiatan {{ $item['nama'] }}"
                >
                    Pilih Kegiatan <span aria-hidden="true">→</span>
                </a>

            </article>
        @endforeach

    </div>

</section>

@endsection
