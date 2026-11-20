@extends('layouts.publik')

@section('title', 'Galeri Kegiatan PDLK')

@section('fullwidth')
@php
    $galeri = $galeri ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 9);
    $daftarTahun = $daftarTahun ?? collect();
    $tahun = $tahun ?? '';
    $q = $q ?? '';
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER --}}
    <section class="relative w-full overflow-hidden"
             style="background: linear-gradient(rgba(0, 53, 128, 0.9), rgba(0, 85, 165, 0.92)), url('{{ asset('images/kampus-del.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin pt-10 pb-16">
            <div class="flex flex-col items-start">
                <div class="flex items-center gap-2 text-sm text-[#8cc5dd] mb-3">
                    <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                    <span>/</span>
                    <span class="text-white font-semibold">Galeri</span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-[#46166c] text-white font-label-sm text-label-sm uppercase tracking-wider mb-3 shadow-sm font-semibold">
                    <i class="bi bi-images"></i> DOKUMENTASI
                </span>
                <h1 class="text-white font-display-lg text-display-lg tracking-tight leading-tight mb-2">
                    Galeri Kegiatan PDLK
                </h1>
                <p class="text-white/90 font-body-lg text-body-lg max-w-3xl leading-relaxed">
                    Dokumentasi visual dedikasi mahasiswa, bimbingan dosen pembimbing lapangan, dan kemitraan kolaboratif di dunia industri.
                </p>
            </div>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-1">
            <svg class="w-full h-12 block" preserveAspectRatio="none" viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,20 Q360,50 720,20 T1440,20 L1440,60 L0,60 Z" fill="#ffffff"></path>
            </svg>
        </div>
    </section>

    {{-- 2. FILTER BAR --}}
    <section class="w-full bg-white border-b border-outline-variant/30 py-6 px-margin-mobile md:px-margin shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            {{-- Tab Pills Tahun (Dinamis dari database) --}}
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                <a href="{{ route('galeri.index', array_filter(['q' => $q])) }}"
                   class="px-5 py-2 rounded-full font-medium text-sm transition-all shrink-0 no-underline {{ $tahun === '' ? 'bg-[#1a81c2] text-white shadow-sm' : 'bg-white border border-outline-variant/40 text-on-surface-variant hover:bg-surface-container' }}">
                    Semua
                </a>
                @foreach ($daftarTahun as $rowTahun)
                    @php $th = (string) $rowTahun->tahun; @endphp
                    <a href="{{ route('galeri.index', array_filter(['tahun' => $th, 'q' => $q])) }}"
                       class="px-5 py-2 rounded-full font-medium text-sm transition-all shrink-0 no-underline {{ $tahun === $th ? 'bg-[#1a81c2] text-white shadow-sm' : 'bg-white border border-outline-variant/40 text-on-surface-variant hover:bg-surface-container' }}">
                        {{ $th }}
                    </a>
                @endforeach
            </div>

            {{-- Kotak Pencarian --}}
            <form method="GET" action="{{ route('galeri.index') }}" class="relative w-full md:w-80">
                @if ($tahun !== '')
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                @endif
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm"></i>
                <input name="q" value="{{ $q }}"
                       class="w-full pl-10 pr-16 py-2 text-sm rounded-lg border border-outline-variant/40 bg-surface focus:bg-white focus:outline-none focus:border-[#1a81c2] transition-colors text-on-surface"
                       placeholder="Cari dokumentasi atau nama kegiatan..." type="text">
                @if ($q !== '' || $tahun !== '')
                    <a href="{{ route('galeri.index') }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#664780] font-semibold hover:underline">Reset</a>
                @endif
            </form>
        </div>
    </section>

    {{-- 3. DAFTAR ALBUM --}}
    <section class="w-full bg-[#dce3eb] py-12 px-margin-mobile md:px-margin flex-1">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">ARSIP DOKUMENTASI TERPADU</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Koleksi Dokumentasi &amp; Rekam Jejak PDLK</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
            </div>

            @if ($galeri->isEmpty())
                <div class="bg-white rounded-xl p-10 text-center text-on-surface-variant shadow-sm">
                    <i class="bi bi-images text-4xl text-outline/40 block mb-2"></i>
                    <p class="font-headline-sm text-headline-sm text-[#003580]">Belum ada galeri kegiatan yang sesuai</p>
                    <p class="font-body-sm text-body-sm mt-1">Dokumentasi yang dipublikasikan oleh Staf UPT PP-ESTEM akan tampil di sini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach ($galeri as $g)
                        @php
                            $fotoCount = (int) ($g->jumlah_foto ?? 0);
                            $videoCount = (int) ($g->jumlah_video ?? 0);
                            $badgeParts = [];
                            if ($fotoCount > 0) $badgeParts[] = $fotoCount . ' Foto';
                            if ($videoCount > 0) $badgeParts[] = $videoCount . ' Video';
                            $badgeLabel = $badgeParts !== [] ? implode(' · ', $badgeParts) : 'Album';
                        @endphp

                        <a href="{{ route('galeri.show', $g->id) }}"
                           class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group h-full no-underline">
                            <div class="w-full relative overflow-hidden shrink-0 bg-gradient-to-br from-[#003580] to-[#1a81c2] flex items-center justify-center" style="aspect-ratio: 4/3;">
                                @if ($g->cover_image)
                                    <img alt="{{ $g->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ file_url($g->cover_image) }}" loading="lazy">
                                @else
                                    <i class="bi bi-images text-white/30 text-6xl"></i>
                                @endif

                                <span class="absolute top-0 left-0 bg-[#46166c] text-white text-xs font-semibold px-3 py-1.5 rounded-br-lg shadow-md flex items-center gap-1.5 z-10">
                                    <i class="bi bi-images"></i> {{ $badgeLabel }}
                                </span>

                                <div class="absolute inset-0 bg-[#1a81c2]/75 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white z-20">
                                    <span class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm mb-2">
                                        <i class="bi bi-zoom-in text-2xl"></i>
                                    </span>
                                    <span class="font-semibold text-sm tracking-wide">Buka Album</span>
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    @if ($g->event_date)
                                        <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1.5 mb-1.5">
                                            <i class="bi bi-calendar3 text-[#1a81c2]"></i> {{ tgl($g->event_date) }}
                                        </span>
                                    @endif
                                    <h3 class="font-headline-sm text-headline-sm text-[#003580] font-bold line-clamp-2 mb-2 group-hover:text-[#1a81c2] transition-colors leading-snug">
                                        {{ $g->title }}
                                    </h3>
                                </div>
                                @if ($g->description)
                                    <p class="text-xs md:text-sm text-on-surface-variant line-clamp-2 mt-2">
                                        {{ ringkas($g->description, 110) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Paginasi --}}
                @if ($galeri->lastPage() > 1)
                    @php $paginator = $galeri->withQueryString(); @endphp
                    <div class="mt-12 flex justify-center items-center gap-2">
                        @if ($paginator->onFirstPage())
                            <span class="w-10 h-10 rounded-lg border border-outline-variant/40 bg-white/60 text-on-surface-variant/40 flex items-center justify-center cursor-not-allowed">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Halaman Sebelumnya"
                               class="w-10 h-10 rounded-lg border border-outline-variant/40 bg-white text-on-surface-variant hover:bg-surface-container flex items-center justify-center transition-colors">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                            @if ($page === $paginator->currentPage())
                                <span class="w-10 h-10 rounded-lg bg-[#1a81c2] text-white font-bold flex items-center justify-center shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}"
                                   class="w-10 h-10 rounded-lg border border-outline-variant/40 bg-white text-on-surface-variant hover:bg-surface-container flex items-center justify-center font-medium transition-colors no-underline">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Halaman Selanjutnya"
                               class="w-10 h-10 rounded-lg border border-outline-variant/40 bg-white text-on-surface-variant hover:bg-surface-container flex items-center justify-center transition-colors">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="w-10 h-10 rounded-lg border border-outline-variant/40 bg-white/60 text-on-surface-variant/40 flex items-center justify-center cursor-not-allowed">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>

</div>
@endsection
