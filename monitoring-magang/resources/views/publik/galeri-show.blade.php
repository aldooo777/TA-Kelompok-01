@extends('layouts.publik')

@section('title', $galeri->title . ' - Galeri PDLK')

@section('fullwidth')
@php
    $items = $items ?? collect();
    $lainnya = $lainnya ?? collect();

    $jumlahFoto = $items->where('media_type', 'image')->count();
    $jumlahVideo = $items->where('media_type', 'video')->count();
    $totalMedia = $items->count();

    $badgeMedia = [];
    if ($jumlahFoto > 0) $badgeMedia[] = $jumlahFoto . ' Foto';
    if ($jumlahVideo > 0) $badgeMedia[] = $jumlahVideo . ' Video';
    $ringkasanMedia = $badgeMedia !== [] ? implode(' · ', $badgeMedia) : '0 Media';

    $mediaJs = $items->values()->map(function ($it, $idx) use ($galeri) {
        return [
            'index' => $idx,
            'type' => $it->media_type === 'video' ? 'video' : 'photo',
            'url' => file_url($it->file_path),
            'caption' => $it->caption ?: $galeri->title,
        ];
    })->all();
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER HERO --}}
    <section class="relative w-full bg-gradient-to-r from-primary via-primary-container to-secondary text-on-primary pt-10 pb-20 px-margin overflow-hidden shadow-sm">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

        <div class="max-w-[1280px] mx-auto relative z-10 flex flex-col gap-space-sm">
            <nav class="flex items-center gap-space-xs font-body-sm text-body-sm text-secondary-fixed flex-wrap">
                <a class="text-on-primary hover:text-secondary-fixed-dim transition-colors flex items-center gap-1" href="{{ route('beranda') }}">
                    <span class="material-symbols-outlined text-[16px]">home</span> Beranda
                </a>
                <span>/</span>
                <a class="text-on-primary hover:text-secondary-fixed-dim transition-colors" href="{{ route('galeri.index') }}">Galeri</a>
                <span>/</span>
                <span class="text-secondary-fixed font-medium truncate">{{ $galeri->title }}</span>
            </nav>

            <div class="mt-space-xs">
                <span class="inline-flex items-center gap-1.5 bg-tertiary text-on-tertiary font-label-sm text-label-sm px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                    <span class="material-symbols-outlined text-[14px]">photo_library</span> GALERI
                </span>
            </div>

            <h1 class="font-headline-xl text-headline-xl text-on-primary tracking-tight leading-snug max-w-4xl">
                {{ $galeri->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-y-2 gap-x-space-lg text-secondary-fixed font-label-md text-label-md pt-space-xs">
                @if ($galeri->event_date)
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-secondary-fixed-dim">calendar_today</span>
                        <span>{{ tgl($galeri->event_date) }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px] text-secondary-fixed-dim">perm_media</span>
                    <span>{{ $ringkasanMedia }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px] text-secondary-fixed-dim">apartment</span>
                    <span>UPT PP-ESTEM Institut Teknologi Del</span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 w-full overflow-hidden leading-none pointer-events-none -mb-px">
            <svg class="relative block w-full h-8 text-surface-container-lowest" preserveAspectRatio="none" viewBox="0 0 1200 120">
                <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    {{-- 2. DESKRIPSI KEGIATAN --}}
    @if ($galeri->description)
        <section class="w-full bg-surface-container-lowest py-space-xl px-margin">
            <div class="max-w-[1280px] mx-auto">
                <div class="bg-surface-container-low border-l-4 border-tertiary p-space-lg rounded-r-xl shadow-sm flex flex-col md:flex-row items-start gap-space-md">
                    <div class="p-2.5 bg-primary/10 text-primary rounded-lg shrink-0 hidden sm:flex items-center justify-center">
                        <span class="material-symbols-outlined text-[32px]">info</span>
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <span class="font-label-lg text-label-lg text-primary tracking-wide">Ringkasan Dokumentasi Kegiatan</span>
                        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed text-justify m-0">
                            {{ $galeri->description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 3. MEDIA DOKUMENTASI --}}
    <section class="w-full bg-surface-container-highest py-space-xl px-margin">
        <div class="max-w-[1280px] mx-auto flex flex-col gap-space-lg">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-space-md">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-tertiary font-bold">DOKUMENTASI MEDIA</span>
                    <h2 class="font-headline-lg text-headline-lg text-primary mt-1">Foto &amp; Video Kegiatan</h2>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="w-10 h-1 bg-secondary rounded-full"></span>
                        <span class="w-5 h-1 bg-tertiary rounded-full"></span>
                    </div>
                </div>

                @if ($items->isNotEmpty())
                    <div class="flex items-center gap-2 overflow-x-auto pb-1" id="filter-container">
                        <button class="filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-label-md text-label-md bg-secondary text-on-secondary shadow-sm transition-all"
                                onclick="filterGallery('all', this)" type="button">
                            <span class="material-symbols-outlined text-[16px]">grid_view</span>
                            Semua ({{ $totalMedia }})
                        </button>
                        <button class="filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-label-md text-label-md bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high transition-all shadow-sm"
                                onclick="filterGallery('photo', this)" type="button">
                            <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                            Foto ({{ $jumlahFoto }})
                        </button>
                        <button class="filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-label-md text-label-md bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high transition-all shadow-sm"
                                onclick="filterGallery('video', this)" type="button">
                            <span class="material-symbols-outlined text-[16px]">play_circle</span>
                            Video ({{ $jumlahVideo }})
                        </button>
                    </div>
                @endif
            </div>

            @if ($items->isEmpty())
                <div class="bg-surface-container-lowest rounded-xl p-10 text-center text-on-surface-variant shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-outline/50 block mb-2">photo_library</span>
                    <p class="font-headline-sm text-headline-sm text-primary">Belum ada foto atau video di album ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-gutter mt-2" id="gallery-grid">
                    @foreach ($items as $idx => $it)
                        @php
                            $isVideo = $it->media_type === 'video';
                            $mediaUrl = file_url($it->file_path);
                        @endphp
                        <div class="gallery-card group flex flex-col bg-surface-container-lowest p-2 pb-3 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
                             data-type="{{ $isVideo ? 'video' : 'photo' }}"
                             onclick="openLightbox({{ $idx }})">
                            <div class="relative w-full aspect-[4/3] rounded-lg overflow-hidden {{ $isVideo ? 'bg-primary' : 'bg-surface-container-high' }}">
                                @if ($isVideo)
                                    <video src="{{ $mediaUrl }}" preload="metadata" muted class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"></video>
                                    <div class="absolute inset-0 bg-primary/40 flex items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-tertiary text-on-tertiary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-2 left-2 bg-tertiary text-on-tertiary px-2 py-0.5 rounded text-label-sm font-label-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">videocam</span> Video
                                    </div>
                                @else
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         src="{{ $mediaUrl }}" alt="{{ $it->caption ?: $galeri->title }}" loading="lazy">
                                @endif

                                <div class="absolute inset-0 bg-secondary/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center gap-1 text-on-secondary transition-opacity duration-200">
                                    <span class="material-symbols-outlined text-[32px]">{{ $isVideo ? 'open_in_full' : 'zoom_in' }}</span>
                                    <span class="font-label-md text-label-md font-semibold">Buka Media</span>
                                </div>
                            </div>

                            <p class="font-body-sm text-body-sm text-on-surface-variant font-medium mt-2 px-1 line-clamp-2 m-0">
                                {{ $it->caption ?: $galeri->title }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 4. ALBUM DOKUMENTASI LAINNYA --}}
    <section class="w-full bg-surface-container-lowest py-space-xl px-margin">
        <div class="max-w-[1280px] mx-auto flex flex-col gap-space-lg">
            @if ($lainnya->isNotEmpty())
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-tertiary font-bold">EKSPLORASI DOKUMENTASI</span>
                    <h2 class="font-headline-lg text-headline-lg text-primary mt-1">Album Dokumentasi Lainnya</h2>
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="w-10 h-1 bg-secondary rounded-full"></span>
                        <span class="w-5 h-1 bg-tertiary rounded-full"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mt-2">
                    @foreach ($lainnya as $l)
                        @php
                            $lFoto = (int) ($l->jumlah_foto ?? 0);
                            $lVideo = (int) ($l->jumlah_video ?? 0);
                            $lParts = [];
                            if ($lFoto > 0) $lParts[] = $lFoto . ' Foto';
                            if ($lVideo > 0) $lParts[] = $lVideo . ' Video';
                            $lBadge = $lParts !== [] ? implode(' · ', $lParts) : 'Album';
                        @endphp
                        <a class="group flex flex-col bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 border-t-[3px] border-tertiary no-underline"
                           href="{{ route('galeri.show', $l->id) }}">
                            <div class="relative w-full aspect-video bg-gradient-to-br from-[#003580] to-[#1a81c2] overflow-hidden flex items-center justify-center">
                                @if ($l->cover_image)
                                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ file_url($l->cover_image) }}" alt="{{ $l->title }}" loading="lazy">
                                @else
                                    <span class="material-symbols-outlined text-white/30 text-5xl">photo_library</span>
                                @endif
                                <div class="absolute bottom-2 left-2 bg-primary/90 text-on-primary text-label-sm font-label-sm px-2.5 py-1 rounded shadow">
                                    {{ $lBadge }}
                                </div>
                            </div>
                            <div class="p-space-md flex flex-col gap-1.5">
                                @if ($l->event_date)
                                    <div class="flex items-center gap-1 text-on-surface-variant font-body-sm text-body-sm">
                                        <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                        <span>{{ tgl($l->event_date) }}</span>
                                    </div>
                                @endif
                                <h3 class="font-headline-sm text-headline-sm text-primary group-hover:text-secondary transition-colors line-clamp-2 m-0">
                                    {{ $l->title }}
                                </h3>
                                @if ($l->description)
                                    <p class="font-body-sm text-body-sm text-on-surface-variant truncate m-0">
                                        {{ ringkas($l->description, 90) }}
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-center mt-4">
                <a class="inline-flex items-center justify-center gap-2 bg-primary-container text-on-primary hover:bg-primary font-label-lg text-label-lg px-6 py-3 rounded-lg shadow-sm transition-colors duration-150 no-underline"
                   href="{{ route('galeri.index') }}">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    <span>Kembali ke Galeri</span>
                </a>
            </div>
        </div>
    </section>

    {{-- LIGHTBOX MODAL --}}
    <div class="fixed inset-0 z-50 bg-black/90 hidden flex-col justify-between p-4 sm:p-6 backdrop-blur-sm" id="lightbox-modal">
        <div class="flex items-center justify-between text-white max-w-6xl w-full mx-auto">
            <div class="flex items-center gap-2 font-label-md text-label-md">
                <span class="bg-tertiary px-2.5 py-1 rounded font-mono text-white" id="lightbox-counter">1 / 1</span>
                <span class="text-secondary-fixed text-sm hidden sm:inline" id="lightbox-tag">Foto Kegiatan</span>
            </div>
            <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors"
                    onclick="closeLightbox()" title="Tutup (Esc)" type="button">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
        </div>

        <div class="relative flex items-center justify-center w-full max-w-5xl mx-auto my-auto h-[70vh]">
            <button class="absolute left-0 sm:-left-12 z-20 w-11 h-11 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/30 text-white transition-all shadow-lg"
                    onclick="prevLightbox()" title="Sebelumnya" type="button">
                <span class="material-symbols-outlined text-[28px]">chevron_left</span>
            </button>

            <div class="w-full h-full flex items-center justify-center relative p-2">
                <img class="max-w-full max-h-full object-contain rounded shadow-2xl hidden" id="lightbox-img" src="" alt="">
                <video class="max-w-full max-h-full rounded shadow-2xl hidden" id="lightbox-video" controls playsinline></video>
            </div>

            <button class="absolute right-0 sm:-right-12 z-20 w-11 h-11 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/30 text-white transition-all shadow-lg"
                    onclick="nextLightbox()" title="Berikutnya" type="button">
                <span class="material-symbols-outlined text-[28px]">chevron_right</span>
            </button>
        </div>

        <div class="max-w-4xl w-full mx-auto text-center pb-2">
            <p class="text-white font-body-md text-body-md bg-black/60 px-4 py-2.5 rounded-lg inline-block m-0" id="lightbox-caption"></p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const mediaItems = @json($mediaJs);
    let currentIdx = 0;

    function filterGallery(type, btnElement) {
        const cards = document.querySelectorAll('.gallery-card');
        const buttons = document.querySelectorAll('.filter-btn');

        buttons.forEach(btn => {
            btn.classList.remove('bg-secondary', 'text-on-secondary');
            btn.classList.add('bg-surface-container-lowest', 'text-on-surface-variant');
        });

        btnElement.classList.remove('bg-surface-container-lowest', 'text-on-surface-variant');
        btnElement.classList.add('bg-secondary', 'text-on-secondary');

        cards.forEach(card => {
            card.style.display = (type === 'all' || card.getAttribute('data-type') === type) ? 'flex' : 'none';
        });
    }

    function openLightbox(idx) {
        if (!mediaItems.length) return;
        currentIdx = idx;
        updateLightboxContent();
        const modal = document.getElementById('lightbox-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const modal = document.getElementById('lightbox-modal');
        const video = document.getElementById('lightbox-video');
        if (video) {
            video.pause();
            video.removeAttribute('src');
            video.load();
        }
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function nextLightbox() {
        if (!mediaItems.length) return;
        currentIdx = (currentIdx + 1) % mediaItems.length;
        updateLightboxContent();
    }

    function prevLightbox() {
        if (!mediaItems.length) return;
        currentIdx = (currentIdx - 1 + mediaItems.length) % mediaItems.length;
        updateLightboxContent();
    }

    function updateLightboxContent() {
        const item = mediaItems[currentIdx];
        if (!item) return;

        const img = document.getElementById('lightbox-img');
        const video = document.getElementById('lightbox-video');
        const counter = document.getElementById('lightbox-counter');
        const caption = document.getElementById('lightbox-caption');
        const tag = document.getElementById('lightbox-tag');

        counter.textContent = (currentIdx + 1) + ' / ' + mediaItems.length;
        caption.textContent = item.caption || '';

        if (item.type === 'video') {
            tag.textContent = 'Video Kegiatan';
            img.classList.add('hidden');
            img.removeAttribute('src');
            video.src = item.url;
            video.classList.remove('hidden');
        } else {
            tag.textContent = 'Foto Dokumentasi';
            video.pause();
            video.classList.add('hidden');
            video.removeAttribute('src');
            img.src = item.url;
            img.alt = item.caption || '';
            img.classList.remove('hidden');
        }
    }

    document.getElementById('lightbox-modal')?.addEventListener('click', function (e) {
        if (e.target === this) closeLightbox();
    });

    window.addEventListener('keydown', function (e) {
        const modal = document.getElementById('lightbox-modal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
        }
    });
</script>
@endpush
