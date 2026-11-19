@extends('layouts.publik')

@section('title', 'Beranda')

@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- SECTION 1: HERO --}}
    <section class="relative w-full overflow-hidden" id="beranda"
         style="background: linear-gradient(rgba(0, 53, 128, 0.88), rgba(0, 85, 165, 0.88)), url('{{ asset('images/kampus-del.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin pt-14 pb-28">
            <div class="max-w-4xl mx-auto flex flex-col items-center text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded bg-[#46166c] text-white font-label-sm text-label-sm uppercase tracking-wider mb-5 shadow-sm font-semibold">
                    <i class="bi bi-shield-check"></i>
                    SISTEM INFORMASI RESMI
                </span>
                <h1 class="text-white font-display-lg text-display-lg tracking-tight leading-tight mb-4">
                    Pembelajaran di Luar Kampus (PDLK)
                </h1>
                <p class="text-white/90 font-body-lg text-body-lg max-w-2xl mx-auto leading-relaxed mb-8">
                    UPT PP-ESTEM Institut Teknologi Del — Wadah Mahasiswa Belajar Langsung di Dunia Kerja, Riset, dan Masyarakat
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#002155] text-white font-label-lg text-label-lg shadow-md hover:bg-[#00173d] transition-all" href="{{ route('lowongan.index') }}">
                        <i class="bi bi-briefcase"></i>
                        <span>Lihat Lowongan PDLK</span>
                    </a>
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-transparent text-white font-label-lg text-label-lg border-2 border-white hover:bg-white hover:text-primary transition-all" href="#jenis-pdlk">
                        <i class="bi bi-info-circle"></i>
                        <span>Pelajari Jenis PDLK</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-1">
            <svg class="w-full h-16 sm:h-24 block" preserveAspectRatio="none" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,32L48,42.7C96,53,192,75,288,74.7C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" fill="#8cc5dd" fill-opacity="0.5"></path>
                <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,58.7C672,64,768,96,864,96C960,96,1056,64,1152,53.3C1248,43,1344,53,1392,58.7L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" fill="#fbf9f8"></path>
            </svg>
        </div>
    </section>

    {{-- 4 KARTU STATISTIK --}}
    <div class="relative z-20 max-w-7xl mx-auto px-margin-mobile md:px-margin -mt-10 sm:-mt-14 w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-lg border-t-4 border-[#1a81c2] flex items-center gap-4 transition-transform hover:-translate-y-1">
                <div class="w-14 h-14 rounded-full bg-[#1a81c2] text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-xl text-headline-xl text-[#003580] leading-none">{{ $statistik['bentuk'] }}</span>
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wide mt-1">Jenis Kegiatan</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-lg border-t-4 border-[#46166c] flex items-center gap-4 transition-transform hover:-translate-y-1">
                <div class="w-14 h-14 rounded-full bg-[#46166c] text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
                    <i class="bi bi-buildings-fill"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-xl text-headline-xl text-[#003580] leading-none">{{ $statistik['mitra'] }}</span>
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wide mt-1">Mitra Aktif</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-lg border-t-4 border-[#1a81c2] flex items-center gap-4 transition-transform hover:-translate-y-1">
                <div class="w-14 h-14 rounded-full bg-[#1a81c2] text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-xl text-headline-xl text-[#003580] leading-none">{{ $statistik['peserta'] }}</span>
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wide mt-1">Peserta Aktif</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-lg border-t-4 border-[#46166c] flex items-center gap-4 transition-transform hover:-translate-y-1">
                <div class="w-14 h-14 rounded-full bg-[#46166c] text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
                    <i class="bi bi-briefcase-fill"></i>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-xl text-headline-xl text-[#003580] leading-none">{{ $statistik['lowongan'] }}</span>
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wide mt-1">Lowongan Dibuka</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: PROFIL UPT PP-ESTEM & STRUKTUR ORGANISASI --}}
    <section class="w-full bg-[#dce3eb] py-space-xl mt-12" id="profil">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <div class="mb-4">
                        <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">PROFIL INSTITUSI</span>
                        <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Profil UPT PP-ESTEM</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                            <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                        </div>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-4 text-justify">
    Unit Pelayanan Teknis Pusat Pembelajaran Elektronik dan Sains, Teknologi, Enjinering, dan Matematika (UPT PP-ESTEM) merupakan unit di bawah koordinasi Wakil Rektor I Bidang Akademik dan Kemahasiswaan Institut Teknologi Del yang memiliki dua divisi, yaitu Divisi Pelatihan dan Divisi PDLK. UPT PP-ESTEM bertanggung jawab mengelola seluruh kegiatan Pembelajaran di Luar Kampus (PDLK), mulai dari proses administrasi, sosialisasi program, pendaftaran peserta, seleksi, pengumuman hasil seleksi, hingga pelaksanaan kegiatan serta monitoring dan evaluasi (monev) pada pertengahan dan akhir semester.
</p>
                    <div class="w-full bg-[#46166c] p-5 rounded-xl shadow-md mb-5 text-white relative overflow-hidden">
                        <i class="bi bi-quote text-[#8cc5dd] text-5xl leading-none block mb-1 -ml-1"></i>
                        <span class="font-label-sm text-label-sm text-[#8cc5dd] uppercase tracking-wider block font-semibold mb-1">Visi Utama</span>
                        <p class="font-body-md text-body-md italic text-white/95 leading-relaxed">
                            “Menjadi pusat pengembangan pembelajaran yang unggul dan inovatif melalui penerapan pembelajaran yang berkualitas, efisien, efektif, produktif, dan akuntabel.”
                        </p>
                    </div>
                    <a class="inline-flex items-center gap-2 px-5 py-2.5 rounded bg-primary text-white font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm" href="{{ route('tentang') }}">
                        <span>Lihat Visi, Misi &amp; Tujuan</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="lg:col-span-5">
                    <div class="relative rounded-xl overflow-hidden shadow-md bg-surface-container-lowest">
                        <img alt="Gedung Kampus Institut Teknologi Del" class="w-full h-80 object-cover"
     src="{{ asset('images/kampus-del.jpg') }}">
                        <div class="p-3 bg-surface-container-lowest border-t border-primary/10">
                            <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5">
                                <i class="bi bi-geo-alt-fill text-[#1a81c2]"></i>
                                Institut Teknologi Del, Sitoluama, Laguboti
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dua Skema PDLK --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter mt-10">
                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="bg-[#1a81c2] px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                                <i class="bi bi-stars"></i>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-white font-bold">Skema PDLK Flagship</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded bg-white/20 text-white font-label-sm text-label-sm uppercase font-semibold">Nasional</span>
                    </div>
                    <div class="p-6 bg-white flex-1">
                        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                            Program terpusat yang diinisiasi oleh Kemdiktisaintek melalui inisiatif Kampus Berdampak nasional dengan konversi SKS penuh dan pendanaan terstruktur.
                        </p>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="bg-[#46166c] px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-white font-bold">Skema PDLK Mandiri</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded bg-white/20 text-white font-label-sm text-label-sm uppercase font-semibold">Kemitraan</span>
                    </div>
                    <div class="p-6 bg-white flex-1">
                        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                            Program yang diinisiasi langsung oleh program studi atau institusi IT Del bersama mitra strategis, memerlukan dokumen kerja sama dengan mitra.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Struktur Organisasi (Dinamis dari organization_positions) --}}
            <div class="mt-14">
                <div class="text-center mb-8 flex flex-col items-center">
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">MANAJEMEN &amp; TATA KELOLA</span>
                    <h3 class="font-headline-md text-headline-md text-[#003580] tracking-tight">Struktur Organisasi UPT PP-ESTEM</h3>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">Tim Pengelola &amp; Pembina Program Pembelajaran di Luar Kampus</p>
                </div>

                @if ($struktur->isEmpty())
                    <div class="bg-white rounded-xl p-6 text-center text-on-surface-variant shadow-sm">
                        Data struktur organisasi belum ditambahkan.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach ($struktur as $s)
                            <div class="group bg-surface-container-lowest rounded-lg shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-[3px] hover:border-[#46166c]">
                                <div>
                                    <div class="bg-[#003580] py-3 flex justify-center items-center">
                                        <div class="w-9 h-9 rounded-full bg-white text-[#003580] flex items-center justify-center text-base shadow-sm">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <span class="font-label-sm text-label-sm text-[#46166c] block uppercase tracking-wider font-semibold">{{ $s->position_name }}</span>
                                        <h4 class="font-label-lg text-label-lg text-primary mt-0.5 leading-snug font-bold">{{ $s->officer_name ?? '-' }}</h4>
                                        @if ($s->description)
                                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-2 line-clamp-3 leading-snug">{{ $s->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="text-center mt-8">
                    <a class="inline-flex items-center gap-2 px-5 py-2.5 rounded bg-primary text-white font-label-md text-label-md hover:bg-primary-container transition-colors shadow-sm" href="{{ route('struktur') }}">
                        <span>Lihat Struktur Organisasi Lengkap</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 3: JENIS KEGIATAN PDLK (Dinamis dari activity_types) --}}
    <section class="w-full bg-[#003580] py-space-xl relative overflow-hidden" id="jenis-pdlk">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 rounded-full bg-[#0055A5]/40 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -top-20 w-96 h-96 rounded-full bg-[#0055A5]/30 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#8cc5dd] uppercase tracking-widest font-bold block mb-1">KURIKULUM MERDEKA IT DEL</span>
                <h2 class="font-headline-lg text-headline-lg text-white tracking-tight">{{ $jenisKegiatan->count() }} Jenis Kegiatan Pembelajaran di Luar Kampus</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#8cc5dd] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-[#8cc5dd] mt-2">
                    Pilihan eksplorasi kompetensi praktis bagi mahasiswa Institut Teknologi Del yang dapat dikonversikan sesuai ketentuan akademik.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($jenisKegiatan as $jenis)
                    @php
                        $isEven = $loop->iteration % 2 === 0;
                        $accentHex = $isEven ? '#46166c' : '#1a81c2';
                        $ikon = match ($jenis->slug) {
                            'magang' => 'bi-briefcase',
                            'kerja-praktik' => 'bi-laptop',
                            'penelitian' => 'bi-search',
                            'pertukaran-pelajar' => 'bi-globe-americas',
                            'kkn' => 'bi-house-heart',
                            'studi-independen' => 'bi-compass',
                            'kewirausahaan' => 'bi-lightbulb',
                            default => 'bi-journal-bookmark',
                        };
                        $spanTerakhir = ($loop->last && $loop->count % 4 === 3) ? 'sm:col-span-2 lg:col-span-2' : '';
                    @endphp
                    <div class="group bg-white p-6 rounded-xl shadow-md border-t-4 flex flex-col justify-between transition-all duration-300 hover:bg-[#1a81c2] hover:-translate-y-1 {{ $spanTerakhir }}"
                         style="border-top-color: {{ $accentHex }};">
                        <div>
                            <div class="w-12 h-12 rounded-full text-white flex items-center justify-center text-xl mb-4 group-hover:bg-white transition-colors"
                                 style="background-color: {{ $accentHex }};">
                                <i class="bi {{ $ikon }} group-hover:text-[#003580]"></i>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-primary mb-2 group-hover:text-white transition-colors">{{ $jenis->name }}</h3>
                            @if ($jenis->duration_text)
                                <span class="inline-block px-2.5 py-0.5 rounded bg-primary/10 text-primary font-label-sm text-label-sm mb-3 font-semibold group-hover:bg-white/20 group-hover:text-white transition-colors">
                                    {{ $jenis->duration_text }}
                                </span>
                            @endif
                            @if ($jenis->summary)
                                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed group-hover:text-white/90 transition-colors">
                                    {{ $jenis->summary }}
                                </p>
                            @endif
                        </div>
                        <a class="inline-flex items-center gap-1 font-label-md text-label-md text-primary mt-4 group-hover:text-white transition-colors font-semibold"
                           href="{{ route('lowongan.index', ['jenis' => $jenis->slug]) }}">
                            <span>Lihat Lowongan</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SECTION 4: LOWONGAN PDLK TERBARU (Dinamis dari Query::LOWONGAN) --}}
    <section class="w-full bg-[#dce3eb] py-space-xl" id="lowongan">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">KESEMPATAN KARIR &amp; RISET</span>
                    <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Lowongan PDLK Terbaru</h2>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-2">Pilih posisi magang, riset, atau proyek industri yang sesuai dengan jalur peminatan Anda.</p>
                </div>
                <a class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:underline" href="{{ route('lowongan.index') }}">
                    <span>Lihat filter &amp; kriteria</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            @if ($lowongan->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                    Belum ada lowongan PDLK yang dipublikasikan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                    @foreach ($lowongan as $item)
                        @php $dibuka = lowongan_dibuka($item); @endphp
                        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col justify-between transition-all hover:shadow-md relative">
                            <div class="h-1 w-full" style="background: linear-gradient(to right, #1a81c2, #46166c);"></div>
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center text-primary font-bold text-lg shrink-0 overflow-hidden">
                                            @if ($item->cover_image)
                                                <img src="{{ file_url($item->cover_image) }}" alt="{{ $item->partner_name }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="bi bi-building text-2xl text-[#1a81c2]"></i>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Mitra</span>
                                            <span class="font-label-md text-label-md text-primary leading-tight font-semibold">{{ $item->partner_name }}</span>
                                        </div>
                                    </div>
                                    @if ($dibuka)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-700 text-white font-label-sm text-[11px] font-bold tracking-wide uppercase shrink-0 shadow-sm">Dibuka</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-red-700 text-white font-label-sm text-[11px] font-bold tracking-wide uppercase shrink-0 shadow-sm">Ditutup</span>
                                    @endif
                                </div>

                                <h3 class="font-headline-sm text-headline-sm text-primary mb-3 font-bold">{{ $item->title }}</h3>

                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-2.5 py-1 rounded bg-[#46166c] text-white font-label-sm text-label-sm">{{ $item->activity_name }}</span>
                                    <span class="px-2.5 py-1 rounded text-white font-label-sm text-label-sm {{ $item->registration_type === 'external' ? 'bg-[#664780]' : 'bg-[#0055A5]' }}">
                                        {{ $item->registration_type === 'external' ? 'Eksternal' : 'Internal' }}
                                    </span>
                                </div>

                                <div class="space-y-2 py-3 border-y border-outline-variant/30 text-on-surface-variant font-body-sm text-body-sm">
                                    @if ($item->location)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-geo-alt text-[#1a81c2]"></i>
                                            <span>{{ $item->location }}</span>
                                        </div>
                                    @endif
                                    @if ($item->quota)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-people text-[#1a81c2]"></i>
                                            <span>Kuota: {{ $item->quota }} Mahasiswa</span>
                                        </div>
                                    @endif
                                    @if ($item->period_name)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar text-[#1a81c2]"></i>
                                            <span>{{ $item->period_name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                @if ($item->registration_deadline)
                                    <div class="flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant mb-3">
                                        <span>Batas pendaftaran:</span>
                                        <span class="font-semibold text-primary">{{ tgl($item->registration_deadline) }}</span>
                                    </div>
                                @endif
                                <a class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded bg-primary text-white font-label-md text-label-md hover:bg-primary-container transition-colors"
                                   href="{{ route('lowongan.show', $item->slug) }}">
                                    <span>Lihat Detail Lowongan</span>
                                    <i class="bi bi-arrow-right-short text-lg"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-10">
                <a class="inline-flex items-center gap-2 px-8 py-3 rounded bg-primary text-white font-label-lg text-label-lg shadow-md hover:bg-primary-container transition-colors" href="{{ route('lowongan.index') }}">
                    <span>Lihat Semua Lowongan</span>
                    <i class="bi bi-briefcase"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- SECTION 4.5: CTA BANNER --}}
    <section class="w-full relative overflow-hidden py-10 px-margin-mobile md:px-margin" style="background: linear-gradient(135deg, #46166c 0%, #664780 100%);">
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none opacity-10">
            <svg class="w-full h-12 block" preserveAspectRatio="none" viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,20 Q360,50 720,20 T1440,20 L1440,60 L0,60 Z" fill="#ffffff"></path>
            </svg>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col items-start text-left">
                <h3 class="text-white font-headline-lg text-headline-lg font-bold tracking-tight">Siap Mengikuti PDLK?</h3>
                <p class="text-white/90 font-body-md text-body-md mt-1 max-w-xl">
                    Login menggunakan akun CIS untuk mendaftar lowongan dan memantau kegiatanmu.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                @if (session()->has('cis_user'))
                    <a class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-white text-[#46166c] font-label-lg text-label-lg font-bold shadow-md hover:bg-white/90 transition-all" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-white text-[#46166c] font-label-lg text-label-lg font-bold shadow-md hover:bg-white/90 transition-all" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login Sekarang</span>
                    </a>
                @endif
                <a class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-transparent text-white font-label-lg text-label-lg font-semibold border-2 border-white hover:bg-white/10 transition-all" href="{{ route('lowongan.index') }}">
                    <i class="bi bi-briefcase"></i>
                    <span>Lihat Lowongan</span>
                </a>
            </div>
        </div>
    </section>

    {{-- SECTION 5: PENGUMUMAN & BERITA PDLK --}}
    <section class="w-full bg-white py-space-xl" id="pengumuman">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">INFORMASI AKADEMIK</span>
                    <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Pengumuman &amp; Berita PDLK</h2>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                </div>

                <div class="inline-flex p-1 bg-surface-container rounded-lg gap-1" id="announcement-tabs">
                    <button class="tab-btn px-4 py-1.5 rounded font-label-md text-label-md transition-colors bg-[#1a81c2] text-white" data-filter="all" type="button">Semua</button>
                    <button class="tab-btn px-4 py-1.5 rounded font-label-md text-label-md transition-colors text-on-surface-variant hover:text-primary" data-filter="berita" type="button">Berita</button>
                    @if (sprint(2))
                        <button class="tab-btn px-4 py-1.5 rounded font-label-md text-label-md transition-colors text-on-surface-variant hover:text-primary" data-filter="seleksi" type="button">Hasil Seleksi</button>
                    @endif
                </div>
            </div>

            @if ($pengumuman->isEmpty())
                <div class="bg-surface-container-lowest rounded-xl p-8 text-center text-on-surface-variant border border-outline-variant/30">
                    Belum ada berita atau pengumuman yang dipublikasikan.
                </div>
            @else
                <div class="space-y-3" id="announcement-list">
                    @foreach ($pengumuman as $item)
                        @php
                            $isSeleksi = $item->kategori_tab === 'seleksi';
                            $detailUrl = $isSeleksi ? route('hasil.show', $item->slug) : route('berita.show', $item->slug);
                        @endphp
                        <div class="announcement-item bg-surface-container-lowest p-5 rounded-lg shadow-sm border-l-4 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:shadow-md"
                             style="border-left-color: {{ $isSeleksi ? '#664780' : '#46166c' }};"
                             data-category="{{ $item->kategori_tab }}">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-lg bg-[#8cc5dd]/30 text-[#003580] flex items-center justify-center text-xl shrink-0 mt-0.5">
                                    <i class="bi {{ $isSeleksi ? 'bi-file-earmark-check' : 'bi-newspaper' }}"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded text-white font-label-sm text-label-sm font-semibold {{ $isSeleksi ? 'bg-[#664780]' : 'bg-[#46166c]' }}">
                                            {{ $isSeleksi ? 'Hasil Seleksi' : 'Berita' }}
                                        </span>
                                        @if ($item->published_at)
                                            <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                                                <i class="bi bi-calendar3"></i> {{ tgl($item->published_at) }}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{ $detailUrl }}" class="font-headline-sm text-headline-sm text-primary hover:text-secondary leading-snug block no-underline">
                                        {{ $item->title }}
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                                <a class="px-4 py-1.5 rounded bg-primary text-white font-label-sm text-label-sm hover:bg-primary-container" href="{{ $detailUrl }}">
                                    Baca
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-10">
                <a class="inline-flex items-center gap-2 px-8 py-3 rounded bg-primary text-white font-label-lg text-label-lg shadow-md hover:bg-primary-container transition-colors" href="{{ route('berita.index') }}">
                    <span>Lihat Semua Pengumuman</span>
                    <i class="bi bi-bell"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- SECTION 6: MITRA KOLABORASI PDLK (Dinamis dari partners) --}}
    <section class="w-full bg-white py-space-xl border-t border-outline-variant/20" id="mitra">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-4 rounded-2xl p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden text-white shadow-md"
                     style="background: linear-gradient(135deg, #46166c 0%, #664780 100%); min-height: 420px;">
                    <div class="relative z-10">
                        <span class="font-label-sm text-label-sm text-[#8cc5dd] uppercase font-bold block mb-2" style="letter-spacing: 1.5px;">KOLABORASI</span>
                        <h2 class="font-headline-xl text-headline-xl font-bold text-white tracking-tight mb-3">Mitra PDLK</h2>
                        <p class="font-body-md text-body-md text-white/90 leading-relaxed mb-6">
                            Institut Teknologi Del menjalin sinergi strategis dengan BUMN, korporasi, dan lembaga riset terkemuka untuk pembelajaran terapan mahasiswa.
                        </p>
                        <div class="grid grid-cols-3 gap-2 py-4 border-y border-white/20 text-center">
                            <div class="flex flex-col items-center">
                                <span class="font-headline-lg text-headline-lg font-bold text-white mb-0 leading-tight">{{ $mitraStat->total ?? 0 }}</span>
                                <span class="font-label-sm text-label-sm text-[#8cc5dd] font-semibold mt-1">Mitra</span>
                            </div>
                            <div class="flex flex-col items-center border-x border-white/20">
                                <span class="font-headline-lg text-headline-lg font-bold text-white mb-0 leading-tight">{{ $mitraStat->cooperating ?? 0 }}</span>
                                <span class="font-label-sm text-label-sm text-[#8cc5dd] font-semibold mt-1">Bekerja Sama</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="font-headline-lg text-headline-lg font-bold text-white mb-0 leading-tight">{{ $mitraStat->in_process ?? 0 }}</span>
                                <span class="font-label-sm text-label-sm text-[#8cc5dd] font-semibold mt-1">Dalam Proses</span>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 mt-6">
                        <a class="inline-flex items-center justify-center gap-2 w-full px-5 py-2.5 bg-white text-[#46166c] font-semibold rounded-lg shadow hover:bg-white/90 transition-all text-label-md font-label-md"
                           href="{{ route('lowongan.index') }}">
                            Lihat Lowongan Mitra →
                        </a>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 pointer-events-none opacity-[0.08]">
                        <svg class="w-full h-24 block" preserveAspectRatio="none" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,58.7C672,64,768,96,864,96C960,96,1056,64,1152,53.3C1248,43,1344,53,1392,58.7L1440,64L1440,120L0,120Z" fill="#ffffff"></path>
                        </svg>
                    </div>
                </div>

                <div class="lg:col-span-8 rounded-2xl p-4 sm:p-6 flex flex-col justify-center" style="background-color: rgba(140, 197, 221, 0.18);">
                    @if ($mitra->isEmpty())
                        <div class="bg-white rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                            Belum ada data mitra yang terdaftar.
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach ($mitra as $m)
                                @php
                                    $isGanjil = $loop->iteration % 2 === 1;
                                    $borderWarna = $isGanjil ? '#1a81c2' : '#46166c';
                                    $statusLabel = match ($m->cooperation_status) {
                                        'cooperating' => 'Bekerja Sama',
                                        'in_process' => 'Dalam Proses',
                                        default => 'Belum Bekerja Sama',
                                    };
                                    $badgeClass = match ($m->cooperation_status) {
                                        'cooperating' => 'bg-emerald-700',
                                        'in_process' => 'bg-[#664780]',
                                        default => 'bg-slate-600',
                                    };
                                @endphp
                                <div class="bg-white rounded-lg p-3 shadow-sm text-center flex flex-col items-center justify-between border-b-[3px] transition-all duration-200 group hover:-translate-y-1 hover:shadow-md"
                                     style="border-bottom-color: {{ $borderWarna }};">
                                    <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-xl text-on-surface-variant group-hover:scale-105 transition-all mb-2">
                                        <i class="bi bi-buildings"></i>
                                    </div>
                                    <div class="flex flex-col items-center flex-1 justify-center">
                                        <h4 class="font-bold text-[#003580] text-[13px] leading-tight mb-1">{{ $m->name }}</h4>
                                        @if ($m->field)
                                            <span class="text-[11px] text-on-surface-variant">{{ $m->field }}</span>
                                        @endif
                                    </div>
                                    @if ($m->cooperation_status)
                                        <span class="mt-2.5 px-2 py-0.5 rounded text-white text-[10px] font-semibold {{ $badgeClass }}">{{ $statusLabel }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 7: GALERI KEGIATAN PDLK (Dinamis dari galleries) --}}
    <section class="w-full bg-[#dce3eb] py-space-xl" id="galeri">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">DOKUMENTASI AKTIVITAS</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Galeri Kegiatan PDLK</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Rekaman visual dedikasi, pembelajaran terapan mahasiswa, dan supervisi akademik di lapangan.</p>
            </div>

            @if ($galeri->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                    Belum ada galeri kegiatan yang dipublikasikan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($galeri as $g)
                        <a href="{{ route('galeri.show', $g->id) }}" class="bg-white rounded-lg shadow-sm overflow-hidden flex flex-col h-full group hover:shadow-md transition-all duration-300 no-underline">
                            <div class="w-full relative overflow-hidden shrink-0 bg-surface-container flex items-center justify-center" style="aspect-ratio: 4/3;">
                                @if ($g->cover_image)
                                    <img alt="{{ $g->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ file_url($g->cover_image) }}" loading="lazy">
                                @else
                                    <i class="bi bi-images text-4xl text-on-surface-variant/40"></i>
                                @endif
                                <div class="absolute inset-0 bg-[#1a81c2]/75 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                    <span class="w-12 h-12 rounded-full bg-white/20 text-white flex items-center justify-center text-2xl backdrop-blur-sm">
                                        <i class="bi bi-zoom-in"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                @if ($g->event_date)
                                    <span class="font-body-sm text-body-sm text-on-surface-variant block mb-1">{{ tgl($g->event_date) }}</span>
                                @endif
                                <h3 class="font-headline-sm text-headline-sm text-[#003580] font-bold line-clamp-2 leading-snug">{{ $g->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="text-center mt-10">
                <a class="inline-flex items-center gap-2 px-8 py-3 rounded bg-[#003580] text-white font-label-lg text-label-lg shadow-md hover:bg-[#0055A5] transition-colors" href="{{ route('galeri.index') }}">
                    <i class="bi bi-images"></i><span>Lihat Semua Galeri Kegiatan</span>
                </a>
            </div>
        </div>
    </section>

    {{-- SECTION 8: REKAPITULASI PESERTA PDLK (Dinamis dari periods & pdlk_participants) --}}
    <section class="w-full bg-white py-space-xl border-t border-outline-variant/30" id="rekapitulasi">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">DATA PDLK</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Rekapitulasi Peserta PDLK</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">
                    Transparansi dan progres keikutsertaan mahasiswa Institut Teknologi Del dalam program PDLK antar-semester.
                </p>
            </div>

            @php
                $maxTren = max(1, (int) ($periodeTren->max('total') ?? 0));
                $totalAktif = (int) ($pesertaAktif->total ?? 0);
                $flagshipAktif = (int) ($pesertaAktif->flagship ?? 0);
                $mandiriAktif = (int) ($pesertaAktif->independent ?? 0);
                $persenFlagship = $totalAktif > 0 ? round(($flagshipAktif / $totalAktif) * 100) : 0;
                $persenMandiri = $totalAktif > 0 ? round(($mandiriAktif / $totalAktif) * 100) : 0;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
                <div class="lg:col-span-7 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/30 shadow-sm">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b border-outline-variant/30">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-bar-chart-line-fill text-xl text-[#003580]"></i>
                            <span class="font-headline-sm text-headline-sm text-primary font-bold">Tren Jumlah Peserta</span>
                        </div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">4 Periode Terakhir</span>
                    </div>

                    @if ($periodeTren->isEmpty())
                        <p class="text-on-surface-variant text-center py-6">Belum ada data periode akademik.</p>
                    @else
                        <div class="space-y-5">
                            @foreach ($periodeTren as $pr)
                                @php
                                    $barColor = $loop->iteration % 2 === 1 ? '#1a81c2' : '#46166c';
                                    $lebar = $pr->total > 0 ? max(5, round(($pr->total / $maxTren) * 100)) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-1 text-body-sm font-medium">
                                        <span class="text-on-surface">{{ $pr->name }}</span>
                                        <span class="font-bold" style="color: {{ $barColor }};">{{ $pr->total }} Peserta</span>
                                    </div>
                                    <div class="w-full bg-surface-container rounded-full h-4 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300" style="width: {{ $lebar }}%; background-color: {{ $barColor }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-5 flex flex-col gap-4">
                    <div class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-[#003580] border border-outline-variant/30 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#003580] text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wide">Total Peserta Aktif</span>
                                <span class="font-headline-sm text-headline-sm text-primary font-bold">{{ $totalAktif }} Mahasiswa</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-primary/10 text-primary font-label-sm text-label-sm font-bold">{{ $totalAktif > 0 ? '100%' : '0%' }}</span>
                    </div>

                    <div class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-[#1a81c2] border border-outline-variant/30 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#1a81c2] text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wide">PDLK Flagship</span>
                                <span class="font-headline-sm text-headline-sm text-primary font-bold">{{ $flagshipAktif }} Mahasiswa</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-[#1a81c2]/10 text-[#0055A5] font-label-sm text-label-sm font-bold">{{ $persenFlagship }}%</span>
                    </div>

                    <div class="bg-surface-container-lowest p-5 rounded-xl border-l-4 border-[#46166c] border border-outline-variant/30 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#46166c] text-white flex items-center justify-center text-xl shrink-0 shadow-sm">
                                <i class="bi bi-briefcase-fill"></i>
                            </div>
                            <div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wide">PDLK Mandiri</span>
                                <span class="font-headline-sm text-headline-sm text-primary font-bold">{{ $mandiriAktif }} Mahasiswa</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded bg-[#46166c]/10 text-[#46166c] font-label-sm text-label-sm font-bold">{{ $persenMandiri }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const tabBtns = document.querySelectorAll('#announcement-tabs .tab-btn');
        const items = document.querySelectorAll('#announcement-list .announcement-item');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => {
                    b.classList.remove('bg-[#1a81c2]', 'text-white');
                    b.classList.add('text-on-surface-variant');
                });
                btn.classList.add('bg-[#1a81c2]', 'text-white');
                btn.classList.remove('text-on-surface-variant');

                const filter = btn.getAttribute('data-filter');
                items.forEach(item => {
                    const cat = item.getAttribute('data-category');
                    item.style.display = (filter === 'all' || cat === filter) ? 'flex' : 'none';
                });
            });
        });
    })();
</script>
@endpush
