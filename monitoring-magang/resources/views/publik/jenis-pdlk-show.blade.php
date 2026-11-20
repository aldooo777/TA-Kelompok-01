@extends('layouts.publik')

@section('title', $jenis->name . ' - Jenis PDLK')

@section('fullwidth')
@php
    $ikonJenis = function (string $slug): string {
        return match ($slug) {
            'magang' => 'bi-briefcase',
            'kerja-praktik' => 'bi-tools',
            'penelitian' => 'bi-search',
            'pertukaran-pelajar' => 'bi-arrow-left-right',
            'kkn' => 'bi-house-heart',
            'studi-independen' => 'bi-lightbulb',
            'kewirausahaan' => 'bi-shop',
            default => 'bi-journal-bookmark',
        };
    };

    $ikonSekarang = $ikonJenis($jenis->slug);

    // Pisahkan deskripsi menjadi paragraf berdasarkan baris baru
    $paragrafDeskripsi = collect(preg_split('/\r?\n+/', trim((string) ($jenis->description ?? ''))))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();

    // Pisahkan tujuan (objectives) menjadi poin per baris (hapus prefix nomor/strip bila ada)
    $poinTujuan = collect(preg_split('/\r?\n+/', trim((string) ($jenis->objectives ?? ''))))
        ->map(fn ($p) => trim(preg_replace('/^(\d+[\.\)]\s*|[-•*]\s*)/', '', $p)))
        ->filter()
        ->values();

    // Navigasi program sebelumnya & berikutnya
    $idxSekarang = $semuaJenis->search(fn ($item) => $item->id === $jenis->id);
    $programLain = $semuaJenis->reject(fn ($item) => $item->id === $jenis->id)->values();
    $sebelumnya = ($semuaJenis->count() > 1 && $idxSekarang !== false)
        ? $semuaJenis[($idxSekarang - 1 + $semuaJenis->count()) % $semuaJenis->count()]
        : null;
    $berikutnya = ($semuaJenis->count() > 1 && $idxSekarang !== false)
        ? $semuaJenis[($idxSekarang + 1) % $semuaJenis->count()]
        : null;
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative w-full overflow-hidden bg-primary min-h-[220px] flex flex-col justify-between">
        <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-25"
             style="background-image: url('{{ asset('images/kampus-del.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary-container via-primary to-secondary opacity-90"></div>

        <div class="relative z-10 max-w-[1280px] w-full mx-auto px-margin-mobile lg:px-margin pt-8 pb-4">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-label-sm text-label-sm text-secondary-container mb-3">
                <a class="hover:text-surface-container-lowest transition-colors flex items-center gap-1 text-secondary-container" href="{{ route('beranda') }}">
                    <i class="bi bi-house-door text-xs"></i>
                    <span>Beranda</span>
                </a>
                <i class="bi bi-chevron-right text-[10px] opacity-70"></i>
                <a class="hover:text-surface-container-lowest transition-colors text-secondary-container" href="{{ route('jenis-pdlk') }}">
                    <span>Jenis PDLK</span>
                </a>
                <i class="bi bi-chevron-right text-[10px] opacity-70"></i>
                <span class="text-surface-container-lowest font-semibold">{{ $jenis->name }}</span>
            </nav>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-surface-container-lowest flex items-center justify-center shadow-md flex-shrink-0">
                        <i class="bi {{ $ikonSekarang }} text-primary-container text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold bg-tertiary-container text-on-tertiary">
                                JENIS PDLK
                            </span>
                            <span class="font-label-sm text-label-sm text-secondary-fixed opacity-90 uppercase">
                                {{ $jenis->slug }}
                            </span>
                        </div>
                        <h1 class="font-headline-xl text-headline-xl text-surface-container-lowest font-bold tracking-tight leading-tight">
                            {{ $jenis->name }}
                        </h1>
                    </div>
                </div>

                @if ($jenis->summary)
                    <div class="max-w-xl">
                        <p class="font-body-md text-body-md text-secondary-fixed leading-relaxed">
                            {{ $jenis->summary }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="relative w-full leading-none overflow-hidden z-10 -mb-[1px]">
            <svg class="relative block w-full h-8 lg:h-10" preserveAspectRatio="none" viewBox="0 0 1200 120">
                <path class="fill-secondary-container opacity-50" d="M0,0 C180,60 380,10 600,45 C820,80 1020,30 1200,60 L1200,120 L0,120 Z"></path>
                <path class="fill-surface-container-lowest" d="M0,35 C150,75 350,25 540,65 C730,105 920,40 1200,75 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </section>

    {{-- 2. ISI DUA KOLOM --}}
    <section class="w-full bg-surface-container-lowest py-8 lg:py-12">
        <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin">
            <div class="flex flex-col lg:flex-row gap-gutter">

                {{-- KOLOM KIRI (±65%) --}}
                <div class="w-full lg:w-[65%] flex flex-col">
                    {{-- Foto Utama Kegiatan --}}
                    <div class="w-full rounded-2xl overflow-hidden shadow-[0_4px_20px_rgba(0,53,128,0.08)] bg-gradient-to-br from-[#003580] to-[#1a81c2] aspect-video relative group flex items-center justify-center">
                        @if ($jenis->image)
                            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                                 src="{{ file_url($jenis->image) }}" alt="{{ $jenis->name }}">
                        @else
                            <div class="flex flex-col items-center justify-center text-white/80 p-6 text-center">
                                <i class="bi {{ $ikonSekarang }} text-7xl text-white/40 mb-3"></i>
                                <span class="font-headline-md text-headline-md text-white font-semibold">{{ $jenis->name }}</span>
                            </div>
                        @endif
                        <div class="absolute bottom-3 right-3 bg-primary/80 backdrop-blur-sm text-surface-container-lowest font-label-sm text-label-sm px-3 py-1 rounded-md flex items-center gap-1.5">
                            <i class="bi bi-shield-check text-secondary-container"></i>
                            <span>Program {{ $jenis->name }} · UPT PP-ESTEM IT Del</span>
                        </div>
                    </div>

                    {{-- TENTANG KEGIATAN (Deskripsi dari database) --}}
                    <article class="mt-8">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-label-sm text-label-sm font-bold tracking-widest text-tertiary-container uppercase">
                                TENTANG KEGIATAN
                            </span>
                        </div>
                        <h2 class="font-headline-lg text-headline-lg font-bold text-primary-container tracking-tight">
                            Mengenal Program {{ $jenis->name }}
                        </h2>
                        <div class="flex items-center gap-1 mt-2.5 mb-5">
                            <div class="w-10 h-1 bg-secondary rounded-full"></div>
                            <div class="w-5 h-1 bg-tertiary-container rounded-full"></div>
                        </div>

                        <div class="space-y-4 font-body-md text-body-md text-on-surface leading-relaxed text-justify">
                            @if ($paragrafDeskripsi->isNotEmpty())
                                @foreach ($paragrafDeskripsi as $p)
                                    <p>{{ $p }}</p>
                                @endforeach
                            @elseif ($jenis->summary)
                                <p>{{ $jenis->summary }}</p>
                            @else
                                <p class="text-on-surface-variant">Deskripsi lengkap untuk kegiatan {{ $jenis->name }} belum ditambahkan oleh Staf UPT PP-ESTEM.</p>
                            @endif
                        </div>
                    </article>

                    {{-- CAPAIAN & KOMPETENSI (Tujuan dari database: objectives) --}}
                    <article class="mt-10">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-label-sm text-label-sm font-bold tracking-widest text-tertiary-container uppercase">
                                CAPAIAN &amp; KOMPETENSI
                            </span>
                        </div>
                        <h2 class="font-headline-lg text-headline-lg font-bold text-primary-container tracking-tight">
                            Tujuan Program {{ $jenis->name }}
                        </h2>
                        <div class="flex items-center gap-1 mt-2.5 mb-6">
                            <div class="w-10 h-1 bg-secondary rounded-full"></div>
                            <div class="w-5 h-1 bg-tertiary-container rounded-full"></div>
                        </div>

                        @if ($poinTujuan->isNotEmpty())
                            <div class="flex flex-col gap-3">
                                @foreach ($poinTujuan as $tujuan)
                                    @php $isGenap = $loop->iteration % 2 === 0; @endphp
                                    <div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow duration-200">
                                        <div class="w-8 h-8 rounded-full text-surface-container-lowest flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm {{ $isGenap ? 'bg-tertiary-container' : 'bg-secondary' }}">
                                            <i class="bi bi-check-lg text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-body-md text-body-md text-on-surface font-medium leading-relaxed">
                                                {{ $tujuan }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 rounded-xl bg-surface-container-low text-on-surface-variant font-body-md text-body-md">
                                Tujuan khusus kegiatan {{ $jenis->name }} belum ditambahkan oleh Staf UPT PP-ESTEM.
                            </div>
                        @endif
                    </article>
                </div>

                {{-- KOLOM KANAN (±35%, Sticky Desktop) --}}
                <aside class="w-full lg:w-[35%] flex flex-col gap-6">
                    <div class="lg:sticky lg:top-28 flex flex-col gap-6">

                        {{-- KARTU 1: Informasi Kegiatan --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-md overflow-hidden border border-outline-variant/20">
                            <div class="bg-gradient-to-r from-tertiary-container to-[#664780] px-5 py-3.5 flex items-center gap-2.5 text-on-tertiary">
                                <i class="bi bi-info-circle text-lg"></i>
                                <h3 class="font-headline-sm text-headline-sm font-bold tracking-tight">
                                    Informasi Kegiatan
                                </h3>
                            </div>
                            <div class="p-5 flex flex-col gap-3 font-body-sm text-body-sm">
                                <div class="flex items-center justify-between py-2 bg-surface-container-low px-3 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-secondary/15 flex items-center justify-center">
                                            <i class="bi bi-clock text-secondary text-sm"></i>
                                        </div>
                                        <span class="text-on-surface-variant font-medium">Durasi Kegiatan</span>
                                    </div>
                                    <span class="font-label-md text-label-md font-bold text-primary text-right">
                                        {{ $jenis->duration_text ?: 'Sesuai program' }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-2 bg-surface-container-low px-3 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-tertiary-container/15 flex items-center justify-center">
                                            <i class="bi bi-megaphone text-tertiary-container text-sm"></i>
                                        </div>
                                        <span class="text-on-surface-variant font-medium">Lowongan Dibuka</span>
                                    </div>
                                    <span class="font-label-sm text-label-sm font-bold bg-tertiary-container text-on-tertiary px-2.5 py-0.5 rounded-full">
                                        {{ $jumlahLowonganAktif }} Posisi Aktif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-2 bg-surface-container-low px-3 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-secondary/15 flex items-center justify-center">
                                            <i class="bi bi-people text-secondary text-sm"></i>
                                        </div>
                                        <span class="text-on-surface-variant font-medium">Peserta Aktif</span>
                                    </div>
                                    <span class="font-label-md text-label-md font-bold text-on-surface">
                                        {{ $jumlahPeserta }} Mahasiswa
                                    </span>
                                </div>

                                <div class="flex items-center justify-between py-2 bg-surface-container-low px-3 rounded-lg">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-md bg-tertiary-container/15 flex items-center justify-center">
                                            <i class="bi bi-award text-tertiary-container text-sm"></i>
                                        </div>
                                        <span class="text-on-surface-variant font-medium">Konversi SKS</span>
                                    </div>
                                    <span class="font-label-md text-label-md font-bold text-primary-container text-right">
                                        Sesuai Ketentuan Prodi
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- KARTU 2: Lowongan Terkait (Dinamis dari database) --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-md p-5 relative overflow-hidden border border-outline-variant/20">
                            <div class="absolute top-0 left-0 right-0 h-1 bg-secondary"></div>

                            <div class="flex items-center justify-between mb-4 mt-1">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-briefcase-fill text-secondary"></i>
                                    <h3 class="font-headline-sm text-headline-sm font-bold text-primary">
                                        Lowongan {{ $jenis->name }}
                                    </h3>
                                </div>
                                @if ($jumlahLowonganAktif > 0)
                                    <span class="inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @endif
                            </div>

                            @if ($lowonganTerkait->isEmpty())
                                <div class="p-4 rounded-lg bg-surface-container-low text-center text-on-surface-variant font-body-sm text-body-sm">
                                    Belum ada lowongan {{ $jenis->name }} yang dipublikasikan.
                                </div>
                            @else
                                <div class="flex flex-col gap-3 font-body-sm text-body-sm">
                                    @foreach ($lowonganTerkait as $low)
                                        @php $buka = lowongan_dibuka($low); @endphp
                                        <div class="p-3 rounded-lg bg-surface-container-low hover:bg-secondary-fixed/30 transition-colors">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-surface-container-lowest shadow-sm flex items-center justify-center flex-shrink-0 text-secondary">
                                                    <i class="bi bi-building text-lg"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <a class="font-label-md text-label-md font-bold text-primary hover:text-secondary block truncate"
                                                       href="{{ route('lowongan.show', $low->slug) }}">
                                                        {{ $low->title }}
                                                    </a>
                                                    <p class="text-on-surface-variant truncate">{{ $low->partner_name }}</p>
                                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-surface-dim/40 font-label-sm text-label-sm">
                                                        <span class="text-on-surface-variant flex items-center gap-1">
                                                            <i class="bi bi-calendar-event text-secondary"></i>
                                                            {{ $low->registration_deadline ? tgl($low->registration_deadline) : 'Tanpa batas' }}
                                                        </span>
                                                        @if ($buka)
                                                            <span class="text-emerald-700 font-semibold">Dibuka</span>
                                                        @else
                                                            <span class="text-error font-bold">Ditutup</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <a class="mt-4 w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded bg-primary-container hover:bg-secondary text-surface-container-lowest font-label-md text-label-md font-semibold transition-all duration-200 shadow-sm"
                               href="{{ route('lowongan.index', ['jenis' => $jenis->slug]) }}">
                                <span>Lihat Semua Lowongan {{ $jenis->name }}</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        {{-- KARTU 3: Eksplorasi Program Lain (Dinamis dari database) --}}
                        @if ($programLain->isNotEmpty())
                            <div class="bg-surface-container-lowest rounded-xl shadow-md p-4 border border-outline-variant/20">
                                <div class="flex items-center justify-between pb-3 mb-3 border-b border-surface-dim">
                                    <h3 class="font-label-lg text-label-lg font-bold text-primary tracking-tight">
                                        Eksplorasi Program Lain
                                    </h3>
                                    <i class="bi bi-grid-3x3-gap text-secondary-container"></i>
                                </div>

                                <div class="flex flex-col gap-1.5 font-label-md text-label-md">
                                    @foreach ($programLain as $lain)
                                        @php $isGenap = $loop->iteration % 2 === 0; @endphp
                                        <a class="flex items-center justify-between p-2.5 rounded-lg text-on-surface hover:bg-secondary-container/20 hover:text-primary-container transition-all group"
                                           href="{{ route('jenis-pdlk.show', $lain->slug) }}">
                                            <div class="flex items-center gap-2.5">
                                                <i class="bi {{ $ikonJenis($lain->slug) }} text-base group-hover:scale-110 transition-transform {{ $isGenap ? 'text-tertiary-container' : 'text-secondary' }}"></i>
                                                <span>{{ $lain->name }}</span>
                                            </div>
                                            <i class="bi bi-chevron-right text-xs opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- 3. NAVIGASI BAWAH: PROGRAM SEBELUMNYA & BERIKUTNYA --}}
    @if ($sebelumnya && $berikutnya)
        <section class="w-full bg-[#dce3eb] py-10 lg:py-12">
            <div class="max-w-[1280px] mx-auto px-margin-mobile lg:px-margin">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <a class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 flex items-center justify-between overflow-hidden hover:-translate-x-1"
                       href="{{ route('jenis-pdlk.show', $sebelumnya->slug) }}">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-secondary"></div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center text-xl group-hover:bg-secondary group-hover:text-surface-container-lowest transition-colors duration-300 shrink-0">
                                <i class="bi bi-arrow-left"></i>
                            </div>
                            <div>
                                <span class="inline-block font-label-sm text-label-sm font-semibold uppercase tracking-wider text-secondary mb-1">
                                    Program Sebelumnya
                                </span>
                                <h4 class="font-headline-sm text-headline-sm font-bold text-primary group-hover:text-secondary transition-colors">
                                    {{ $sebelumnya->name }}
                                </h4>
                                @if ($sebelumnya->summary)
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5 line-clamp-1">
                                        {{ $sebelumnya->summary }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <i class="bi {{ $ikonJenis($sebelumnya->slug) }} text-2xl text-secondary opacity-40 group-hover:opacity-100 transition-opacity hidden sm:block shrink-0 ml-3"></i>
                    </a>

                    <a class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 flex items-center justify-between overflow-hidden hover:translate-x-1"
                       href="{{ route('jenis-pdlk.show', $berikutnya->slug) }}">
                        <div class="absolute right-0 top-0 bottom-0 w-1 bg-tertiary-container"></div>
                        <div class="flex items-center gap-4 order-2 sm:order-1 text-left">
                            <div>
                                <span class="inline-block font-label-sm text-label-sm font-semibold uppercase tracking-wider text-tertiary-container mb-1">
                                    Program Berikutnya
                                </span>
                                <h4 class="font-headline-sm text-headline-sm font-bold text-primary group-hover:text-tertiary-container transition-colors">
                                    {{ $berikutnya->name }}
                                </h4>
                                @if ($berikutnya->summary)
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5 line-clamp-1">
                                        {{ $berikutnya->summary }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-tertiary-container/10 text-tertiary-container flex items-center justify-center text-xl group-hover:bg-tertiary-container group-hover:text-surface-container-lowest transition-colors duration-300 order-1 sm:order-2 flex-shrink-0">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    @endif

</div>
@endsection
