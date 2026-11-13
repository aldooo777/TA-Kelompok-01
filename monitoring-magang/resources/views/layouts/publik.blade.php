<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PDLK UPT PP-ESTEM') | Institut Teknologi Del</title>
    <link rel="stylesheet" href="{{ asset('css/pdlk-app.css') }}">
    <style>
        :root {
            --accent: #318ab7;
            --accent-dark: #24749b;
            --accent-soft: #e7f3fa;
            --text: #354052;
            --muted: #7b8797;
            --border: #e2e6eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f6f9;
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
        }
        a { color: var(--accent-dark); text-decoration: none; }
        .pub-container { width: 100%; max-width: 1140px; margin: 0 auto; padding: 0 16px; }

        .pub-header { background: #fff; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 10; }
        .pub-header-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; min-height: 68px; }
        .pub-brand { display: flex; align-items: center; gap: 10px; color: #26364d; }
        .pub-logo {
            width: 40px; height: 40px; border-radius: 6px; background: var(--accent); color: #fff;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px;
        }
        .pub-brand strong { display: block; font-size: 16px; letter-spacing: .3px; }
        .pub-brand small { color: var(--muted); font-size: 12px; }
        .pub-nav { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
        .pub-nav a { padding: 8px 12px; border-radius: 6px; color: #4a5a70; font-weight: 700; font-size: 14px; }
        .pub-nav a:hover, .pub-nav a.active { color: var(--accent-dark); background: var(--accent-soft); }
        .pub-nav .p-btn { margin-left: 6px; }
        .pub-menu-toggle { display: none; border: 1px solid var(--border); background: #fff; border-radius: 6px; padding: 8px 12px; font-size: 18px; }

        .pub-hero { background: linear-gradient(135deg, var(--accent-dark), var(--accent)); color: #fff; padding: 56px 0; }
        .pub-hero h1 { margin: 0 0 12px; font-size: 34px; line-height: 1.2; }
        .pub-hero p { margin: 0; max-width: 640px; line-height: 1.6; opacity: .95; }
        .pub-hero .p-btn { margin-top: 22px; }
        .pub-hero .p-btn-light { color: var(--accent-dark) !important; border-color: #fff; }

        .pub-page-head { background: #fff; border-bottom: 1px solid var(--border); padding: 28px 0; }
        .pub-page-head h1 { margin: 0; font-size: 26px; color: #26364d; }
        .pub-page-head p { margin: 8px 0 0; color: var(--muted); }
        .pub-crumb { font-size: 13px; color: var(--muted); margin-bottom: 8px; }

        .pub-main { padding: 32px 0 48px; }
        .pub-section + .pub-section { margin-top: 40px; }
        .pub-section-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; margin-bottom: 16px; }
        .pub-section-head h2 { margin: 0; font-size: 21px; color: #26364d; }

        .pub-footer { background: #26364d; color: #c9d3df; padding: 28px 0; font-size: 13px; }
        .pub-footer strong { color: #fff; }

        @media (max-width: 820px) {
            .pub-menu-toggle { display: block; }
            .pub-nav { display: none; position: absolute; left: 0; right: 0; top: 68px; background: #fff; flex-direction: column; align-items: stretch; padding: 10px 16px 16px; border-bottom: 1px solid var(--border); }
            .pub-nav.open { display: flex; }
            .pub-nav .p-btn { margin: 6px 0 0; }
            .pub-hero { padding: 40px 0; }
            .pub-hero h1 { font-size: 26px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="pub-header">
        <div class="pub-container pub-header-inner">
            <a href="{{ route('beranda') }}" class="pub-brand">
                <span class="pub-logo">M</span>
                <span>
                    <strong>PDLK IT DEL</strong>
                    <small>UPT PP-ESTEM · Institut Teknologi Del</small>
                </span>
            </a>

            <button type="button" class="pub-menu-toggle" aria-label="Buka menu"
                    onclick="document.getElementById('pubNav').classList.toggle('open')">☰</button>

            <nav class="pub-nav" id="pubNav">
                <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('lowongan.index') }}" class="{{ request()->routeIs('lowongan.*') ? 'active' : '' }}">Lowongan</a>
                <a href="{{ route('berita.index') }}" class="{{ request()->routeIs('berita.*') ? 'active' : '' }}">Berita &amp; Pengumuman</a>
                <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'active' : '' }}">Tentang PDLK</a>
                @if (session()->has('cis_user'))
                    <a href="{{ route('dashboard') }}" class="p-btn p-btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="p-btn p-btn-primary">Login</a>
                @endif
            </nav>
        </div>
    </header>

    @yield('hero')

    <main class="pub-main">
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

    <footer class="pub-footer">
        <div class="pub-container">
            <strong>Pembelajaran di Luar Kampus (PDLK)</strong> · UPT PP-ESTEM Institut Teknologi Del<br>
            Jl. Sisingamangaraja, Sitoluama, Laguboti, Toba, Sumatera Utara
        </div>
    </footer>
</body>
</html>
