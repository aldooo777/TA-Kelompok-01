@extends('layouts.publik')

@section('title', 'Jenis Kegiatan PDLK')

@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative w-full h-[220px] overflow-hidden flex flex-col justify-between">
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ asset('images/kampus-del.jpg') }}');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#003580]/90 via-[#003580]/85 to-[#175eae]/85"></div>

        <div class="relative z-10 max-w-[1280px] w-full mx-auto px-margin-mobile lg:px-margin pt-6 flex flex-col items-center justify-center text-center my-auto">
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-lg bg-[#502176] text-white font-label-sm text-label-sm uppercase tracking-wider mb-2 shadow-sm">
                <i class="bi bi-star-fill text-[10px]"></i>
                <span>Program</span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-white font-bold tracking-tight mb-1.5 drop-shadow-sm">
                Jenis Kegiatan PDLK
            </h1>
            <nav class="flex items-center gap-2 font-label-md text-label-md text-[#8cc5dd]">
                <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                <span class="opacity-60">/</span>
                <span class="text-white font-semibold">Jenis PDLK</span>
            </nav>
        </div>

        <div class="relative z-10 w-full leading-none pointer-events-none select-none -mb-px">
            <svg class="w-full h-7 block" preserveAspectRatio="none" viewBox="0 0 1200 48">
                <path class="fill-[#72a9ff]/50" d="M0,20 C180,42 360,2 600,24 C840,46 1020,8 1200,28 L1200,48 L0,48 Z"></path>
                <path class="fill-white" d="M0,28 C220,6 440,38 720,18 C960,-2 1100,28 1200,22 L1200,48 L0,48 Z"></path>
            </svg>
        </div>
    </section>

    {{-- 2. PENGANTAR & STATISTIK DINAMIS --}}
    <section class="w-full bg-white py-[60px]">
        <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <span class="font-label-md text-label-md font-bold uppercase tracking-[1.5px] text-[#502176] mb-1.5">
                        Tentang PDLK
                    </span>
                    <h2 class="font-headline-lg text-headline-lg font-bold text-[#003580] mb-3">
                        Pembelajaran di Luar Kampus
                    </h2>
                    <div class="flex items-center gap-1.5 mb-6">
                        <span class="w-10 h-1.5 rounded-full bg-[#175eae]"></span>
                        <span class="w-5 h-1.5 rounded-full bg-[#502176]"></span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-justify">
                        Program Pembelajaran di Luar Kampus (PDLK) dirancang sebagai wadah bagi mahasiswa Institut Teknologi Del untuk memperoleh pengalaman belajar langsung (<em>Experiential Learning</em>) di luar perkuliahan reguler. Di bawah pengelolaan UPT PP-ESTEM, program ini memfasilitasi mahasiswa mengasah kompetensi saintifik, profesionalisme industri, riset, kewirausahaan, dan pengabdian masyarakat melalui kolaborasi nyata bersama mitra.
                    </p>
                </div>

                <div class="lg:col-span-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-surface-container-low shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-[#175eae] flex items-center justify-center text-white shadow-sm">
                                <i class="bi bi-grid-fill text-xl"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-lg text-headline-lg font-extrabold text-[#003580] leading-tight">{{ $jenisKegiatan->count() }}</span>
                                <span class="font-label-md text-label-md text-on-surface-variant font-medium">Jenis Kegiatan</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant text-[24px]">verified</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-surface-container-low shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-[#502176] flex items-center justify-center text-white shadow-sm">
                                <i class="bi bi-layers-fill text-xl"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-lg text-headline-lg font-extrabold text-[#502176] leading-tight">2</span>
                                <span class="font-label-md text-label-md text-on-surface-variant font-medium">Skema PDLK</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant text-[24px]">route</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-surface-container-low shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-[#175eae] flex items-center justify-center text-white shadow-sm">
                                <i class="bi bi-building-check text-xl"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-lg text-headline-lg font-extrabold text-[#003580] leading-tight">{{ $jumlahMitra }}</span>
                                <span class="font-label-md text-label-md text-on-surface-variant font-medium">Mitra Aktif</span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-outline-variant text-[24px]">handshake</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. SKEMA PELAKSANAAN PDLK --}}
    <section class="w-full bg-surface-container py-[60px]">
        <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin">
            <div class="flex flex-col items-center text-center mb-10">
                <span class="font-label-md text-label-md font-bold uppercase tracking-[1.5px] text-[#502176] mb-1.5">
                    Mekanisme Program
                </span>
                <h2 class="font-headline-lg text-headline-lg font-bold text-[#003580] mb-3">
                    Skema Pelaksanaan PDLK
                </h2>
                <div class="flex items-center gap-1.5">
                    <span class="w-10 h-1.5 rounded-full bg-[#175eae]"></span>
                    <span class="w-5 h-1.5 rounded-full bg-[#502176]"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter items-stretch">
                {{-- Kartu 1: Flagship --}}
                <div class="flex flex-col h-full bg-white rounded-xl overflow-hidden shadow-sm transition-all duration-200 hover:-translate-y-1">
                    <div class="bg-[#175eae] text-white p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white text-[#175eae] flex items-center justify-center font-bold text-lg shadow-sm">
                                <i class="bi bi-flag-fill"></i>
                            </div>
                            <h3 class="font-headline-md text-headline-md font-bold text-white">PDLK Flagship</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-white/20 text-white font-label-sm text-label-sm uppercase tracking-wide">
                            Nasional
                        </span>
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <p class="font-body-md text-body-md text-on-surface-variant mb-6 leading-relaxed">
                                Program yang diinisiasi langsung oleh Kementerian Pendidikan Tinggi, Sains, dan Teknologi (Kemdiktisaintek) atau bersinergi dengan kementerian/lembaga nasional melalui platform resmi Kampus Berdampak.
                            </p>
                            <ul class="space-y-3.5 list-none p-0 m-0">
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#175eae] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Diinisiasi langsung oleh Kemdiktisaintek dan kementerian/lembaga nasional.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#175eae] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Tidak memerlukan dokumen kerja sama khusus antara institut dan mitra (MoU/MoA terpusat).</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#175eae] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Konversi SKS sesuai ketentuan program nasional dan kurikulum prodi.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-outline-variant/20 flex items-center justify-between text-[#175eae] font-label-md text-label-md">
                            <span class="flex items-center gap-1.5"><i class="bi bi-shield-check"></i> Standardisasi Pusat</span>
                            <span class="text-on-surface-variant font-body-sm text-body-sm">Pendaftaran Terpusat</span>
                        </div>
                    </div>
                </div>

                {{-- Kartu 2: Mandiri --}}
                <div class="flex flex-col h-full bg-white rounded-xl overflow-hidden shadow-sm transition-all duration-200 hover:-translate-y-1">
                    <div class="bg-[#502176] text-white p-5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white text-[#502176] flex items-center justify-center font-bold text-lg shadow-sm">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <h3 class="font-headline-md text-headline-md font-bold text-white">PDLK Mandiri</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-white/20 text-white font-label-sm text-label-sm uppercase tracking-wide">
                            Kemitraan Institut
                        </span>
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <p class="font-body-md text-body-md text-on-surface-variant mb-6 leading-relaxed">
                                Program yang diinisiasi secara spesifik oleh Program Studi dan Institut Teknologi Del bersama mitra strategis dunia industri dan lembaga riset untuk menyelaraskan capaian lulusan.
                            </p>
                            <ul class="space-y-3.5 list-none p-0 m-0">
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#502176] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Diinisiasi langsung oleh Program Studi dan Sivitas Institut Teknologi Del.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#502176] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Memerlukan dokumen kerja sama resmi (MoU, MoA, atau IA) dengan mitra tujuan.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="bi bi-check-circle-fill text-[#502176] text-base mt-0.5 shrink-0"></i>
                                    <span class="font-body-md text-body-md text-on-surface">Pendanaan fleksibel bersumber dari mahasiswa, sponsor industri, atau beasiswa mitra.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-outline-variant/20 flex items-center justify-between text-[#502176] font-label-md text-label-md">
                            <span class="flex items-center gap-1.5"><i class="bi bi-file-earmark-text"></i> MoA / IA Institusional</span>
                            <span class="text-on-surface-variant font-body-sm text-body-sm">Pendaftaran Kampus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. DAFTAR JENIS KEGIATAN PDLK (Dinamis dari CRUD tabel activity_types) --}}
    <section class="w-full bg-white py-[65px]">
        <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin">
            <div class="flex flex-col items-center text-center mb-12">
                <span class="font-label-md text-label-md font-bold uppercase tracking-[1.5px] text-[#502176] mb-1.5">
                    {{ $jenisKegiatan->count() }} Bentuk Kegiatan
                </span>
                <h2 class="font-headline-lg text-headline-lg font-bold text-[#003580] mb-3">
                    Pilih Jenis Kegiatan PDLK
                </h2>
                <div class="flex items-center gap-1.5 mb-3">
                    <span class="w-10 h-1.5 rounded-full bg-[#175eae]"></span>
                    <span class="w-5 h-1.5 rounded-full bg-[#502176]"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                    Eksplorasi ragam pilihan pembelajaran lapangan yang diakui dan dapat dikonversikan ke dalam kurikulum akademik IT Del.
                </p>
            </div>

            @if ($jenisKegiatan->isEmpty())
                <div class="bg-surface-container-low rounded-xl p-8 text-center text-on-surface-variant">
                    Belum ada jenis kegiatan PDLK yang ditambahkan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter items-stretch">
                    @foreach ($jenisKegiatan as $jenis)
                        @php
                            $isGanjil = $loop->iteration % 2 === 1;
                            $badgeBg = $isGanjil ? '#175eae' : '#502176';
                            $ikon = match ($jenis->slug) {
                                'magang' => 'bi-briefcase',
                                'kerja-praktik' => 'bi-tools',
                                'penelitian' => 'bi-search',
                                'pertukaran-pelajar' => 'bi-arrow-left-right',
                                'kkn' => 'bi-house-heart',
                                'studi-independen' => 'bi-lightbulb',
                                'kewirausahaan' => 'bi-shop',
                                default => 'bi-journal-bookmark',
                            };
                            // Jika sisa 1 kartu di baris terakhir (misal jumlah = 7), posisikan di tengah pada layar lg
                            $centerLast = ($loop->last && $loop->count % 3 === 1) ? 'lg:col-start-2 md:col-span-2 lg:col-span-1' : '';
                        @endphp

                        <article class="flex flex-col h-full bg-white rounded-xl overflow-hidden shadow-sm border border-outline-variant/20 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md relative group {{ $centerLast }}">
                            <div class="h-1 w-full bg-[#502176]"></div>

                            <div class="relative w-full aspect-video overflow-hidden bg-gradient-to-br from-[#003580] to-[#1a81c2] flex items-center justify-center">
                                @if ($jenis->image)
                                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                         src="{{ file_url($jenis->image) }}" alt="{{ $jenis->name }}" loading="lazy">
                                @else
                                    <i class="bi {{ $ikon }} text-white/25 text-6xl group-hover:scale-110 transition-transform duration-300"></i>
                                @endif

                                <div class="absolute bottom-3 right-4 w-11 h-11 rounded-full text-white flex items-center justify-center shadow-md"
                                     style="background-color: {{ $badgeBg }};">
                                    <i class="bi {{ $ikon }} text-lg"></i>
                                </div>
                            </div>

                            <div class="p-6 flex flex-col justify-between flex-grow">
                                <div>
                                    <h3 class="font-headline-sm text-headline-sm font-bold text-[#003580] mb-2 group-hover:text-[#175eae] transition-colors">
                                        {{ $jenis->name }}
                                    </h3>
                                    <p class="font-body-md text-body-md text-on-surface-variant mb-4 leading-relaxed">
                                        {{ $jenis->summary ?: ringkas($jenis->description ?? '', 140) }}
                                    </p>

                                    @if ($jenis->description || $jenis->objectives)
                                        <details class="mb-4 text-body-sm text-on-surface-variant bg-surface-container-low rounded-lg p-3">
                                            <summary class="cursor-pointer font-semibold text-[#003580]">Detail &amp; Tujuan Kegiatan</summary>
                                            <div class="mt-2 space-y-2 leading-relaxed">
                                                @if ($jenis->description)
                                                    <p>{{ $jenis->description }}</p>
                                                @endif
                                                @if ($jenis->objectives)
                                                    <p><strong class="text-[#003580]">Tujuan:</strong> {{ $jenis->objectives }}</p>
                                                @endif
                                            </div>
                                        </details>
                                    @endif
                                </div>

                                <div>
                                    @if ($jenis->duration_text)
                                        <div class="flex items-center gap-2 mb-4 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                                                <i class="bi bi-clock" style="color: {{ $badgeBg }};"></i> Durasi: {{ $jenis->duration_text }}
                                            </span>
                                        </div>
                                    @endif

                                    <a class="inline-flex items-center gap-1.5 font-label-md text-label-md font-bold text-[#003580] hover:text-[#175eae] transition-colors"
                                       href="{{ route('jenis-pdlk.show', $jenis->slug) }}">
                                        <span>Lihat Detail</span>
                                        <i class="bi bi-arrow-right text-sm transition-transform group-hover:translate-x-1"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 5. PITA AJAKAN (CTA BANNER) --}}
    <section class="w-full bg-gradient-to-r from-[#39025f] to-[#502176] text-white py-12 relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-[#72a9ff]/10 pointer-events-none"></div>

        <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin relative z-10">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex flex-col items-center lg:items-start text-center lg:text-left max-w-2xl">
                    <span class="px-3 py-1 rounded bg-white/15 font-label-sm text-label-sm uppercase tracking-wider text-[#d6e3ff] mb-2">
                        Kesempatan Belajar
                    </span>
                    <h2 class="font-headline-lg text-headline-lg font-bold text-white mb-2">
                        Temukan Lowongan Sesuai Minatmu
                    </h2>
                    <p class="font-body-md text-body-md text-[#e0b7ff] leading-relaxed">
                        Daftarkan diri Anda pada beragam posisi magang, riset, dan proyek kolaboratif bersama mitra terpercaya Institut Teknologi Del.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a class="inline-flex items-center gap-2.5 px-6 py-3 rounded-lg bg-white text-[#39025f] font-label-lg text-label-lg font-bold shadow-md hover:bg-surface-container hover:scale-105 transition-all"
                       href="{{ route('lowongan.index') }}">
                        <i class="bi bi-briefcase-fill text-lg"></i>
                        <span>Lihat Lowongan</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
