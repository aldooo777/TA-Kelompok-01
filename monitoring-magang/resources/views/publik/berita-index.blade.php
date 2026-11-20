@extends('layouts.publik')

@section('title', 'Pengumuman & Berita')

@section('fullwidth')
@php
    $kategori = $kategori ?? 'all';
    $q = $q ?? '';
    $arsip = $arsip ?? null;
    $bolehHasil = $bolehHasil ?? false;
    $pengumuman = $pengumuman ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
    $jumlahKategori = $jumlahKategori ?? (object) ['semua' => 0, 'berita' => 0, 'hasil' => 0];
    $terbaru = $terbaru ?? collect();
    $arsipBulan = $arsipBulan ?? collect();

    $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];
    $formatArsip = function (string $ym) use ($namaBulan): string {
        [$tahun, $bulan] = array_pad(explode('-', $ym), 2, '');
        return ($namaBulan[$bulan] ?? $bulan) . ' ' . $tahun;
    };

    $sedangFilter = ($q !== '') || ($arsip !== null) || ($kategori !== ($bolehHasil ? 'all' : 'berita')) || $pengumuman->currentPage() > 1;
    $featured = (! $sedangFilter && $pengumuman->isNotEmpty()) ? $pengumuman->first() : null;
    $daftar = $featured ? $pengumuman->getCollection()->slice(1)->values() : $pengumuman->getCollection();
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative w-full bg-gradient-to-r from-primary via-primary-container to-secondary text-on-primary overflow-hidden min-h-[220px] flex flex-col justify-between">
        <div class="absolute inset-0 opacity-15 bg-cover bg-center mix-blend-overlay pointer-events-none"
             style="background-image: url('{{ asset('images/kampus-del.jpg') }}');"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin pt-space-xl pb-space-lg w-full flex flex-col justify-center">
            <div class="flex items-center gap-space-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary text-on-tertiary font-label-sm text-label-sm tracking-wider uppercase shadow-sm">
                    <span class="material-symbols-outlined text-[14px]">campaign</span>
                    INFORMASI
                </span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-primary tracking-tight mt-2">Pengumuman &amp; Berita</h1>
            <p class="font-body-md text-body-md text-primary-fixed-dim max-w-3xl mt-1 leading-relaxed">
                Informasi resmi, berita kegiatan, dan pengumuman program Pembelajaran di Luar Kampus Institut Teknologi Del.
            </p>
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 mt-3 font-label-sm text-label-sm text-secondary-container">
                <a class="hover:text-on-primary transition-colors text-secondary-container" href="{{ route('beranda') }}">Beranda</a>
                <span>/</span>
                <span class="text-on-primary font-semibold">Pengumuman</span>
            </nav>
        </div>

        <div class="w-full overflow-hidden leading-none text-surface-container-lowest -mb-px">
            <svg class="w-full h-8 block fill-current" preserveAspectRatio="none" viewBox="0 0 1440 32">
                <path d="M0,16 C360,32 720,0 1080,24 C1260,36 1380,8 1440,16 L1440,32 L0,32 Z"></path>
            </svg>
        </div>
    </section>

    {{-- 2. FILTER & SEARCH BAR --}}
    <section class="w-full bg-surface-container-lowest shadow-sm border-b border-outline-variant/20">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin py-space-md flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-space-md">
            {{-- Tab Pills Filter --}}
            <div class="inline-flex p-1 rounded-full bg-surface-container gap-1 self-start sm:self-auto flex-wrap">
                <a href="{{ route('berita.index', array_filter(['q' => $q, 'arsip' => $arsip])) }}"
                   class="px-5 py-2 rounded-full font-label-sm text-label-sm transition-all duration-150 no-underline {{ $kategori === 'all' ? 'bg-secondary text-on-secondary font-semibold shadow-sm' : 'font-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest' }}">
                    Semua
                </a>
                <a href="{{ route('berita.index', array_filter(['kategori' => 'berita', 'q' => $q, 'arsip' => $arsip])) }}"
                   class="px-5 py-2 rounded-full font-label-sm text-label-sm transition-all duration-150 no-underline {{ $kategori === 'berita' ? 'bg-secondary text-on-secondary font-semibold shadow-sm' : 'font-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest' }}">
                    Berita
                </a>
                @if ($bolehHasil)
                    <a href="{{ route('berita.index', array_filter(['kategori' => 'hasil', 'q' => $q, 'arsip' => $arsip])) }}"
                       class="px-5 py-2 rounded-full font-label-sm text-label-sm transition-all duration-150 no-underline {{ $kategori === 'hasil' ? 'bg-secondary text-on-secondary font-semibold shadow-sm' : 'font-medium text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest' }}">
                        Hasil Seleksi
                    </a>
                @endif
            </div>

            {{-- Search Box --}}
            <form method="GET" action="{{ route('berita.index') }}" class="relative w-full sm:w-80">
                @if ($kategori !== 'all')
                    <input type="hidden" name="kategori" value="{{ $kategori }}">
                @endif
                @if ($arsip)
                    <input type="hidden" name="arsip" value="{{ $arsip }}">
                @endif
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input name="q" value="{{ $q }}"
                       class="w-full pl-10 pr-16 py-2 text-body-md font-body-md bg-surface-container-low text-on-surface rounded-full border border-outline-variant/30 outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-secondary/40 transition"
                       placeholder="Cari judul atau topik..." type="text">
                @if ($q !== '' || $arsip)
                    <a href="{{ route('berita.index', $kategori !== 'all' ? ['kategori' => $kategori] : []) }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#664780] hover:underline font-semibold">Reset</a>
                @endif
            </form>
        </div>
    </section>

    {{-- 3. PENGUMUMAN UTAMA (FEATURED ITEM) --}}
    @if ($featured)
        @php
            $featuredSeleksi = $featured->type === 'result';
            $featuredUrl = $featuredSeleksi ? route('hasil.show', $featured->slug) : route('berita.show', $featured->slug);
            $featuredTgl = $featured->published_at ?? $featured->created_at;
        @endphp
        <section class="w-full bg-surface py-space-xl">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
                <div class="flex items-center gap-2 mb-space-md">
                    <div class="flex items-center">
                        <span class="w-10 h-1 bg-secondary rounded-l"></span>
                        <span class="w-5 h-1 bg-tertiary rounded-r"></span>
                    </div>
                    <span class="font-label-sm text-label-sm font-bold tracking-widest text-primary uppercase">Pengumuman Terbaru</span>
                </div>

                <article class="bg-surface-container-lowest rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col lg:flex-row group border border-outline-variant/20">
                    <div class="lg:w-1/2 min-h-[260px] lg:min-h-[360px] relative overflow-hidden bg-gradient-to-br from-[#003580] to-[#1a81c2] flex items-center justify-center">
                        @if ($featured->cover_image)
                            <img src="{{ file_url($featured->cover_image) }}" alt="{{ $featured->title }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <i class="bi {{ $featuredSeleksi ? 'bi-file-earmark-check' : 'bi-newspaper' }} text-white/25 text-7xl"></i>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent pointer-events-none"></div>
                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-on-primary font-label-sm text-label-sm">
                            <span class="inline-flex items-center gap-1 bg-tertiary/90 backdrop-blur px-3 py-1 rounded">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                                Publikasi Resmi
                            </span>
                            @if ($featuredTgl)
                                <span class="bg-primary/80 backdrop-blur px-2.5 py-1 rounded">{{ tgl($featuredTgl) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="lg:w-1/2 p-space-lg flex flex-col justify-between bg-surface-container-lowest">
                        <div>
                            <div class="flex items-center justify-between gap-space-xs mb-space-sm flex-wrap">
                                <span class="px-3 py-0.5 rounded font-label-sm text-label-sm font-semibold {{ $featuredSeleksi ? 'bg-tertiary-container text-on-tertiary' : 'bg-tertiary text-on-tertiary' }}">
                                    {{ $featuredSeleksi ? 'Hasil Seleksi' : 'Berita' }}
                                </span>
                                @if ($featuredTgl)
                                    <span class="flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                        {{ tgl($featuredTgl) }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ $featuredUrl }}" class="no-underline">
                                <h2 class="font-headline-md text-headline-md text-primary group-hover:text-secondary transition-colors leading-snug">
                                    {{ $featured->title }}
                                </h2>
                            </a>

                            <p class="font-body-md text-body-md text-on-surface-variant mt-space-sm line-clamp-3 leading-relaxed">
                                {{ $featured->summary ?: ringkas($featured->content ?? '', 220) }}
                            </p>
                        </div>

                        <div class="mt-space-lg pt-space-md bg-surface-container-low p-space-md rounded-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 font-label-sm text-label-sm text-primary">
                                    <span class="material-symbols-outlined text-[16px]">account_balance</span>
                                    <span>UPT PP-ESTEM IT Del</span>
                                </div>
                                @if (($featured->jumlah_lampiran ?? 0) > 0)
                                    <div class="flex items-center gap-1 font-label-sm text-label-sm text-secondary font-semibold">
                                        <span class="material-symbols-outlined text-[15px]">attach_file</span>
                                        <span>{{ $featured->jumlah_lampiran }} Dokumen Lampiran</span>
                                    </div>
                                @endif
                            </div>
                            <a class="inline-flex items-center justify-center gap-2 bg-primary-container text-on-primary hover:bg-primary transition px-space-md py-2.5 rounded font-label-lg text-label-lg shadow-sm no-underline"
                               href="{{ $featuredUrl }}">
                                <span>Baca Selengkapnya</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    @endif

    {{-- 4. DUAL-COLUMN LIST & SIDEBAR --}}
    <section class="w-full bg-surface-container py-space-xl">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin grid grid-cols-1 lg:grid-cols-12 gap-space-lg">

            {{-- KOLOM KIRI: DAFTAR KARTU --}}
            <div class="lg:col-span-8 flex flex-col gap-space-md">
                @if ($arsip)
                    <div class="bg-white px-4 py-3 rounded-lg shadow-sm flex items-center justify-between text-xs">
                        <span>Menampilkan arsip bulan: <strong class="text-primary">{{ $formatArsip($arsip) }}</strong></span>
                        <a href="{{ route('berita.index', array_filter(['kategori' => $kategori !== 'all' ? $kategori : null, 'q' => $q])) }}"
                           class="text-[#664780] font-semibold hover:underline">Hapus filter arsip</a>
                    </div>
                @endif

                @if ($pengumuman->isEmpty())
                    <div class="bg-surface-container-lowest p-space-xl rounded-lg text-center shadow-sm">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">search_off</span>
                        <p class="font-headline-sm text-headline-sm text-primary">Tidak Ada Pengumuman Ditemukan</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Belum ada pengumuman yang sesuai dengan filter atau kata kunci Anda.
                        </p>
                    </div>
                @elseif ($daftar->isNotEmpty())
                    @foreach ($daftar as $item)
                        @php
                            $isSeleksi = $item->type === 'result';
                            $itemUrl = $isSeleksi ? route('hasil.show', $item->slug) : route('berita.show', $item->slug);
                            $itemTgl = $item->published_at ?? $item->created_at;
                        @endphp
                        <article class="bg-surface-container-lowest rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col sm:flex-row relative group border border-outline-variant/20">
                            <div class="w-1.5 shrink-0 sm:block hidden {{ $isSeleksi ? 'bg-tertiary-container' : 'bg-tertiary' }}"></div>

                            <a href="{{ $itemUrl }}" class="sm:w-52 h-44 shrink-0 overflow-hidden bg-gradient-to-br from-[#003580] to-[#1a81c2] relative flex items-center justify-center no-underline">
                                @if ($item->cover_image)
                                    <img src="{{ file_url($item->cover_image) }}" alt="{{ $item->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <i class="bi {{ $isSeleksi ? 'bi-file-earmark-check' : 'bi-newspaper' }} text-white/30 text-5xl"></i>
                                @endif
                            </a>

                            <div class="p-space-md flex flex-col justify-between flex-1">
                                <div>
                                    <div class="flex items-center justify-between gap-space-xs mb-1.5 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded font-label-sm text-label-sm font-semibold {{ $isSeleksi ? 'bg-tertiary-container text-on-tertiary' : 'bg-tertiary text-on-tertiary' }}">
                                            {{ $isSeleksi ? 'Hasil Seleksi' : 'Berita' }}
                                        </span>
                                        @if ($itemTgl)
                                            <span class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant">
                                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                                {{ tgl($itemTgl) }}
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ $itemUrl }}" class="no-underline">
                                        <h3 class="font-headline-sm text-headline-sm text-primary group-hover:text-secondary transition-colors line-clamp-2 leading-snug">
                                            {{ $item->title }}
                                        </h3>
                                    </a>

                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1.5 line-clamp-2 leading-relaxed">
                                        {{ $item->summary ?: ringkas($item->content ?? '', 140) }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-space-xs mt-space-sm border-t border-surface-container">
                                    @if (($item->jumlah_lampiran ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-secondary font-medium truncate max-w-[220px]">
                                            <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                            {{ $item->jumlah_lampiran === 1 ? $item->nama_lampiran : $item->jumlah_lampiran . ' lampiran' }}
                                        </span>
                                    @else
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">UPT PP-ESTEM IT Del</span>
                                    @endif

                                    <a class="font-label-sm text-label-sm font-semibold text-primary hover:text-secondary inline-flex items-center gap-1 no-underline shrink-0"
                                       href="{{ $itemUrl }}">
                                        Baca Selengkapnya
                                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endif

                {{-- PAGINASI --}}
                @if ($pengumuman->lastPage() > 1)
                    @php $paginator = $pengumuman->withQueryString(); @endphp
                    <nav aria-label="Pagination" class="flex items-center justify-center gap-2 pt-space-lg">
                        @if ($paginator->onFirstPage())
                            <span class="w-10 h-10 rounded bg-surface-container-lowest/60 text-on-surface-variant/40 flex items-center justify-center cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman Sebelumnya"
                               class="w-10 h-10 rounded bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-highest transition flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </a>
                        @endif

                        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                            @if ($page === $paginator->currentPage())
                                <span class="w-10 h-10 rounded font-label-md text-label-md font-bold bg-secondary text-on-secondary shadow-sm flex items-center justify-center">{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}"
                                   class="w-10 h-10 rounded font-label-md text-label-md font-medium bg-surface-container-lowest text-on-surface hover:bg-surface-container-highest transition flex items-center justify-center no-underline">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman Selanjutnya"
                               class="w-10 h-10 rounded bg-surface-container-lowest text-primary hover:bg-surface-container-highest transition flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </a>
                        @else
                            <span class="w-10 h-10 rounded bg-surface-container-lowest/60 text-on-surface-variant/40 flex items-center justify-center cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>

            {{-- KOLOM KANAN: SIDEBAR --}}
            <aside class="lg:col-span-4 space-y-space-md">
                {{-- CARD 1: KATEGORI PENGUMUMAN --}}
                <div class="bg-surface-container-lowest rounded-lg p-space-md shadow-sm">
                    <div class="pb-space-xs mb-space-sm flex items-center justify-between border-b-2 border-secondary">
                        <h4 class="font-headline-sm text-headline-sm text-primary">Kategori Pengumuman</h4>
                        <span class="material-symbols-outlined text-secondary text-[20px]">category</span>
                    </div>
                    <ul class="space-y-1.5 font-label-md text-label-md list-none p-0 m-0">
                        <li>
                            <a href="{{ route('berita.index', array_filter(['q' => $q, 'arsip' => $arsip])) }}"
                               class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-container transition-colors text-on-surface no-underline {{ $kategori === 'all' ? 'bg-surface-container font-bold' : '' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    Semua Pengumuman
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-surface-container-high text-on-surface-variant">
                                    {{ $jumlahKategori->semua ?? 0 }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('berita.index', array_filter(['kategori' => 'berita', 'q' => $q, 'arsip' => $arsip])) }}"
                               class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-container transition-colors text-on-surface no-underline {{ $kategori === 'berita' ? 'bg-surface-container font-bold' : '' }}">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                                    Berita
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-tertiary-fixed text-on-tertiary-fixed">
                                    {{ $jumlahKategori->berita ?? 0 }}
                                </span>
                            </a>
                        </li>
                        @if ($bolehHasil)
                            <li>
                                <a href="{{ route('berita.index', array_filter(['kategori' => 'hasil', 'q' => $q, 'arsip' => $arsip])) }}"
                                   class="w-full flex items-center justify-between p-2 rounded hover:bg-surface-container transition-colors text-on-surface no-underline {{ $kategori === 'hasil' ? 'bg-surface-container font-bold' : '' }}">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-tertiary-container"></span>
                                        Hasil Seleksi
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-tertiary-fixed-dim text-on-tertiary-fixed">
                                        {{ $jumlahKategori->hasil ?? 0 }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- CARD 2: PENGUMUMAN TERBARU --}}
                <div class="bg-surface-container-lowest rounded-lg p-space-md shadow-sm">
                    <div class="pb-space-xs mb-space-sm flex items-center justify-between border-b-2 border-secondary">
                        <h4 class="font-headline-sm text-headline-sm text-primary flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[20px] text-secondary">history</span>
                            Pengumuman Terbaru
                        </h4>
                    </div>
                    @if ($terbaru->isEmpty())
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Belum ada pengumuman.</p>
                    @else
                        <div class="space-y-space-sm divide-y divide-surface-container">
                            @foreach ($terbaru as $t)
                                @php
                                    $tUrl = $t->type === 'result' ? route('hasil.show', $t->slug) : route('berita.show', $t->slug);
                                    $tTgl = $t->published_at ?? $t->created_at;
                                @endphp
                                <div class="pt-2 first:pt-0">
                                    @if ($tTgl)
                                        <span class="text-label-sm font-label-sm text-on-surface-variant flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">calendar_today</span> {{ tgl($tTgl) }}
                                        </span>
                                    @endif
                                    <a class="font-body-sm text-body-sm font-semibold text-primary hover:text-secondary transition-colors line-clamp-2 mt-0.5 leading-snug block no-underline"
                                       href="{{ $tUrl }}">
                                        {{ $t->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- CARD 3: ARSIP PUBLIKASI --}}
                <div class="bg-surface-container-lowest rounded-lg p-space-md shadow-sm">
                    <div class="pb-space-xs mb-space-sm flex items-center justify-between border-b-2 border-secondary">
                        <h4 class="font-headline-sm text-headline-sm text-primary flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[20px] text-secondary">archive</span>
                            Arsip Publikasi
                        </h4>
                    </div>
                    @if ($arsipBulan->isEmpty())
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Belum ada arsip publikasi.</p>
                    @else
                        <div class="grid grid-cols-2 gap-2 font-label-sm text-label-sm">
                            @foreach ($arsipBulan as $ab)
                                <a class="p-2 rounded flex items-center justify-between transition no-underline {{ $arsip === $ab->ym ? 'bg-secondary text-white font-semibold' : 'bg-surface-container-low hover:bg-surface-container text-on-surface' }}"
                                   href="{{ route('berita.index', array_filter(['kategori' => $kategori !== 'all' ? $kategori : null, 'arsip' => $ab->ym, 'q' => $q])) }}">
                                    <span>{{ $formatArsip($ab->ym) }}</span>
                                    <span class="font-semibold {{ $arsip === $ab->ym ? 'text-white' : 'text-primary' }}">({{ $ab->total }})</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- CARD 4: CTA MINI LOWONGAN --}}
                <div class="bg-gradient-to-br from-tertiary to-tertiary-container rounded-xl p-space-lg text-on-tertiary relative overflow-hidden shadow-md">
                    <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-[120px] text-on-tertiary/10 select-none pointer-events-none">work</span>
                    <div class="relative z-10">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-on-tertiary/20 font-label-sm text-label-sm text-tertiary-fixed mb-2">
                            <span class="material-symbols-outlined text-[13px]">lightbulb</span>
                            Peluang PDLK
                        </span>
                        <h3 class="font-headline-sm text-headline-sm text-on-tertiary font-bold leading-tight">
                            Cari Lowongan PDLK?
                        </h3>
                        <p class="font-body-sm text-body-sm text-tertiary-fixed mt-2 leading-relaxed">
                            Temukan peluang magang industri, kerja praktik, riset, dan kegiatan PDLK lainnya bersama mitra IT Del.
                        </p>
                        <a class="mt-space-md inline-flex items-center justify-center gap-2 bg-surface-container-lowest text-tertiary font-label-lg text-label-lg font-bold px-space-md py-2.5 rounded shadow hover:bg-surface-container transition-all no-underline"
                           href="{{ route('lowongan.index') }}">
                            <span>Lihat Lowongan PDLK</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </aside>

        </div>
    </section>

</div>
@endsection
