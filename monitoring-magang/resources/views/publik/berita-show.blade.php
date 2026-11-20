@extends('layouts.publik')

@section('title', $berita->title)

@section('fullwidth')
@php
    $tglPublikasi = $berita->published_at ?? $berita->created_at;
    $paragrafIsi = collect(preg_split('/\r?\n+/', trim((string) ($berita->content ?? ''))))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER HERO DETAIL BERITA --}}
    <section class="relative w-full overflow-hidden bg-gradient-to-r from-[#003580] to-[#0055A5] pt-8 pb-16" style="min-height: 240px;">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 md:px-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-body-sm text-[#8cc5dd] mb-3 flex-wrap">
                <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                <span class="text-white/60">/</span>
                <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('berita.index') }}">Pengumuman</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-medium">Berita</span>
            </nav>

            <div class="mb-3">
                <span class="inline-block px-3 py-1 rounded-full bg-[#46166c] text-white font-label-sm text-[12px] font-semibold tracking-wide uppercase shadow-sm">
                    Berita
                </span>
            </div>

            <h1 class="text-white font-bold text-2xl md:text-3xl max-w-4xl tracking-tight leading-snug mb-4">
                {{ $berita->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-white/90 text-body-sm font-label-sm">
                @if ($tglPublikasi)
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-[#8cc5dd]"></i>
                        {{ tgl($tglPublikasi) }}
                    </span>
                    <span class="text-white/40">•</span>
                @endif
                <span class="inline-flex items-center gap-1.5">
                    <i class="bi bi-buildings text-[#8cc5dd]"></i>
                    UPT PP-ESTEM IT Del
                </span>
                @if ($lampiran->isNotEmpty())
                    <span class="text-white/40">•</span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="bi bi-paperclip text-[#8cc5dd] text-base"></i>
                        {{ $lampiran->count() }} lampiran
                    </span>
                @endif
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 leading-none pointer-events-none -mb-1">
            <svg class="w-full h-8 block text-white" preserveAspectRatio="none" viewBox="0 0 1440 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,24 C320,40 640,10 960,28 C1200,40 1360,20 1440,24 L1440,40 L0,40 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    {{-- 2. KONTEN UTAMA DUA KOLOM --}}
    <section class="w-full bg-white py-10 px-4 md:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            {{-- KOLOM KIRI --}}
            <article class="lg:col-span-8 flex flex-col">
                @if ($berita->cover_image)
                    <div class="rounded-2xl shadow-sm border border-slate-200 overflow-hidden w-full relative bg-slate-100 mb-6" style="aspect-ratio: 16/9;">
                        <img alt="{{ $berita->title }}" class="w-full h-full object-cover" src="{{ file_url($berita->cover_image) }}">
                    </div>
                @endif

                @if ($berita->summary)
                    <div class="border-l-4 border-[#46166c] bg-[#dce3eb] p-5 rounded-r-lg mb-6 shadow-sm">
                        <p class="text-lg italic text-slate-800 leading-relaxed font-normal m-0">
                            {{ $berita->summary }}
                        </p>
                    </div>
                @endif

                <div class="text-slate-700 space-y-4 font-body-md text-base leading-relaxed text-justify">
                    @forelse ($paragrafIsi as $p)
                        <p>{{ $p }}</p>
                    @empty
                        <p class="text-slate-500">Isi berita belum tersedia.</p>
                    @endforelse
                </div>

                {{-- Lampiran --}}
                @if ($lampiran->isNotEmpty())
                    <div class="space-y-3 my-6">
                        @foreach ($lampiran as $f)
                            @php
                                $ukuran = $f->file_size
                                    ? ($f->file_size >= 1048576 ? number_format($f->file_size / 1048576, 2) . ' MB' : max(1, (int) round($f->file_size / 1024)) . ' KB')
                                    : null;
                            @endphp
                            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-2xl shrink-0 border border-red-100">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-semibold text-slate-800 text-sm md:text-base truncate">{{ $f->file_name }}</span>
                                        @if ($ukuran)
                                            <span class="text-xs text-slate-500 mt-0.5">{{ $ukuran }} · Dokumen Lampiran</span>
                                        @endif
                                    </div>
                                </div>
                                <a class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-[#003580] text-[#003580] hover:bg-[#003580] hover:text-white transition-colors font-medium text-sm shrink-0 no-underline"
                                   href="{{ file_url($f->file_path) }}" target="_blank" rel="noopener">
                                    <i class="bi bi-download"></i>
                                    <span>Unduh</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="border-t border-slate-200 my-6"></div>

                {{-- Baris Bagikan --}}
                <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-sm text-slate-700">Bagikan:</span>
                        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition-colors no-underline"
                           href="https://wa.me/?text={{ urlencode($berita->title . ' - ' . url()->current()) }}" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp text-sm"></i> WhatsApp
                        </a>
                        <a class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-semibold transition-colors no-underline"
                           href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener">
                            <i class="bi bi-facebook text-sm"></i> Facebook
                        </a>
                        <button id="copyLinkBeritaBtn"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 text-xs font-medium transition-colors"
                                type="button">
                            <i class="bi bi-clipboard"></i> <span id="copyLinkBeritaText">Salin Tautan</span>
                        </button>
                    </div>
                    <a class="text-xs text-[#003580] font-semibold hover:underline inline-flex items-center gap-1" href="{{ route('berita.index') }}">
                        <i class="bi bi-arrow-left"></i> Kembali ke Pengumuman
                    </a>
                </div>

                {{-- Navigasi Bawah --}}
                @if ($sebelumnya || $berikutnya)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div>
                            @if ($sebelumnya)
                                @php $urlPrev = $sebelumnya->type === 'result' ? route('hasil.show', $sebelumnya->slug) : route('berita.show', $sebelumnya->slug); @endphp
                                <a class="border-l-4 border-[#1a81c2] bg-slate-50 hover:bg-slate-100 p-4 rounded-lg flex flex-col justify-between transition-colors shadow-sm block group no-underline h-full"
                                   href="{{ $urlPrev }}">
                                    <span class="text-xs text-slate-500 font-medium mb-1 group-hover:text-[#1a81c2] transition-colors">← Pengumuman Lebih Baru</span>
                                    <h4 class="font-bold text-sm text-slate-800 leading-snug line-clamp-2 m-0">{{ $sebelumnya->title }}</h4>
                                </a>
                            @endif
                        </div>
                        <div>
                            @if ($berikutnya)
                                @php $urlNext = $berikutnya->type === 'result' ? route('hasil.show', $berikutnya->slug) : route('berita.show', $berikutnya->slug); @endphp
                                <a class="border-r-4 border-[#46166c] bg-slate-50 hover:bg-slate-100 p-4 rounded-lg flex flex-col justify-between transition-colors shadow-sm block text-right group no-underline h-full"
                                   href="{{ $urlNext }}">
                                    <span class="text-xs text-slate-500 font-medium mb-1 group-hover:text-[#46166c] transition-colors">Pengumuman Sebelumnya →</span>
                                    <h4 class="font-bold text-sm text-slate-800 leading-snug line-clamp-2 m-0">{{ $berikutnya->title }}</h4>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </article>

            {{-- KOLOM KANAN SIDEBAR --}}
            <aside class="lg:col-span-4 lg:sticky lg:top-28 space-y-6">
                {{-- Kategori Pengumuman --}}
                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <h3 class="font-bold text-base text-[#003580] mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Kategori Pengumuman</span>
                        <i class="bi bi-folder2-open text-[#1a81c2]"></i>
                    </h3>
                    <ul class="space-y-2 text-sm list-none p-0 m-0">
                        <li>
                            <a class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors no-underline"
                               href="{{ route('berita.index') }}">
                                <span>Semua Pengumuman</span>
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $jumlahKategori->semua ?? 0 }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex items-center justify-between px-3 py-2 rounded-lg bg-[#46166c]/10 text-[#46166c] font-bold border border-[#46166c]/20 transition-colors no-underline"
                               href="{{ route('berita.index', ['kategori' => 'berita']) }}">
                                <span>Berita &amp; Informasi</span>
                                <span class="px-2 py-0.5 rounded-full bg-[#46166c] text-white text-xs font-bold">{{ $jumlahKategori->berita ?? 0 }}</span>
                            </a>
                        </li>
                        @if ($bolehHasil)
                            <li>
                                <a class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors no-underline"
                                   href="{{ route('berita.index', ['kategori' => 'hasil']) }}">
                                    <span>Hasil Seleksi</span>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $jumlahKategori->hasil ?? 0 }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- Pengumuman Terbaru --}}
                <div class="border border-slate-200 rounded-xl p-5 bg-white shadow-sm">
                    <h3 class="font-bold text-base text-[#003580] mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Pengumuman Terbaru</span>
                        <i class="bi bi-bell text-[#1a81c2]"></i>
                    </h3>
                    <div class="space-y-3.5">
                        @forelse ($terbaru as $t)
                            @php
                                $tSeleksi = $t->type === 'result';
                                $tUrl = $tSeleksi ? route('hasil.show', $t->slug) : route('berita.show', $t->slug);
                                $tTgl = $t->published_at ?? $t->created_at;
                            @endphp
                            <a class="block pb-3 border-b border-slate-100 last:border-b-0 last:pb-0 hover:text-[#1a81c2] transition-colors group no-underline" href="{{ $tUrl }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white {{ $tSeleksi ? 'bg-[#664780]' : 'bg-[#46166c]' }}">
                                        {{ $tSeleksi ? 'Hasil Seleksi' : 'Berita' }}
                                    </span>
                                    @if ($tTgl)
                                        <span class="text-[11px] text-slate-400">{{ tgl($tTgl) }}</span>
                                    @endif
                                </div>
                                <h4 class="font-medium text-xs text-slate-800 group-hover:text-[#1a81c2] line-clamp-2 leading-snug m-0">{{ $t->title }}</h4>
                            </a>
                        @empty
                            <p class="text-xs text-slate-500">Belum ada pengumuman terbaru.</p>
                        @endforelse
                    </div>
                </div>

                {{-- CTA Lowongan --}}
                <div class="rounded-xl p-6 text-white shadow-md relative overflow-hidden bg-gradient-to-br from-[#46166c] to-[#664780]">
                    <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center text-2xl mb-4 shadow-sm backdrop-blur-sm">
                        <i class="bi bi-rocket-takeoff-fill"></i>
                    </div>
                    <h4 class="font-bold text-lg mb-2 text-white">Cari lowongan PDLK?</h4>
                    <p class="text-xs text-white/90 leading-relaxed mb-5">
                        Temukan beragam peluang magang industri, riset, dan kegiatan PDLK lainnya untuk mahasiswa IT Del.
                    </p>
                    <a class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-white text-[#46166c] font-semibold text-sm rounded-lg hover:bg-white/90 shadow transition-all no-underline"
                       href="{{ route('lowongan.index') }}">
                        <i class="bi bi-briefcase-fill text-xs"></i>
                        <span>Lihat Lowongan PDLK</span>
                    </a>
                </div>
            </aside>
        </div>
    </section>

    {{-- 3. SECTION BERITA LAINNYA --}}
    @if ($lainnya->isNotEmpty())
        <section class="w-full bg-[#dce3eb] py-14 px-4 md:px-8 border-t border-slate-200">
            <div class="max-w-7xl mx-auto">
                <div class="mb-8 flex flex-col">
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">BACA JUGA</span>
                    <h2 class="font-headline-lg text-2xl md:text-3xl text-slate-900 font-bold tracking-tight mb-2">Berita Lainnya</h2>
                    <div class="flex items-center gap-1 mt-1 mb-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($lainnya as $l)
                        @php $lTgl = $l->published_at ?? $l->created_at; @endphp
                        <div class="flex flex-col bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group hover:shadow-md transition-all duration-300">
                            <div class="w-full relative overflow-hidden bg-gradient-to-br from-[#003580] to-[#1a81c2] flex items-center justify-center" style="aspect-ratio: 4/3;">
                                @if ($l->cover_image)
                                    <img alt="{{ $l->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ file_url($l->cover_image) }}" loading="lazy">
                                @else
                                    <i class="bi bi-newspaper text-white/30 text-5xl"></i>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#46166c] text-white font-label-sm text-[11px] font-semibold shadow-sm">Berita</span>
                                </div>
                            </div>
                            <div class="p-5 flex flex-col flex-1 justify-between">
                                <div>
                                    @if ($lTgl)
                                        <span class="text-xs text-slate-500 mb-1.5 flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ tgl($lTgl) }}</span>
                                    @endif
                                    <h3 class="font-bold text-[#003580] text-base leading-snug line-clamp-2 group-hover:text-[#1a81c2] transition-colors">
                                        {{ $l->title }}
                                    </h3>
                                </div>
                                <a class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-[#003580] group-hover:text-[#1a81c2] transition-colors no-underline"
                                   href="{{ route('berita.show', $l->slug) }}">
                                    <span>Baca Selengkapnya</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-10">
                    <a class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#003580] hover:bg-[#002155] text-white font-medium text-sm shadow transition-colors mx-auto no-underline"
                       href="{{ route('berita.index') }}">
                        <span>Lihat Semua Pengumuman</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const btn = document.getElementById('copyLinkBeritaBtn');
        const txt = document.getElementById('copyLinkBeritaText');
        if (btn && txt) {
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    txt.textContent = 'Tautan Tersalin!';
                    setTimeout(() => { txt.textContent = 'Salin Tautan'; }, 2000);
                });
            });
        }
    })();
</script>
@endpush
