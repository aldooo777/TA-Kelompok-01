
@extends('layouts.pdlk')

@section('title', 'Monitoring PDLK | Monitoring PDLK')
@section('breadcrumb', 'Monitoring / Pilih Kegiatan')
@section('page-title', 'Pilih Kegiatan Monitoring')

@section('content')

@php
    $kegiatan = [
        [
            'slug' => 'magang',
            'nama' => 'Magang',
            'ikon' => '💼',
            'deskripsi' => 'Pantau perkembangan dan evaluasi kegiatan magang.',
        ],
        [
            'slug' => 'kerja-praktik',
            'nama' => 'Kerja Praktik',
            'ikon' => '🏢',
            'deskripsi' => 'Pantau pelaksanaan kerja praktik sesuai bidang studi.',
        ],
        [
            'slug' => 'penelitian',
            'nama' => 'Penelitian',
            'ikon' => '🔬',
            'deskripsi' => 'Pantau kemajuan dan hasil kegiatan penelitian.',
        ],
        [
            'slug' => 'pertukaran-pelajar',
            'nama' => 'Pertukaran Pelajar',
            'ikon' => '🌐',
            'deskripsi' => 'Pantau kegiatan pembelajaran di institusi lain.',
        ],
        [
            'slug' => 'kkn',
            'nama' => 'Kuliah Kerja Nyata (KKN)',
            'ikon' => '🤝',
            'deskripsi' => 'Pantau kegiatan pengabdian kepada masyarakat.',
        ],
        [
            'slug' => 'studi-independen',
            'nama' => 'Studi Independen',
            'ikon' => '📚',
            'deskripsi' => 'Pantau pembelajaran dan proyek mandiri.',
        ],
        [
            'slug' => 'kewirausahaan',
            'nama' => 'Kewirausahaan',
            'ikon' => '💡',
            'deskripsi' => 'Pantau kegiatan dan pengembangan usaha.',
        ],
    ];
@endphp

<style>
    .monitoring-choice-page {
        --choice-blue: #348db8;
        --choice-blue-dark: #287ba5;
        --choice-green: #00a65a;
        --choice-green-dark: #008d4c;
        --choice-text: #26364d;
        --choice-muted: #718096;
        --choice-border: #e1e7ef;

        color: var(--choice-text);
    }

    .monitoring-choice-page,
    .monitoring-choice-page * {
        box-sizing: border-box;
    }

    /* Pengantar */
    .monitoring-choice-intro {
        margin-bottom: 24px;
    }

    .monitoring-choice-intro h2 {
        margin: 0 0 7px;
        color: var(--choice-text);
        font-size: 21px;
        font-weight: 600;
        line-height: 1.5;
    }

    .monitoring-choice-intro p {
        margin: 0;
        color: var(--choice-muted);
        font-size: 13px;
        line-height: 1.8;
    }

    /* Grid kegiatan */
    .monitoring-choice-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        align-items: stretch;
        gap: 16px;
    }

    /* Kartu kegiatan */
    .monitoring-choice-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
        min-height: 245px;
        padding: 20px;
        border: 1px solid var(--choice-border);
        border-radius: 5px;
        background: #fff;
        cursor: default;
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .monitoring-choice-card:hover {
        border-color: #c9dce9;
        box-shadow: 0 4px 12px rgba(38, 65, 95, .05);
    }

    .monitoring-choice-icon {
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

    .monitoring-choice-card h3 {
        margin: 0 0 9px;
        color: var(--choice-text);
        font-size: 15px;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .monitoring-choice-card p {
        flex: 1;
        width: 100%;
        margin: 0 0 20px;
        color: var(--choice-muted);
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    /* Tombol dua tahap monitoring */
    .monitoring-choice-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        width: 100%;
        gap: 8px;
        margin-top: auto;
    }

    .monitoring-choice-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        min-height: 40px;
        padding: 8px 10px;
        border: 1px solid transparent;
        border-radius: 3px;
        color: #fff;
        font-family: inherit;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        overflow-wrap: anywhere;
        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .monitoring-choice-button:hover {
        color: #fff;
        text-decoration: none;
    }

    .monitoring-choice-button:focus-visible {
        outline: 3px solid #9be7bf;
        outline-offset: 3px;
    }

    .monitoring-choice-mid {
        border-color: var(--choice-green);
        background: var(--choice-green);
    }

    .monitoring-choice-mid:hover {
        border-color: var(--choice-green-dark);
        background: var(--choice-green-dark);
    }

    .monitoring-choice-end {
        border-color: var(--choice-blue);
        background: var(--choice-blue);
    }

    .monitoring-choice-end:hover {
        border-color: var(--choice-blue-dark);
        background: var(--choice-blue-dark);
    }

    /* Informasi singkat */
    .monitoring-choice-note {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        margin-top: 22px;
        padding: 13px 15px;
        border-left: 4px solid var(--choice-blue);
        border-radius: 3px;
        background: #e8f3f9;
        color: #506477;
        font-size: 12px;
        line-height: 1.8;
    }

    .monitoring-choice-note-icon {
        flex: 0 0 auto;
        color: var(--choice-blue);
        font-size: 16px;
    }

    .monitoring-choice-note p {
        margin: 0;
    }

    /* Tablet */
    @media (max-width: 1000px) {
        .monitoring-choice-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Ponsel */
    @media (max-width: 600px) {
        .monitoring-choice-intro h2 {
            font-size: 19px;
        }

        .monitoring-choice-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        .monitoring-choice-card {
            min-height: 0;
            padding: 18px;
        }

        .monitoring-choice-card p {
            margin-bottom: 18px;
        }
    }

    @media (max-width: 380px) {
        .monitoring-choice-actions {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .monitoring-choice-card,
        .monitoring-choice-button {
            transition: none;
        }
    }
</style>

<section class="panel monitoring-choice-page">

    <div class="monitoring-choice-intro">
        <h2>Pilih Kegiatan untuk Monitoring</h2>

        <p>
            Pilih jenis PDLK dan tahap monitoring yang ingin Anda isi.
            Gunakan monitoring pertengahan untuk progres kegiatan dan
            monitoring akhir untuk evaluasi.
        </p>
    </div>

    <div class="monitoring-choice-grid">

        @foreach ($kegiatan as $item)
            <article class="monitoring-choice-card">

                <div class="monitoring-choice-icon" aria-hidden="true">
                    {{ $item['ikon'] }}
                </div>

                <h3>{{ $item['nama'] }}</h3>

                <p>{{ $item['deskripsi'] }}</p>

                <div class="monitoring-choice-actions">

                    <a
                        class="monitoring-choice-button monitoring-choice-mid"
                        href="{{ route('monitoring.pertengahan', ['jenis' => $item['slug']]) }}"
                        aria-label="Monitoring pertengahan {{ $item['nama'] }}"
                    >
                        Pertengahan →
                    </a>

                    <a
                        class="monitoring-choice-button monitoring-choice-end"
                        href="{{ route('monitoring.akhir', ['jenis' => $item['slug']]) }}"
                        aria-label="Monitoring akhir {{ $item['nama'] }}"
                    >
                        Akhir →
                    </a>

                </div>

            </article>
        @endforeach

    </div>

    <div class="monitoring-choice-note">
        <span class="monitoring-choice-note-icon" aria-hidden="true">ⓘ</span>

        <p>
            Pilih kegiatan yang sesuai dengan PDLK yang sedang Anda jalani,
            kemudian pilih tahap monitoring.
        </p>
    </div>

</section>

@endsection
