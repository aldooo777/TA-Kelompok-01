@extends('layouts.publik')

@section('title', 'Struktur Organisasi')

@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative w-full overflow-hidden bg-cover bg-center"
             style="background-image: url('{{ asset('images/kampus-del.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-r from-[#003580]/90 via-[#003580]/85 to-[#0055A5]/80 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-margin-mobile md:px-margin pt-12 pb-16 flex flex-col items-center justify-center text-center z-10">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded bg-[#46166c] text-white text-label-sm font-label-sm tracking-wider uppercase shadow-sm">
                <i class="bi bi-diagram-3 text-[12px]"></i> PROFIL
            </span>
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-white font-bold mt-3 mb-2 tracking-tight">
                Struktur Organisasi
            </h1>
            <nav aria-label="Breadcrumb" class="flex items-center justify-center gap-2 font-label-sm text-label-sm text-[#8cc5dd]">
                <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                <span class="text-white/40">/</span>
                <span class="text-white/70">Profil</span>
                <span class="text-white/40">/</span>
                <span class="text-white font-semibold">Struktur Organisasi</span>
            </nav>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-[1px]">
            <svg class="w-full h-8 md:h-12 block" fill="none" preserveAspectRatio="none" viewBox="0 0 1440 64" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 32C240 10 480 54 720 32C960 10 1200 54 1440 32V64H0V32Z" fill="#8cc5dd" fill-opacity="0.45"></path>
                <path d="M0 44C260 22 520 60 760 40C1020 20 1260 56 1440 44V64H0V44Z" fill="#FFFFFF"></path>
            </svg>
        </div>
    </section>

    {{-- 2. NAVIGASI SUB-HALAMAN --}}
    <section class="w-full bg-[#FFFFFF] py-6 shadow-sm">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin flex items-center justify-center gap-3 md:gap-4 flex-wrap">
            <a class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-label-md text-label-md font-semibold text-[#003580] bg-transparent hover:bg-[#003580] hover:text-white transition-all border-2 border-[#003580]"
               href="{{ route('tentang') }}">
                <i class="bi bi-compass"></i>
                <span>Visi, Misi &amp; Tujuan</span>
            </a>
            <a aria-current="page"
               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full font-label-md text-label-md font-semibold text-white bg-[#003580] shadow-md transition-all"
               href="{{ route('struktur') }}">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Struktur Organisasi</span>
            </a>
        </div>
    </section>

    {{-- 3. BAGAN ORGANISASI --}}
    <section class="w-full bg-[#FFFFFF] py-12 md:py-16" id="bagan">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col items-center text-center mb-10">
                <span class="font-label-sm text-label-sm font-bold tracking-wider text-[#46166c] uppercase mb-1">
                    BAGAN ORGANISASI
                </span>
                <h2 class="font-headline-xl text-headline-xl-mobile md:text-headline-xl text-[#003580] font-bold">
                    Struktur Organisasi UPT PP-ESTEM di Institut Teknologi Del
                </h2>
                <div class="flex items-center gap-1.5 mt-3 justify-center">
                    <div class="w-10 h-1 rounded-full bg-[#1a81c2]"></div>
                    <div class="w-5 h-1 rounded-full bg-[#46166c]"></div>
                </div>
            </div>

            <div class="w-full bg-[#FFFFFF] rounded-2xl shadow-md p-4 md:p-8 overflow-hidden border border-outline-variant/20">
                <div class="w-full overflow-x-auto pb-4">
                    <div class="min-w-[960px] flex flex-col items-center">

                        {{-- TINGKAT 1: REKTOR & KETUA LPPM --}}
                        <div class="relative w-full flex justify-center items-center py-2">
                            <div class="w-full grid grid-cols-12 items-center">
                                <div class="col-start-4 col-span-6 flex justify-center">
                                    <div class="w-80 bg-[#003580] text-white p-3.5 rounded-xl shadow text-center">
                                        <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white/10 mb-1 text-white">
                                            <i class="bi bi-bank"></i>
                                        </div>
                                        <div class="font-label-lg text-label-lg font-bold">Rektor</div>
                                        <div class="font-body-sm text-body-sm text-[#8cc5dd] font-medium leading-snug mt-0.5">
                                            Prof. Dr. Arnaldo Marulitua Sinaga, S.T., M.InfoTech.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-start-10 col-span-3 flex justify-end">
                                    <div class="relative w-64 bg-[#664780] text-white p-3 rounded-xl shadow text-center">
                                        <div class="font-label-md text-label-md font-bold flex items-center justify-center gap-1.5">
                                            <i class="bi bi-journal-code"></i> Ketua LPPM
                                        </div>
                                        <div class="font-body-sm text-body-sm text-white/90 leading-tight mt-0.5">
                                            Dr. Rosni Lumbantoruan, M.ISD.
                                        </div>
                                        <span class="inline-block mt-1 font-label-sm text-[10px] text-white/80 uppercase tracking-wider bg-white/10 px-2 py-0.5 rounded">
                                            Unit Penelitian &amp; Pengabdian
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-0.5 h-6 bg-[#003580]"></div>

                        <div class="w-[740px] h-0.5 bg-[#003580] relative">
                            <div class="absolute left-0 top-0 w-0.5 h-5 bg-[#003580]"></div>
                            <div class="absolute left-1/2 -translate-x-1/2 top-0 w-0.5 h-5 bg-[#003580]"></div>
                            <div class="absolute right-0 top-0 w-0.5 h-5 bg-[#003580]"></div>
                        </div>

                        {{-- TINGKAT 2: 3 WAKIL REKTOR --}}
                        <div class="w-full flex justify-center gap-6 pt-5 pb-2">
                            <div class="w-64 bg-[#0055A5] text-white p-3.5 rounded-lg shadow-sm text-center">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-[#8cc5dd] mb-1"></span>
                                <div class="font-label-md text-label-md font-bold leading-tight">
                                    Wakil Rektor I
                                </div>
                                <div class="text-[11px] text-white/90 mt-0.5">Bidang Akademik dan Kemahasiswaan</div>
                                <div class="font-body-sm text-body-sm text-[#d6e3ff] font-medium mt-1.5 leading-snug">
                                    Good Fried Panggabean, S.T., M.T., Ph.D.
                                </div>
                            </div>

                            <div class="w-64 bg-[#0055A5] text-white p-3.5 rounded-lg shadow-sm text-center opacity-95">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-white/40 mb-1"></span>
                                <div class="font-label-md text-label-md font-bold leading-tight">
                                    Wakil Rektor II
                                </div>
                                <div class="text-[11px] text-white/90 mt-0.5">Bidang Perencanaan, Keuangan, dan Sumber Daya</div>
                                <div class="font-body-sm text-body-sm text-[#d6e3ff] font-medium mt-1.5 leading-snug">
                                    Dr. Mariana Simanjuntak, S.S., M.Sc.
                                </div>
                            </div>

                            <div class="w-64 bg-[#0055A5] text-white p-3.5 rounded-lg shadow-sm text-center opacity-95">
                                <span class="inline-block w-2.5 h-2.5 rounded-full bg-white/40 mb-1"></span>
                                <div class="font-label-md text-label-md font-bold leading-tight">
                                    Wakil Rektor III
                                </div>
                                <div class="text-[11px] text-white/90 mt-0.5">Bidang Penelitian, Pengabdian, Kemitraan, Inovasi, dan Usaha</div>
                                <div class="font-body-sm text-body-sm text-[#d6e3ff] font-medium mt-1.5 leading-snug">
                                    Ellyas Alga Nainggolan, S.TP., M.Sc., Ph.D.
                                </div>
                            </div>
                        </div>

                        {{-- GARIS KOMANDO WR I KE UPT PP-ESTEM --}}
                        <div class="w-full flex justify-center">
                            <div class="w-[740px] relative h-9">
                                <div class="absolute left-[88px] top-0 w-0.5 h-9 bg-[#003580]"></div>
                                <div class="absolute left-[83px] bottom-0 text-[#003580] text-[12px] leading-none">
                                    <i class="bi bi-caret-down-fill"></i>
                                </div>
                            </div>
                        </div>

                        {{-- TINGKAT 3: LINGKUP UPT PP-ESTEM --}}
                        <div class="relative w-full max-w-4xl bg-[#46166c]/[0.03] rounded-2xl p-6 shadow-inner border border-[#46166c]/20">
                            <div class="flex items-center justify-between mb-6 pb-2">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-[#46166c] text-white font-label-md text-label-md font-bold tracking-wide">
                                    <i class="bi bi-diagram-2"></i> LINGKUP KERJA UPT PP-ESTEM
                                </div>
                                <span class="font-label-sm text-label-sm text-[#46166c] font-semibold bg-white px-2.5 py-1 rounded shadow-sm">
                                    Unit Pelayanan Teknis · Institut Teknologi Del
                                </span>
                            </div>

                            <div class="flex flex-col items-center">
                                {{-- Kepala UPT PP-ESTEM --}}
                                <div class="w-80 bg-[#46166c] text-white p-4 rounded-xl shadow-md text-center">
                                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/15 mb-1.5 text-white">
                                        <i class="bi bi-person-badge-fill text-[18px]"></i>
                                    </div>
                                    <div class="font-headline-sm text-headline-sm font-bold">Kepala UPT PP-ESTEM</div>
                                    <div class="font-label-md text-label-md text-[#8cc5dd] font-medium mt-0.5">
                                        Monalisa Pasaribu, SS., M.Ed (TESOL)
                                    </div>
                                </div>

                                <div class="w-0.5 h-6 bg-[#003580]"></div>

                                {{-- Cabang Kiri: Administrasi (sesuai bagan resmi) --}}
                                <div class="relative w-[680px] grid grid-cols-3 items-center">
                                    <div class="flex items-center justify-end pr-0 relative">
                                        <div class="w-48 bg-white p-3 rounded-lg shadow-sm text-center border border-[#1a81c2]/30 z-10">
                                            <div class="font-label-md text-label-md font-bold text-[#003580] flex items-center justify-center gap-1.5">
                                                <i class="bi bi-folder-fill text-[#1a81c2]"></i> Administrasi
                                            </div>
                                            <div class="text-[11px] text-[#747782] font-label-sm mt-0.5">Dukungan Operasional UPT</div>
                                        </div>
                                        <div class="flex-1 h-0.5 bg-[#1a81c2]"></div>
                                    </div>

                                    <div class="flex flex-col items-center justify-center relative">
                                        <div class="w-0.5 h-8 bg-[#003580]"></div>
                                        <div class="w-3 h-3 rounded-full bg-[#003580] absolute top-1/2 -translate-y-1/2"></div>
                                    </div>

                                    <div></div>
                                </div>

                                <div class="w-0.5 h-6 bg-[#003580]"></div>

                                {{-- Trunk Horizontal 3 Divisi --}}
                                <div class="w-[660px] h-0.5 bg-[#003580] relative">
                                    <div class="absolute left-0 top-0 w-0.5 h-5 bg-[#003580]"></div>
                                    <div class="absolute left-1/2 -translate-x-1/2 top-0 w-0.5 h-5 bg-[#003580]"></div>
                                    <div class="absolute right-0 top-0 w-0.5 h-5 bg-[#003580]"></div>
                                </div>

                                {{-- TIGA DIVISI SEJAJAR (tanpa nama dulu) --}}
                                <div class="w-full flex justify-between gap-4 pt-5">
                                    <div class="w-64 bg-[#1a81c2] text-white p-4 rounded-lg shadow-sm text-center">
                                        <div class="w-8 h-8 rounded-full bg-white/20 inline-flex items-center justify-center mb-1.5">
                                            <i class="bi bi-mortarboard-fill text-[15px]"></i>
                                        </div>
                                        <div class="font-label-md text-label-md font-bold leading-tight">
                                            (Kepala) Divisi Pelatihan
                                        </div>
                                        <span class="inline-block mt-1.5 font-label-sm text-[10px] text-white/85 bg-white/10 px-2 py-0.5 rounded">
                                            Pelatihan Internal &amp; Eksternal
                                        </span>
                                    </div>

                                    <div class="w-64 bg-[#1a81c2] text-white p-4 rounded-lg shadow-sm text-center">
                                        <div class="w-8 h-8 rounded-full bg-white/20 inline-flex items-center justify-center mb-1.5">
                                            <i class="bi bi-lightbulb-fill text-[15px]"></i>
                                        </div>
                                        <div class="font-label-md text-label-md font-bold leading-tight">
                                            (Kepala) Divisi Penelitian
                                        </div>
                                        <span class="inline-block mt-1.5 font-label-sm text-[10px] text-white/85 bg-white/10 px-2 py-0.5 rounded">
                                            Koordinasi LPPM
                                        </span>
                                    </div>

                                    <div class="w-64 bg-[#1a81c2] text-white p-4 rounded-lg shadow-sm text-center">
                                        <div class="w-8 h-8 rounded-full bg-white/20 inline-flex items-center justify-center mb-1.5">
                                            <i class="bi bi-briefcase-fill text-[15px]"></i>
                                        </div>
                                        <div class="font-label-md text-label-md font-bold leading-tight">
                                            (Kepala) Divisi Kerjasama
                                        </div>
                                        <span class="inline-block mt-1.5 font-label-sm text-[10px] text-white/85 bg-white/10 px-2 py-0.5 rounded">
                                            Koordinasi Mitra &amp; LPPM
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="w-full max-w-4xl mt-4 px-4 flex items-center justify-end">
                            <div class="flex items-center gap-2 bg-[#664780]/10 px-3 py-1.5 rounded-lg text-body-sm font-body-sm text-[#46166c]">
                                <i class="bi bi-arrow-down-left-square-fill text-[16px] text-[#664780]"></i>
                                <span>Garis koordinasi formal LPPM terhubung langsung ke Divisi Penelitian &amp; Kerjasama</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- LEGENDA BAGAN --}}
                <div class="mt-8 pt-5 bg-[#fbf9f8] p-4 rounded-xl flex flex-wrap items-center justify-around gap-4 text-body-sm font-body-sm text-[#434651]">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-1 bg-[#003580] rounded"></span>
                        <span class="font-semibold text-[#003580]">Garis Komando Resmi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-1 border-t-2 border-dashed border-[#664780]"></span>
                        <span class="font-semibold text-[#664780]">Garis Koordinasi dengan LPPM IT Del</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded bg-[#46166c]/10 border-2 border-[#46166c]"></span>
                        <span class="font-semibold text-[#46166c]">Lingkup Kerja UPT PP-ESTEM</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. TUGAS DAN FUNGSI (Dinamis dari CRUD tabel organization_positions) --}}
    <section class="w-full bg-[#dce3eb] py-14 md:py-20" id="tupoksi">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col items-center text-center mb-10">
                <span class="font-label-sm text-label-sm font-bold tracking-wider text-[#46166c] uppercase mb-1">
                    TUGAS &amp; FUNGSI
                </span>
                <h2 class="font-headline-xl text-headline-xl-mobile md:text-headline-xl text-[#003580] font-bold">
                    Uraian Tugas Pengelola UPT PP-ESTEM
                </h2>
                <div class="flex items-center gap-1.5 mt-3 justify-center">
                    <div class="w-10 h-1 rounded-full bg-[#1a81c2]"></div>
                    <div class="w-5 h-1 rounded-full bg-[#46166c]"></div>
                </div>
                <p class="font-body-md text-body-md text-[#434651] max-w-2xl mt-3 text-center">
                    Penjabaran tugas pokok, wewenang, dan tanggung jawab seluruh pengelola unit demi tercapainya mutu pembelajaran di luar kampus yang unggul.
                </p>
            </div>

            @if ($struktur->isNotEmpty())
                @php
                    $utama = $struktur->first(fn ($item) => str_contains(strtolower($item->position_name), 'kepala upt')) ?? $struktur->first();
                    $lainnya = $struktur->reject(fn ($item) => $item->id === $utama->id)->values();
                @endphp

                {{-- Kartu Utama: Kepala UPT --}}
                @if ($utama)
                    <div class="w-full bg-white rounded-xl shadow-md overflow-hidden mb-6 transform hover:-translate-y-1 transition-all duration-200">
                        <div class="bg-gradient-to-r from-[#46166c] via-[#502176] to-[#664780] p-4 md:p-6 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-white/15 flex items-center justify-center shrink-0 shadow-inner">
                                    <i class="bi bi-person-fill-gear text-[28px] text-white"></i>
                                </div>
                                <div>
                                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-[#8cc5dd] font-semibold">Pimpinan Unit</span>
                                    <h3 class="font-headline-lg text-headline-lg font-bold text-white leading-tight">{{ $utama->position_name }}</h3>
                                    @if ($utama->officer_name)
                                        <p class="font-label-lg text-label-lg text-[#8cc5dd] font-medium mt-0.5">{{ $utama->officer_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="self-start md:self-center px-3 py-1 bg-white/15 rounded-md font-label-sm text-label-sm tracking-wide text-white">
                                Pengambil Keputusan Utama
                            </span>
                        </div>
                        <div class="p-6 md:p-8 bg-white">
                            <div class="flex items-start gap-4">
                                <div class="p-2.5 rounded-lg bg-[#46166c]/10 text-[#46166c] shrink-0 mt-1">
                                    <i class="bi bi-shield-check text-[22px]"></i>
                                </div>
                                <div class="font-body-md text-body-md text-[#434651] leading-relaxed">
                                    {!! nl2br(e($utama->description ?? 'Pimpinan UPT yang bertanggung jawab atas terlaksananya kegiatan pada UPT PP-ESTEM dalam mencapai visi, misi, dan tujuannya.')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Grid Kartu Pengelola Lainnya --}}
                @if ($lainnya->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($lainnya as $s)
                            @php
                                $biruTua = $loop->iteration % 2 === 1;
                                $headerBg = $biruTua ? '#003580' : '#1a81c2';
                                $ikon = match (true) {
                                    str_contains(strtolower($s->position_name), 'pelatihan') => 'bi-mortarboard-fill',
                                    str_contains(strtolower($s->position_name), 'penelitian'),
                                    str_contains(strtolower($s->position_name), 'litbang') => 'bi-lightbulb-fill',
                                    str_contains(strtolower($s->position_name), 'kerjasama') => 'bi-briefcase-fill',
                                    str_contains(strtolower($s->position_name), 'administrasi') => 'bi-folder-fill',
                                    default => 'bi-person-badge-fill',
                                };
                            @endphp
                            <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col h-full transform hover:-translate-y-1 transition-all duration-200">
                                <div class="p-4 md:p-5 text-white flex items-center gap-3.5" style="background-color: {{ $headerBg }};">
                                    <div class="w-12 h-12 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                                        <i class="bi {{ $ikon }} text-[24px] text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-headline-sm text-headline-sm font-bold text-white leading-tight">{{ $s->position_name }}</h3>
                                        @if ($s->officer_name)
                                            <p class="font-body-sm text-body-sm text-[#d6e3ff] font-medium mt-0.5">{{ $s->officer_name }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-6 bg-white flex-1">
                                    <p class="font-body-md text-body-md text-[#434651] leading-relaxed">
                                        {!! nl2br(e($s->description ?? '-')) !!}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                    Data struktur organisasi dan uraian tugas belum ditambahkan.
                </div>
            @endif
        </div>
    </section>

    {{-- 5. PITA AJAKAN (CTA BANNER) --}}
    <section class="w-full bg-[#FFFFFF] py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="relative w-full rounded-2xl bg-gradient-to-r from-[#46166c] via-[#592582] to-[#664780] p-8 md:p-12 text-white shadow-xl overflow-hidden flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                    <i class="bi bi-buildings text-[180px] text-white"></i>
                </div>
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded bg-white/20 text-white font-label-sm text-label-sm uppercase tracking-wider mb-2">
                        Kesempatan Emas Mahasiswa
                    </span>
                    <h2 class="font-headline-xl text-headline-xl-mobile md:text-headline-xl font-bold text-white tracking-tight">
                        Ingin Mengikuti Program PDLK?
                    </h2>
                    <p class="font-body-lg text-body-lg text-white/85 mt-2 leading-relaxed">
                        Dapatkan pengalaman industri nyata, perluas jejaring profesional, dan ikuti kegiatan pembelajaran di luar kampus bersama mitra Institut Teknologi Del.
                    </p>
                </div>
                <div class="relative z-10 flex flex-wrap items-center gap-3 shrink-0">
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded bg-white text-[#46166c] font-label-lg text-label-lg font-bold hover:bg-[#8cc5dd] hover:text-[#003580] shadow-md transition-all"
                       href="{{ route('lowongan.index') }}">
                        <i class="bi bi-briefcase"></i>
                        <span>Lihat Lowongan</span>
                    </a>
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded font-label-lg text-label-lg font-semibold text-white bg-transparent hover:bg-white/15 transition-all border border-white/40"
                       href="{{ route('tentang') }}">
                        <i class="bi bi-compass"></i>
                        <span>Visi, Misi &amp; Tujuan</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
