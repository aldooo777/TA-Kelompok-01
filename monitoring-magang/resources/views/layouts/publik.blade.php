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

        /* Header */
        .pub-header { background: #fff; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 10; }
        .pub-header-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; min-height: 64px; }
        .pub-brand { display: flex; align-items: center; gap: 10px; color: #26364d; }
        .pub-brand img { width: 36px; height: 36px; }
        .pub-brand strong { display: block; font-size: 16px; }
        .pub-brand small { display: block; color: var(--muted); font-size: 12px; }
        .pub-nav { display: flex; align-items: center; gap: 2px; flex-wrap: wrap; }
        .pub-nav a { padding: 8px 12px; border-radius: 4px; color: #4a5a70; font-weight: bold; font-size: 14px; }
        .pub-nav a:hover, .pub-nav a.active { color: var(--accent-dark); background: var(--accent-soft); }
        .pub-nav .p-btn { margin-left: 8px; color: #fff; }
        .pub-nav .p-btn:hover { color: #fff; background: var(--accent-dark); }
        .pub-menu-toggle { display: none; border: 1px solid var(--border); background: #fff; border-radius: 4px; padding: 6px 12px; font-size: 18px; cursor: pointer; }

        /* Judul halaman */
        .pub-page-head { background: var(--accent); color: #fff; padding: 32px 0; }
        .pub-page-head h1 { margin: 0; font-size: 26px; }
        .pub-page-head p { margin: 8px 0 0; opacity: .9; line-height: 1.5; }
        .pub-crumb { font-size: 13px; margin-bottom: 8px; opacity: .85; }
        .pub-crumb a { color: #fff; text-decoration: underline; }

        .pub-main { padding: 28px 0 44px; }
        .pub-section + .pub-section { margin-top: 36px; }
        .pub-section-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; margin-bottom: 14px; }
        .pub-section-head h2 { margin: 0; font-size: 20px; color: #26364d; }

        /* Footer */
        .pub-footer { background: #2f3e52; color: #cfd8e3; font-size: 13px; padding: 24px 0; line-height: 1.7; }
        .pub-footer strong { color: #fff; }
        .pub-footer a { color: #fff; }

        @media (max-width: 820px) {
            .pub-menu-toggle { display: block; }
            .pub-nav {
                display: none;
                position: absolute;
                left: 0;
                right: 0;
                top: 64px;
                background: #fff;
                flex-direction: column;
                align-items: stretch;
                padding: 8px 16px 16px;
                border-bottom: 1px solid var(--border);
            }
            .pub-nav.open { display: flex; }
            .pub-nav .p-btn { margin: 8px 0 0; text-align: center; }
            .pub-page-head { padding: 24px 0; }
            .pub-page-head h1 { font-size: 22px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="pub-header">
        <div class="pub-container pub-header-inner">
            <a href="{{ route('beranda') }}" class="pub-brand">
                <img src="{{ asset('images/logo-monitoring-pdlk.png') }}" alt="Logo PDLK">
                <span>
                    <strong>PDLK IT Del</strong>
                    <small>UPT PP-ESTEM Institut Teknologi Del</small>
                </span>
            </a>

            <button type="button" class="pub-menu-toggle" aria-label="Buka menu"
                    onclick="document.getElementById('pubNav').classList.toggle('open')">☰</button>

            <nav class="pub-nav" id="pubNav">
                <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('lowongan.index') }}" class="{{ request()->routeIs('lowongan.*') ? 'active' : '' }}">Lowongan</a>
                <a href="{{ route('berita.index') }}" class="{{ request()->routeIs('berita.*') ? 'active' : '' }}">Berita</a>
                @if (sprint(2))
                    <a href="{{ route('hasil.index') }}" class="{{ request()->routeIs('hasil.*') ? 'active' : '' }}">Hasil Seleksi</a>
                @endif
                <a href="{{ route('galeri.index') }}" class="{{ request()->routeIs('galeri.*') ? 'active' : '' }}">Galeri</a>
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
            <strong>Pembelajaran di Luar Kampus (PDLK)</strong> · UPT PP-ESTEM Institut Teknologi Del
            @foreach (\App\Support\Sql::all('SELECT * FROM contacts WHERE is_active = true ORDER BY display_order, name LIMIT 6') as $k)
                <br>{{ $k->name }}: {{ $k->contact_value }}
            @endforeach
            <br>&copy; {{ date('Y') }} UPT PP-ESTEM IT Del
        </div>
    </footer>
</body>
</html>
