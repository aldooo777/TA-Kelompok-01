<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi PDLK') - UPT PP-ESTEM Institut Teknologi Del</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "inverse-surface": "#303030",
                        "on-secondary-fixed-variant": "#00468a",
                        "primary-fixed": "#d9e2ff",
                        "inverse-primary": "#b0c6ff",
                        "outline-variant": "#c4c6d3",
                        "surface": "#fbf9f8",
                        "secondary-container": "#72a9ff",
                        "surface-variant": "#e4e2e2",
                        "outline": "#747782",
                        "surface-container-lowest": "#ffffff",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "tertiary-fixed-dim": "#e0b7ff",
                        "primary-fixed-dim": "#b0c6ff",
                        "on-tertiary-fixed-variant": "#5e3085",
                        "surface-container-high": "#eae8e7",
                        "tertiary-fixed": "#f2daff",
                        "on-surface": "#1b1c1c",
                        "on-secondary-fixed": "#001b3c",
                        "secondary": "#175eae",
                        "on-background": "#1b1c1c",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed": "#d6e3ff",
                        "tertiary": "#39025f",
                        "surface-container-highest": "#e4e2e2",
                        "on-primary": "#ffffff",
                        "primary": "#002155",
                        "on-primary-fixed-variant": "#1a438e",
                        "tertiary-container": "#502176",
                        "on-error": "#ffffff",
                        "surface-container": "#f0eded",
                        "surface-container-low": "#f5f3f3",
                        "on-error-container": "#93000a",
                        "on-primary-container": "#7fa1f2",
                        "surface-bright": "#fbf9f8",
                        "surface-dim": "#dbd9d9",
                        "on-secondary": "#ffffff",
                        "on-tertiary-container": "#c08ee9",
                        "on-tertiary-fixed": "#2d004f",
                        "on-primary-fixed": "#001945",
                        "primary-container": "#003580",
                        "on-surface-variant": "#434651",
                        "background": "#fbf9f8",
                        "secondary-fixed-dim": "#a8c8ff",
                        "inverse-on-surface": "#f2f0f0",
                        "on-secondary-container": "#003d79",
                        "surface-tint": "#375ca8"
                    },
                    borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
                    spacing: { "margin": "2rem", "space-xl": "2.5rem", "space-xs": "0.25rem", "space-lg": "1.5rem", "gutter": "1.5rem", "margin-mobile": "1rem", "space-sm": "0.5rem", "space-md": "1rem", "gutter-mobile": "1rem" },
                    fontFamily: {
                        "headline-sm": ["Hanken Grotesk", "sans-serif"],
                        "label-md": ["Hanken Grotesk", "sans-serif"],
                        "display-lg": ["Hanken Grotesk", "sans-serif"],
                        "label-lg": ["Hanken Grotesk", "sans-serif"],
                        "label-sm": ["Hanken Grotesk", "sans-serif"],
                        "headline-xl": ["Hanken Grotesk", "sans-serif"],
                        "headline-xl-mobile": ["Hanken Grotesk", "sans-serif"],
                        "body-lg": ["Hanken Grotesk", "sans-serif"],
                        "body-md": ["Hanken Grotesk", "sans-serif"],
                        "display-lg-mobile": ["Hanken Grotesk", "sans-serif"],
                        "headline-lg": ["Hanken Grotesk", "sans-serif"],
                        "headline-md": ["Hanken Grotesk", "sans-serif"],
                        "body-sm": ["Hanken Grotesk", "sans-serif"]
                    },
                    fontSize: {
                        "headline-sm": ["18px", { "lineHeight": "26px", "fontWeight": "600" }],
                        "label-md": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
                        "display-lg": ["48px", { "lineHeight": "56px", "fontWeight": "700" }],
                        "label-lg": ["14px", { "lineHeight": "20px", "fontWeight": "600" }],
                        "label-sm": ["11px", { "lineHeight": "14px", "fontWeight": "500" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "fontWeight": "700" }],
                        "headline-xl-mobile": ["26px", { "lineHeight": "34px", "fontWeight": "700" }],
                        "body-lg": ["16px", { "lineHeight": "26px", "fontWeight": "400" }],
                        "body-md": ["14px", { "lineHeight": "22px", "fontWeight": "400" }],
                        "display-lg-mobile": ["32px", { "lineHeight": "40px", "fontWeight": "700" }],
                        "headline-lg": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                        "headline-md": ["22px", { "lineHeight": "30px", "fontWeight": "600" }],
                        "body-sm": ["12px", { "lineHeight": "18px", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('css/pdlk-app.css') }}">
    <style>
        :root {
            --accent: #1a81c2;
            --accent-dark: #003580;
            --accent-soft: #e7f3fa;
            --text: #354052;
            --muted: #7b8797;
            --border: #e2e6eb;
        }
        html, body { margin: 0; padding: 0; }
        body { overscroll-behavior: none; }
        .pub-container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
        @media (min-width: 768px) { .pub-container { padding: 0 2rem; } }

        /* Kompatibilitas halaman publik selain Beranda (Lowongan, Berita, Galeri, Tentang) */
        .pub-page-head { background: linear-gradient(135deg, #003580 0%, #1a81c2 100%); color: #fff; padding: 32px 0; }
        .pub-page-head h1 { margin: 0; font-size: 28px; font-weight: 700; color: #fff; }
        .pub-page-head p { margin: 8px 0 0; opacity: .92; line-height: 1.5; }
        .pub-crumb { font-size: 13px; margin-bottom: 8px; opacity: .88; }
        .pub-crumb a { color: #fff; text-decoration: underline; }
        .pub-main { padding: 28px 0 48px; }
        .pub-section + .pub-section { margin-top: 36px; }
        .pub-section-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; margin-bottom: 14px; }
        .pub-section-head h2 { margin: 0; font-size: 20px; font-weight: 700; color: #003580; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface min-h-screen flex flex-col">

    {{-- HEADER & NAVBAR --}}
    <header class="fixed top-0 left-0 right-0 z-50 shadow-[0_2px_8px_rgba(0,53,128,0.12)]">
        {{-- Top Bar Ungu --}}
        <div class="bg-[#46166c] text-on-tertiary">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin h-9 flex items-center justify-between font-label-sm text-label-sm">
                <span>Institut Teknologi Del · UPT PP-ESTEM</span>
                @if (session()->has('cis_user'))
                    <a class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded border border-white text-white hover:bg-white hover:text-[#46166c] transition-colors" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                    </a>
                @else
                    <a class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded border border-white text-white hover:bg-white hover:text-[#46166c] transition-colors" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i><span>Login</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Main Navbar Biru --}}
        <div class="bg-[#1a81c2] text-white">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin h-20 flex items-center justify-between">
                <a href="{{ route('beranda') }}" class="flex items-center gap-3 text-white no-underline">
                    <img alt="Logo Institut Teknologi Del" class="h-12 w-auto object-contain" src="{{ asset('images/logo-del.png') }}">
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm font-bold text-white tracking-tight leading-tight">UPT PP-ESTEM</span>
                        <span class="font-label-sm text-label-sm text-white/90 font-normal">Sistem Informasi PDLK</span>
                    </div>
                </a>

                <div class="flex items-center gap-4">
                    <nav class="hidden lg:flex items-center gap-1 font-label-lg text-label-lg">
                        <a href="{{ route('beranda') }}"
                           class="px-2.5 py-1.5 transition-colors {{ request()->routeIs('beranda') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}">
                            Beranda
                        </a>

                        <div class="relative group">
                            <button class="px-2.5 py-1.5 transition-colors inline-flex items-center gap-1 {{ request()->routeIs('tentang', 'struktur') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}" type="button">
                                Profil <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </button>
                            <div class="absolute left-0 top-full hidden group-hover:block bg-[#1a81c2] shadow-lg rounded-lg py-2 min-w-[220px] z-50 border-t-2 border-[#46166c]">
                                <a class="block px-4 py-2 text-white hover:bg-white/10 hover:text-[#8cc5dd] text-label-md font-label-md" href="{{ route('tentang') }}">Visi, Misi &amp; Tujuan</a>
                                <a class="block px-4 py-2 text-white hover:bg-white/10 hover:text-[#8cc5dd] text-label-md font-label-md" href="{{ route('struktur') }}">Struktur Organisasi</a>
                            </div>
                        </div>

                        <a href="{{ route('jenis-pdlk') }}"
                        class="px-2.5 py-1.5 transition-colors {{ request()->routeIs('jenis-pdlk*') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}">
                            Jenis PDLK
                        </a>
                        <a href="{{ route('lowongan.index') }}"
                           class="px-2.5 py-1.5 transition-colors {{ request()->routeIs('lowongan.*') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}">
                            Lowongan
                        </a>

                        <div class="relative group">
                            <button class="px-2.5 py-1.5 transition-colors inline-flex items-center gap-1 {{ request()->routeIs('berita.*', 'hasil.*') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}" type="button">
                                Pengumuman <span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </button>
                            <div class="absolute left-0 top-full hidden group-hover:block bg-[#1a81c2] shadow-lg rounded-lg py-2 min-w-[200px] z-50 border-t-2 border-[#46166c]">
                                <a class="block px-4 py-2 text-white hover:bg-white/10 hover:text-[#8cc5dd] text-label-md font-label-md" href="{{ route('berita.index') }}">Berita</a>
                                @if (sprint(2))
                                    <a class="block px-4 py-2 text-white hover:bg-white/10 hover:text-[#8cc5dd] text-label-md font-label-md" href="{{ route('hasil.index') }}">Hasil Seleksi</a>
                                @endif
                            </div>
                        </div>

                        <a href="{{ request()->routeIs('beranda') ? '#mitra' : route('beranda') . '#mitra' }}"
                           class="px-2.5 py-1.5 text-white hover:text-[#8cc5dd] transition-colors">
                            Mitra
                        </a>

                        <a href="{{ route('galeri.index') }}"
                           class="px-2.5 py-1.5 transition-colors {{ request()->routeIs('galeri.*') ? 'bg-white/20 text-white font-bold rounded-lg' : 'text-white hover:text-[#8cc5dd]' }}">
                            Galeri
                        </a>

                        <a href="{{ request()->routeIs('beranda') ? '#rekapitulasi' : route('beranda') . '#rekapitulasi' }}"
                           class="px-2.5 py-1.5 text-white hover:text-[#8cc5dd] transition-colors">
                            Rekapitulasi Data
                        </a>
                    </nav>

                    <a href="{{ session()->has('cis_user') ? route('dashboard') : route('login') }}"
                       title="{{ session()->has('cis_user') ? 'Ke Dashboard' : 'Login CIS' }}"
                       class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 hover:bg-primary-container transition-colors">
                        <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                    </a>

                    <button aria-label="Toggle Navigation" class="lg:hidden p-2 text-white focus:outline-none" id="navbar-toggle-btn" type="button">
                        <span class="material-symbols-outlined text-[28px]">menu</span>
                    </button>
                </div>
            </div>

            {{-- Mobile Nav Menu --}}
            <div class="hidden lg:hidden bg-[#1a81c2] border-t border-white/10 px-margin-mobile py-4 font-label-md text-label-md" id="mobile-nav-menu">
                <nav class="flex flex-col gap-1">
                    <a class="px-3 py-2 rounded {{ request()->routeIs('beranda') ? 'bg-white/20 text-white font-bold' : 'text-white hover:bg-white/10' }}" href="{{ route('beranda') }}">Beranda</a>
                    <div class="px-3 py-1 text-white/70 font-semibold">Profil</div>
                    <a class="pl-6 pr-3 py-1.5 text-white hover:bg-white/10 rounded" href="{{ route('tentang') }}">Visi, Misi &amp; Tujuan</a>
                    <a class="pl-6 pr-3 py-1.5 rounded {{ request()->routeIs('struktur') ? 'bg-white/20 text-white font-bold' : 'text-white hover:bg-white/10' }}" href="{{ route('struktur') }}">Struktur Organisasi</a>
                    <a class="px-3 py-2 rounded {{ request()->routeIs('jenis-pdlk*') ? 'bg-white/20 text-white font-bold' : 'text-white hover:bg-white/10' }}" href="{{ route('jenis-pdlk') }}">Jenis PDLK</a>
                    <a class="px-3 py-2 rounded {{ request()->routeIs('lowongan.*') ? 'bg-white/20 text-white font-bold' : 'text-white hover:bg-white/10' }}" href="{{ route('lowongan.index') }}">Lowongan</a>
                    <div class="px-3 py-1 text-white/70 font-semibold">Pengumuman</div>
                    <a class="pl-6 pr-3 py-1.5 text-white hover:bg-white/10 rounded" href="{{ route('berita.index') }}">Berita</a>
                    @if (sprint(2))
                        <a class="pl-6 pr-3 py-1.5 text-white hover:bg-white/10 rounded" href="{{ route('hasil.index') }}">Hasil Seleksi</a>
                    @endif
                    <a class="px-3 py-2 text-white hover:bg-white/10 rounded" href="{{ request()->routeIs('beranda') ? '#mitra' : route('beranda') . '#mitra' }}">Mitra</a>
                    <a class="px-3 py-2 rounded {{ request()->routeIs('galeri.*') ? 'bg-white/20 text-white font-bold' : 'text-white hover:bg-white/10' }}" href="{{ route('galeri.index') }}">Galeri</a>
                    <a class="px-3 py-2 text-white hover:bg-white/10 rounded" href="{{ request()->routeIs('beranda') ? '#rekapitulasi' : route('beranda') . '#rekapitulasi' }}">Rekapitulasi Data</a>
                </nav>
            </div>
        </div>
    </header>

    {{-- KONTEN HALAMAN --}}
    <div class="pt-[116px] flex-1 flex flex-col">
        @yield('hero')

        @hasSection('fullwidth')
            @if (session('success') || session('warning'))
                <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin pt-6 w-full">
                    @if (session('success'))
                        <div class="p-alert p-alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('warning'))
                        <div class="p-alert p-alert-warning">{{ session('warning') }}</div>
                    @endif
                </div>
            @endif
            @yield('fullwidth')
        @endif

        @hasSection('content')
            <main class="pub-main flex-1">
                <div class="pub-container">
                    @if (session('success'))
                        <div class="p-alert p-alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
                    @endif
                    @if (session('warning'))
                        <div class="p-alert p-alert-warning" style="margin-bottom:20px">{{ session('warning') }}</div>
                    @endif

                    @yield('content')
                </div>
            </main>
        @endif
    </div>

    {{-- FOOTER --}}
    @php
        $kontakFooter = \App\Support\Sql::all('SELECT * FROM contacts WHERE is_active = true ORDER BY display_order, name LIMIT 6');
        $sosialFooter = $kontakFooter->filter(fn ($k) => in_array($k->contact_type, ['instagram', 'website', 'other'], true));
    @endphp
    <footer class="w-full bg-[#003580] text-white relative">
        <div class="w-full h-1 bg-[#46166c]"></div>
        <div class="w-full overflow-hidden leading-none bg-surface">
            <svg class="w-full h-10 block text-[#003580]" preserveAspectRatio="none" viewBox="0 0 1440 48" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,0 C240,40 480,48 720,24 C960,0 1200,40 1440,16 L1440,48 L0,48 Z" fill="currentColor"></path>
            </svg>
        </div>
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin pt-4 pb-space-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
                {{-- Kolom 1: Identitas --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <img alt="Logo Institut Teknologi Del" class="h-10 w-auto object-contain bg-white/10 p-1 rounded" src="{{ asset('images/logo-del.png') }}">
                        <div class="flex flex-col">
                            <span class="font-headline-sm text-headline-sm font-bold text-white">UPT PP-ESTEM</span>
                            <span class="font-label-sm text-label-sm text-white/80">Institut Teknologi Del</span>
                        </div>
                    </div>
                    <p class="font-body-sm text-body-sm text-white/90 font-semibold">Sistem Informasi Pembelajaran di Luar Kampus</p>
                    <p class="font-body-sm text-body-sm text-white/75 leading-relaxed">
                        Unit Pelaksana Teknis Pengelolaan Program ESTEM IT Del yang memfasilitasi program pembelajaran kolaboratif luar kampus guna memperkuat kompetensi saintifik dan profesionalisme mahasiswa.
                    </p>
                </div>

                {{-- Kolom 2: Tautan Cepat --}}
                <div class="flex flex-col gap-2">
                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold mb-2 border-b border-white/20 pb-2">Tautan Cepat</h4>
                    <ul class="flex flex-col gap-1.5 font-body-sm text-body-sm list-none p-0 m-0">
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('beranda') }}">Beranda</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('tentang') }}">Visi, Misi &amp; Tujuan</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('struktur') }}">Struktur Organisasi</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('jenis-pdlk') }}">Jenis PDLK</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('lowongan.index') }}">Lowongan</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('berita.index') }}">Pengumuman &amp; Berita</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ request()->routeIs('beranda') ? '#mitra' : route('beranda') . '#mitra' }}">Mitra</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ route('galeri.index') }}">Galeri</a></li>
                        <li class="flex items-center gap-1"><i class="bi bi-chevron-right text-white/60 text-[10px]"></i><a class="text-white/85 hover:text-[#8cc5dd] transition-colors" href="{{ request()->routeIs('beranda') ? '#rekapitulasi' : route('beranda') . '#rekapitulasi' }}">Rekapitulasi Data</a></li>
                    </ul>
                </div>

                {{-- Kolom 3: Kontak Kami (Dinamis dari tabel contacts) --}}
                <div class="flex flex-col gap-2">
                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold mb-2 border-b border-white/20 pb-2">Kontak Kami</h4>
                    <div class="flex flex-col gap-2.5 font-body-sm text-body-sm text-white/85">
                        @forelse ($kontakFooter as $k)
                            @php
                                $ikonKontak = match ($k->contact_type) {
                                    'address' => 'bi-geo-alt',
                                    'email' => 'bi-envelope',
                                    'whatsapp' => 'bi-whatsapp',
                                    'phone' => 'bi-telephone',
                                    'instagram' => 'bi-instagram',
                                    'website' => 'bi-globe',
                                    default => 'bi-info-circle',
                                };
                            @endphp
                            <div class="flex items-start gap-2.5">
                                <i class="bi {{ $ikonKontak }} text-[#8cc5dd] text-base shrink-0 mt-0.5"></i>
                                <span>{{ $k->contact_value }}</span>
                            </div>
                        @empty
                            <div class="flex items-start gap-2.5"><i class="bi bi-geo-alt text-[#8cc5dd] text-base shrink-0 mt-0.5"></i><span>Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba, Sumatera Utara</span></div>
                            <div class="flex items-center gap-2.5"><i class="bi bi-envelope text-[#8cc5dd] text-base shrink-0"></i><span>estem@del.ac.id</span></div>
                            <div class="flex items-center gap-2.5"><i class="bi bi-telephone text-[#8cc5dd] text-base shrink-0"></i><span>(0632) 331234</span></div>
                        @endforelse
                    </div>
                </div>

                {{-- Kolom 4: Media Sosial --}}
                <div class="flex flex-col gap-2">
                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold mb-2 border-b border-white/20 pb-2">Media Sosial</h4>
                    <p class="font-body-sm text-body-sm text-white/75 mb-3">Ikuti kanal informasi dan pembaruan resmi UPT PP-ESTEM IT Del.</p>
                    <div class="flex items-center gap-3">
                        @if ($sosialFooter->isNotEmpty())
                            @foreach ($sosialFooter as $s)
                                <a aria-label="{{ $s->name }}" target="_blank" rel="noopener"
                                   class="w-9 h-9 rounded bg-white/10 hover:bg-[#8cc5dd] hover:text-[#003580] flex items-center justify-center transition-colors text-white text-base"
                                   href="{{ str_starts_with($s->contact_value, 'http') ? $s->contact_value : '#' }}">
                                    <i class="bi {{ $s->contact_type === 'instagram' ? 'bi-instagram' : 'bi-globe' }}"></i>
                                </a>
                            @endforeach
                        @else
                            <a aria-label="Instagram" class="w-9 h-9 rounded bg-white/10 hover:bg-[#8cc5dd] hover:text-[#003580] flex items-center justify-center transition-colors text-white text-base" href="#"><i class="bi bi-instagram"></i></a>
                            <a aria-label="Facebook" class="w-9 h-9 rounded bg-white/10 hover:bg-[#8cc5dd] hover:text-[#003580] flex items-center justify-center transition-colors text-white text-base" href="#"><i class="bi bi-facebook"></i></a>
                            <a aria-label="YouTube" class="w-9 h-9 rounded bg-white/10 hover:bg-[#8cc5dd] hover:text-[#003580] flex items-center justify-center transition-colors text-white text-base" href="#"><i class="bi bi-youtube"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-space-lg pt-space-md border-t border-white/20 text-center font-body-sm text-body-sm text-white/70">
                <p class="m-0">&copy; {{ date('Y') }} UPT PP-ESTEM Institut Teknologi Del. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const toggleBtn = document.getElementById('navbar-toggle-btn');
            const mobileMenu = document.getElementById('mobile-nav-menu');
            if (toggleBtn && mobileMenu) {
                toggleBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
