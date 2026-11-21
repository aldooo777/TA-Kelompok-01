@php
    $cisUser = session('cis_user', []);
    $peranAktif = \App\Support\Peran::aktif();
    $roleLabel = \App\Support\Peran::label();
    $semuaPeran = array_keys(\App\Support\Peran::semua());
    $pdlkUser = \App\Support\Peran::user();
    $username = $cisUser['username'] ?? 'Pengguna';
    $email = $pdlkUser?->email ?? ($cisUser['email'] ?? '');
    $nimUser = $pdlkUser?->nim ?? ($cisUser['nim'] ?? '');

    $displayName = trim((string) ($pdlkUser?->name ?? $cisUser['nama'] ?? $cisUser['name'] ?? $username));
    $nameParts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initial = strtoupper(
        substr($nameParts[0] ?? $username, 0, 1)
        . (count($nameParts) > 1 ? substr($nameParts[count($nameParts) - 1], 0, 1) : '')
    );

    $notifikasiPortal = $pdlkUser
        ? \App\Support\Sql::all('SELECT * FROM notifications WHERE user_id = ? AND is_active = true ORDER BY created_at DESC LIMIT 5', [$pdlkUser->id])
        : collect();
    $unreadNotif = $notifikasiPortal->whereNull('read_at')->count();
@endphp

@if ($peranAktif === 'student')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Mahasiswa | Sistem Informasi PDLK')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pdlk-app.css') }}">
    <style>
        :root {
            --primary: #003580;
            --secondary: #0055A5;
            --navbar-bg: #1a81c2;
            --accent: #8cc5dd;
            --accent-dark: #003580;
            --accent-soft: #e7f3fa;
            --purple-dark: #46166c;
            --purple-mid: #664780;
            --bg-content: #eef2f7;
            --text-main: #444444;
            --text-muted: #6c757d;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-content);
            color: var(--text-main);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        a { text-decoration: none; }
        .top-stripe {
            height: 4px;
            background-color: var(--purple-dark);
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1040;
        }
        .dashboard-navbar {
            background-color: var(--navbar-bg);
            min-height: 68px;
            box-shadow: 0 4px 14px rgba(0, 53, 128, 0.12);
            position: sticky;
            top: 4px;
            z-index: 1030;
            padding: 0.5rem 1rem;
        }
        .brand-container { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand-logo-wrap {
            width: 42px; height: 42px; background: #ffffff; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15); border: 2px solid var(--accent);
            padding: 3px; overflow: hidden; flex-shrink: 0;
        }
        .brand-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
        .brand-title { line-height: 1.15; }
        .brand-title .main-text { font-size: 1.05rem; font-weight: 700; color: #ffffff; letter-spacing: 0.3px; display: block; }
        .brand-title .sub-text { font-size: 0.72rem; font-weight: 500; color: rgba(255, 255, 255, 0.9); }

        .nav-center .nav-link {
            color: #ffffff !important; font-size: 0.88rem; font-weight: 500;
            padding: 8px 14px !important; border-radius: 20px; transition: all 0.2s ease;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .nav-center .nav-link:hover { background-color: rgba(255, 255, 255, 0.12); color: #ffffff !important; }
        .nav-center .nav-link.active {
            background-color: rgba(255, 255, 255, 0.18) !important;
            border-bottom: 3px solid var(--accent);
            border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; font-weight: 600;
        }
        .dropdown-menu { border: none; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 53, 128, 0.14); padding: 8px; }
        .dropdown-item { font-size: 0.85rem; font-weight: 500; padding: 8px 14px; border-radius: 8px; color: var(--text-main); }
        .dropdown-item:hover { background-color: #f0f4fa; color: var(--primary); }

        .icon-bell-btn {
            position: relative; background: rgba(255, 255, 255, 0.15); width: 38px; height: 38px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: #ffffff; border: 0; transition: background 0.2s;
        }
        .icon-bell-btn:hover { background: rgba(255, 255, 255, 0.25); color: #ffffff; }
        .bell-badge {
            position: absolute; top: -2px; right: -2px; background-color: #dc3545; color: #fff;
            font-size: 0.65rem; font-weight: 700; padding: 2px 5px; border-radius: 10px; border: 2px solid var(--navbar-bg);
        }
        .role-badge-pill {
            border: 1px solid rgba(255, 255, 255, 0.6); background: rgba(255, 255, 255, 0.12);
            color: #ffffff; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.5px;
            padding: 6px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
        }
        .profile-pill-btn {
            background: rgba(255, 255, 255, 0.16); border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px; padding: 4px 12px 4px 5px; display: flex; align-items: center;
            gap: 8px; cursor: pointer; color: #ffffff; text-decoration: none; transition: all 0.2s ease;
        }
        .profile-pill-btn:hover { background: rgba(255, 255, 255, 0.24); color: #ffffff; }
        .avatar-circle-sm {
            width: 30px; height: 30px; border-radius: 50%; background: var(--purple-dark);
            color: #ffffff; font-weight: 700; font-size: 0.75rem; display: flex;
            align-items: center; justify-content: center; border: 1.5px solid #ffffff;
        }
        .dashboard-container { padding-top: 24px; padding-bottom: 40px; flex-grow: 1; }
        .dashboard-footer {
            background: #ffffff; border-top: 1px solid #e2e8f0; padding: 18px 0;
            text-align: center; color: var(--text-muted); font-size: 0.8rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="top-stripe"></div>

    <nav class="navbar navbar-expand-lg dashboard-navbar">
        <div class="container-fluid px-lg-4">
            <a class="brand-container me-3" href="{{ route('dashboard') }}">
                <div class="brand-logo-wrap">
                    <img src="{{ asset('images/logo-del.png') }}" alt="Logo IT Del">
                </div>
                <div class="brand-title">
                    <span class="main-text">Sistem Informasi PDLK</span>
                    <span class="sub-text">Institut Teknologi Del</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 text-white shadow-none" type="button"
                    data-bs-toggle="collapse" data-bs-target="#dashboardNavbarContent"
                    aria-controls="dashboardNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="dashboardNavbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 nav-center gap-1 py-2 py-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('portal.lowongan.*', 'mahasiswa.pendaftaran.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-briefcase"></i> Lowongan PDLK
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('portal.lowongan.index') }}"><i class="bi bi-search me-2 text-primary"></i> Cari Lowongan PDLK</a></li>
                            @if (sprint(2))
                                <li><a class="dropdown-item" href="{{ route('mahasiswa.pendaftaran.index') }}"><i class="bi bi-clock-history me-2 text-primary"></i> Progres Pendaftaran</a></li>
                            @endif
                        </ul>
                    </li>

                    @if (sprint(2))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('mahasiswa.progres.*') ? 'active' : '' }}"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-journal-bookmark"></i> Kegiatan PDLK
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}#kegiatan-saya"><i class="bi bi-card-checklist me-2 text-primary"></i> Kegiatan Saya</a></li>
                                <li><a class="dropdown-item" href="{{ route('mahasiswa.progres.edit') }}"><i class="bi bi-pencil-square me-2 text-primary"></i> Perbarui Progres</a></li>
                            </ul>
                        </li>
                    @endif

                    @if (sprint(3))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}" href="{{ route('monitoring.index') }}">
                                <i class="bi bi-clipboard-data"></i> Monev
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0 pb-2 pb-lg-0">
                    <div class="dropdown">
                        <button class="icon-bell-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                            <i class="bi bi-bell-fill"></i>
                            @if ($unreadNotif > 0)
                                <span class="bell-badge">{{ $unreadNotif }}</span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="width: 310px; max-height: 360px; overflow-y: auto;">
                            <li class="px-3 py-2 border-bottom fw-bold" style="font-size: 0.84rem; color: var(--primary);">Notifikasi</li>
                            @forelse ($notifikasiPortal as $notif)
                                <li>
                                    <a class="dropdown-item py-2 border-bottom text-wrap" href="{{ $notif->url ?: '#' }}">
                                        <div class="fw-semibold" style="font-size: 0.8rem; color: var(--primary);">{{ $notif->title }}</div>
                                        @if ($notif->message)
                                            <div class="text-muted" style="font-size: 0.74rem;">{{ $notif->message }}</div>
                                        @endif
                                    </a>
                                </li>
                            @empty
                                <li class="px-3 py-3 text-muted text-center" style="font-size: 0.8rem;">Belum ada notifikasi baru.</li>
                            @endforelse
                        </ul>
                    </div>

                    <span class="role-badge-pill d-none d-sm-inline-flex">
                        <i class="bi bi-person-badge"></i> {{ strtoupper($roleLabel) }}
                    </span>

                    <div class="dropdown">
                        <a class="profile-pill-btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle-sm">{{ $initial }}</div>
                            <span class="d-none d-md-inline fw-medium" style="font-size: 0.82rem;">{{ $displayName }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 240px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold" style="font-size: 0.84rem; color: var(--primary);">{{ $displayName }}</div>
                                @if ($nimUser)
                                    <div class="text-muted" style="font-size: 0.72rem;">NIM: {{ $nimUser }}</div>
                                @endif
                                @if ($email)
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $email }}</div>
                                @endif
                            </li>
                            @if (count($semuaPeran) > 1)
                                <li class="px-3 pt-1 pb-1 text-muted" style="font-size: 0.72rem;">Ganti peran:</li>
                                @foreach ($semuaPeran as $kodePeran)
                                    @continue($kodePeran === $peranAktif)
                                    <li>
                                        <form method="POST" action="{{ route('peran.ganti', $kodePeran) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-arrow-left-right me-2 text-primary"></i> {{ \App\Support\Peran::label($kodePeran) }}
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider my-1"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('beranda') }}"><i class="bi bi-globe me-2 text-primary"></i> Lihat Situs Informasi</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="dashboard-container">
        <div class="container-fluid px-lg-4">
            @if (! request()->routeIs('dashboard'))
                <div class="mb-3">
                    @include('partials.flash')
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="dashboard-footer">
        <div class="container-fluid px-lg-4">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2">
                <span>&copy; {{ date('Y') }} UPT PP-ESTEM Institut Teknologi Del</span>
                <span class="d-none d-sm-inline">·</span>
                <span>Sistem Informasi Pembelajaran di Luar Kampus</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

@elseif ($peranAktif === 'upt_staff')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staf UPT | Sistem Informasi PDLK')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,300..900;1,300..900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-surface-variant": "#434651", "surface": "#fbf9f8", "surface-container-high": "#eae8e7",
                        "primary-fixed": "#d9e2ff", "on-tertiary-fixed": "#2d004f", "secondary-fixed-dim": "#a8c8ff",
                        "on-surface": "#1b1c1c", "surface-tint": "#375ca8", "primary": "#002155",
                        "outline-variant": "#c4c6d3", "surface-container-low": "#f5f3f3", "on-tertiary-fixed-variant": "#5e3085",
                        "surface-variant": "#e4e2e2", "inverse-primary": "#b0c6ff", "surface-container-lowest": "#ffffff",
                        "on-secondary-container": "#003d79", "on-error": "#ffffff", "outline": "#747782",
                        "on-background": "#1b1c1c", "on-tertiary": "#ffffff", "tertiary-fixed-dim": "#e0b7ff",
                        "inverse-on-surface": "#f2f0f0", "tertiary-container": "#502176", "secondary-fixed": "#d6e3ff",
                        "error": "#ba1a1a", "on-error-container": "#93000a", "surface-bright": "#fbf9f8",
                        "surface-container-highest": "#e4e2e2", "secondary": "#175eae", "surface-dim": "#dbd9d9",
                        "tertiary-fixed": "#f2daff", "secondary-container": "#72a9ff", "on-secondary": "#ffffff",
                        "on-primary": "#ffffff", "error-container": "#ffdad6", "on-tertiary-container": "#c08ee9",
                        "on-primary-container": "#7fa1f2", "inverse-surface": "#303030", "primary-fixed-dim": "#b0c6ff",
                        "surface-container": "#f0eded", "on-primary-fixed": "#001945", "on-primary-fixed-variant": "#1a438e",
                        "on-secondary-fixed": "#001b3c", "on-secondary-fixed-variant": "#00468a", "primary-container": "#003580",
                        "background": "#fbf9f8", "tertiary": "#39025f"
                    },
                    spacing: { "margin": "2rem", "space-xl": "2.5rem", "space-xs": "0.25rem", "space-sm": "0.5rem", "space-md": "1rem", "gutter": "1.5rem", "space-lg": "1.5rem", "gutter-mobile": "1rem", "margin-mobile": "1rem" },
                    fontFamily: { "sans": ["Poppins", "Hanken Grotesk", "sans-serif"] },
                    fontSize: {
                        "body-md": ["14px", { "lineHeight": "22px", "fontWeight": "400" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "fontWeight": "700" }],
                        "label-md": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
                        "headline-lg": ["28px", { "lineHeight": "36px", "fontWeight": "600" }],
                        "label-sm": ["11px", { "lineHeight": "14px", "fontWeight": "500" }],
                        "display-lg": ["48px", { "lineHeight": "56px", "fontWeight": "700" }],
                        "headline-sm": ["18px", { "lineHeight": "26px", "fontWeight": "600" }],
                        "body-lg": ["16px", { "lineHeight": "26px", "fontWeight": "400" }],
                        "body-sm": ["12px", { "lineHeight": "18px", "fontWeight": "400" }],
                        "label-lg": ["14px", { "lineHeight": "20px", "fontWeight": "600" }],
                        "headline-md": ["22px", { "lineHeight": "30px", "fontWeight": "600" }]
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/pdlk-app.css') }}">
    <style>
        :root { --accent: #1a81c2; --accent-dark: #003580; --accent-soft: #e7f3fa; }
        body { font-family: 'Poppins', 'Hanken Grotesk', sans-serif; background-color: #eef2f7; margin: 0; }
        .top-stripe { height: 4px; background-color: #46166c; width: 100%; position: fixed; top: 0; left: 0; z-index: 60; }
        .header-custom { background-color: #1a81c2; height: 68px; top: 4px; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#eef2f7] text-on-surface antialiased min-h-screen flex flex-col">
    <div class="top-stripe"></div>

    <header class="header-custom fixed w-full z-50 shadow-[0_2px_4px_rgba(0,53,128,0.12)] text-white">
        @php $masterSemua = \App\Support\MasterData::all(); @endphp
        <div class="max-w-[1440px] mx-auto h-full px-3 lg:px-5 flex items-center justify-between gap-2">
            {{-- Brand Kiri --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 shrink-0 no-underline text-white">
                <div class="w-9 h-9 rounded-lg bg-white p-1 flex items-center justify-center border border-[#8cc5dd] shadow-sm overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo-del.png') }}" alt="Logo IT Del" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-white text-[14px] tracking-tight leading-none whitespace-nowrap">Sistem Informasi PDLK</span>
                    <span class="text-[10px] text-white/85 font-medium tracking-wide leading-tight mt-0.5 whitespace-nowrap">UPT PP-ESTEM · IT Del</span>
                </div>
            </a>

            {{-- Menu Tengah Ringkas --}}
            <nav class="hidden xl:flex items-center gap-0.5 h-full">
                <a href="{{ route('dashboard') }}"
                   class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors no-underline {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                    <i class="bi bi-speedometer2 text-[13px]"></i><span>Dashboard</span>
                </a>

                <div class="relative group h-full flex items-center">
                    <button type="button"
                            class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors {{ request()->routeIs('kelola.master.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <span>Data Master</span><i class="bi bi-chevron-down text-[9px] opacity-75"></i>
                    </button>
                    <div class="absolute left-0 top-full w-52 bg-white text-on-surface rounded-xl shadow-lg border border-outline-variant/30 py-2 hidden group-hover:block z-50">
                        @foreach ($masterSemua as $kunci => $def)
                            <a class="block px-4 py-2 text-xs hover:bg-surface-container hover:text-primary no-underline {{ request()->route('master') === $kunci ? 'font-bold text-primary bg-surface-container-low' : 'text-on-surface-variant' }}"
                               href="{{ route('kelola.master.index', $kunci) }}">{{ $def['title'] }}</a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('kelola.pengguna.index') }}"
                   class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors no-underline {{ request()->routeIs('kelola.pengguna.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                    <i class="bi bi-people text-[13px]"></i><span>Pengguna</span>
                </a>

                <div class="relative group h-full flex items-center">
                    <button type="button"
                            class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors {{ request()->routeIs('kelola.postingan.*', 'kelola.hasil.*', 'kelola.galeri.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <span>Konten</span><i class="bi bi-chevron-down text-[9px] opacity-75"></i>
                    </button>
                    <div class="absolute left-0 top-full w-52 bg-white text-on-surface rounded-xl shadow-lg border border-outline-variant/30 py-2 hidden group-hover:block z-50">
                        <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.postingan.index') }}">Berita</a>
                        @if (sprint(2))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.hasil.index') }}">Pengumuman Hasil</a>
                        @endif
                        <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.galeri.index') }}">Galeri Kegiatan</a>
                        @if (isset($masterSemua['struktur-organisasi']) || isset($masterSemua['kontak']))
                            <div class="border-t border-surface-variant my-1"></div>
                        @endif
                        @if (isset($masterSemua['struktur-organisasi']))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.master.index', 'struktur-organisasi') }}">Struktur Organisasi</a>
                        @endif
                        @if (isset($masterSemua['kontak']))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.master.index', 'kontak') }}">Kontak</a>
                        @endif
                    </div>
                </div>

                <div class="relative group h-full flex items-center">
                    <button type="button"
                            class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors {{ request()->routeIs('kelola.lowongan.*', 'kelola.pendaftaran.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <span>Lowongan PDLK</span><i class="bi bi-chevron-down text-[9px] opacity-75"></i>
                    </button>
                    <div class="absolute left-0 top-full w-52 bg-white text-on-surface rounded-xl shadow-lg border border-outline-variant/30 py-2 hidden group-hover:block z-50">
                        <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.lowongan.index') }}">Kelola Lowongan PDLK</a>
                        @if (sprint(2))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.pendaftaran.index') }}">Kelola Pelamar</a>
                        @endif
                    </div>
                </div>

                @if (sprint(2))
                    <a href="{{ route('peserta.index') }}"
                       class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors no-underline {{ request()->routeIs('peserta.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                        <i class="bi bi-person-workspace text-[13px]"></i><span>Peserta</span>
                    </a>
                @endif

                @if (sprint(3))
                    <div class="relative group h-full flex items-center">
                        <button type="button"
                                class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 transition-colors {{ request()->routeIs('kelola.kuesioner.*', 'kelola.jadwal-monev.*', 'rekap-monev.*') ? 'bg-white/20 text-white border-b-[3px] border-[#8cc5dd] font-semibold rounded-t-lg' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-lg' }}">
                            <span>Monev</span><i class="bi bi-chevron-down text-[9px] opacity-75"></i>
                        </button>
                        <div class="absolute left-0 top-full w-48 bg-white text-on-surface rounded-xl shadow-lg border border-outline-variant/30 py-2 hidden group-hover:block z-50">
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.kuesioner.index') }}">Kuesioner</a>
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.jadwal-monev.index') }}">Jadwal Monev</a>
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('rekap-monev.index') }}">Rekap Hasil Monev</a>
                        </div>
                    </div>
                @endif

                <div class="relative group h-full flex items-center">
                    <button type="button"
                            class="text-[12.5px] whitespace-nowrap flex items-center gap-1 px-2 py-1.5 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <span>Laporan</span><i class="bi bi-chevron-down text-[9px] opacity-75"></i>
                    </button>
                    <div class="absolute right-0 top-full w-52 bg-white text-on-surface rounded-xl shadow-lg border border-outline-variant/30 py-2 hidden group-hover:block z-50">
                        <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('rekapitulasi') }}">Rekapitulasi Peserta</a>
                        @if (sprint(2))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('kelola.pendaftaran.ekspor') }}">Ekspor Data Pendaftar</a>
                        @endif
                        @if (sprint(3))
                            <a class="block px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('rekap-monev.index') }}">Rekapitulasi Monev</a>
                        @endif
                    </div>
                </div>
            </nav>

            {{-- Aksi Kanan (Klik untuk buka/tutup) --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="relative" data-portal-dropdown-wrap>
                    <button aria-label="Notifikasi" onclick="togglePortalMenu('staffNotifMenu', event)"
                            class="relative p-1.5 rounded-lg hover:bg-white/10 transition-colors text-white focus:outline-none" type="button">
                        <i class="bi bi-bell text-lg"></i>
                        @if ($unreadNotif > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-error text-[10px] font-bold text-white">{{ $unreadNotif }}</span>
                        @endif
                    </button>
                    <div id="staffNotifMenu" data-portal-dropdown class="hidden absolute right-0 top-full mt-2 w-72 bg-white text-on-surface rounded-xl shadow-xl border border-outline-variant/20 py-2 z-50">
                        <div class="px-4 py-2 border-b border-surface-variant text-xs font-bold text-primary">Notifikasi</div>
                        @forelse ($notifikasiPortal as $notif)
                            <a href="{{ $notif->url ?: '#' }}" class="block px-4 py-2.5 border-b border-surface-container hover:bg-surface-container-low no-underline">
                                <div class="text-xs font-semibold text-primary">{{ $notif->title }}</div>
                                @if ($notif->message)
                                    <div class="text-[11px] text-on-surface-variant mt-0.5">{{ $notif->message }}</div>
                                @endif
                            </a>
                        @empty
                            <div class="px-4 py-4 text-xs text-on-surface-variant text-center">Belum ada notifikasi baru.</div>
                        @endforelse
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-1 px-2 py-0.5 rounded-full border border-white/60 text-white text-[10px] font-semibold tracking-wider uppercase whitespace-nowrap">
                    <i class="bi bi-shield-check text-[12px]"></i><span>STAF UPT</span>
                </div>

                <div class="relative" data-portal-dropdown-wrap>
                    <button onclick="togglePortalMenu('staffProfileMenu', event)"
                            class="flex items-center gap-1.5 pl-1 pr-2 py-1 rounded-full bg-white/15 hover:bg-white/25 border border-white/25 transition-colors text-left focus:outline-none" type="button">
                        <div class="w-7 h-7 rounded-full bg-[#46166c] text-white flex items-center justify-center text-xs font-bold border border-white/40 shrink-0">
                            {{ $initial }}
                        </div>
                        <span class="hidden sm:block text-xs font-semibold text-white leading-tight truncate max-w-[95px]">{{ $displayName }}</span>
                        <i class="bi bi-caret-down-fill text-[9px] text-white/85 shrink-0"></i>
                    </button>
                    <div id="staffProfileMenu" data-portal-dropdown class="hidden absolute right-0 top-full mt-2 w-56 bg-white text-on-surface rounded-xl shadow-xl border border-outline-variant/20 py-2 z-50">
                        <div class="px-4 py-2 border-b border-surface-variant">
                            <p class="text-xs font-bold text-primary truncate m-0">{{ $displayName }}</p>
                            @if ($email)
                                <p class="text-[11px] text-on-surface-variant truncate m-0">{{ $email }}</p>
                            @endif
                        </div>

                        @if (count($semuaPeran) > 1)
                            <div class="px-4 pt-2 pb-1 text-[10px] uppercase tracking-wider text-outline font-bold">Ganti Peran</div>
                            @foreach ($semuaPeran as $kodePeran)
                                @continue($kodePeran === $peranAktif)
                                <form method="POST" action="{{ route('peran.ganti', $kodePeran) }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-1.5 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary">
                                        <i class="bi bi-arrow-left-right text-sm"></i> {{ \App\Support\Peran::label($kodePeran) }}
                                    </button>
                                </form>
                            @endforeach
                            <div class="border-t border-surface-variant my-1"></div>
                        @endif

                        <a class="flex items-center gap-2.5 px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('beranda') }}">
                            <i class="bi bi-box-arrow-up-right text-sm"></i> Lihat Situs Informasi
                        </a>
                        <div class="border-t border-surface-variant my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-error hover:bg-error-container hover:text-on-error-container">
                                <i class="bi bi-box-arrow-right text-sm"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>

                <button type="button" onclick="document.getElementById('staffMobileMenu').classList.toggle('hidden')"
                        class="xl:hidden p-1.5 rounded-lg hover:bg-white/10 text-white" aria-label="Buka menu">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>

        {{-- Menu Mobile Staf UPT --}}
        <div id="staffMobileMenu" class="hidden xl:hidden bg-[#1a81c2] border-t border-white/15 px-4 py-3 space-y-1 text-xs shadow-lg max-h-[75vh] overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded font-semibold text-white hover:bg-white/15 no-underline">Dashboard</a>
            <div class="px-3 pt-2 pb-1 text-white/70 font-semibold uppercase text-[10px]">Data Master</div>
            @foreach ($masterSemua as $kunci => $def)
                <a href="{{ route('kelola.master.index', $kunci) }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">{{ $def['title'] }}</a>
            @endforeach
            <a href="{{ route('kelola.pengguna.index') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Pengguna &amp; Role</a>
            <div class="px-3 pt-2 pb-1 text-white/70 font-semibold uppercase text-[10px]">Konten &amp; Lowongan PDLK</div>
            <a href="{{ route('kelola.postingan.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Berita</a>
            @if (sprint(2))
                <a href="{{ route('kelola.hasil.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Pengumuman Hasil</a>
            @endif
            <a href="{{ route('kelola.galeri.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Galeri Kegiatan</a>
            @if (isset($masterSemua['struktur-organisasi']))
                <a href="{{ route('kelola.master.index', 'struktur-organisasi') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Struktur Organisasi</a>
            @endif
            @if (isset($masterSemua['kontak']))
                <a href="{{ route('kelola.master.index', 'kontak') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Kontak</a>
            @endif
            <a href="{{ route('kelola.lowongan.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Kelola Lowongan PDLK</a>
            @if (sprint(2))
                <a href="{{ route('kelola.pendaftaran.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Kelola Pelamar</a>
                <a href="{{ route('peserta.index') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Peserta PDLK</a>
            @endif
            @if (sprint(3))
                <div class="px-3 pt-2 pb-1 text-white/70 font-semibold uppercase text-[10px]">Monev</div>
                <a href="{{ route('kelola.kuesioner.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Kuesioner</a>
                <a href="{{ route('kelola.jadwal-monev.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Jadwal Monev</a>
                <a href="{{ route('rekap-monev.index') }}" class="block pl-6 pr-3 py-1.5 text-white hover:bg-white/15 rounded no-underline">Rekap Hasil Monev</a>
            @endif
        </div>
    </header>

    <main class="w-full pt-[92px] bg-[#eef2f7] flex-1 pb-10 max-w-[1440px] mx-auto px-4 md:px-6 lg:px-8">
        @if (! request()->routeIs('dashboard'))
            <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <div class="text-xs text-on-surface-variant mb-1">@yield('breadcrumb', 'Dashboard')</div>
                    <h1 class="text-xl font-bold text-primary m-0">@yield('page-title', 'Pengelolaan PDLK')</h1>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="w-full bg-surface-container-lowest border-t border-outline-variant/30 py-4 mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 text-center text-on-surface-variant text-xs">
            &copy; {{ date('Y') }} UPT PP-ESTEM Institut Teknologi Del · Sistem Informasi PDLK
        </div>
    </footer>

    @stack('scripts')
    <script>
        function togglePortalMenu(id, event) {
            event.stopPropagation();
            const target = document.getElementById(id);
            if (!target) return;
            const willOpen = target.classList.contains('hidden');
            document.querySelectorAll('[data-portal-dropdown]').forEach(el => el.classList.add('hidden'));
            if (willOpen) target.classList.remove('hidden');
        }
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-portal-dropdown-wrap]')) {
                document.querySelectorAll('[data-portal-dropdown]').forEach(el => el.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>

@else
@php
    $cakupanProdiIds = \App\Support\Peran::cakupanProdi();
    $labelProdiNav = $cakupanProdiIds !== null && $cakupanProdiIds !== []
        ? \App\Support\Sql::all("SELECT level || ' ' || name AS label FROM study_programs WHERE id IN (" . implode(',', array_map('intval', $cakupanProdiIds)) . ') ORDER BY level, name')
            ->pluck('label')
            ->implode(', ')
        : 'Semua Prodi';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Koordinator Prodi | Sistem Informasi PDLK')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-primary-fixed-variant": "#1a438e", "error": "#ba1a1a", "surface-container-lowest": "#ffffff",
                        "secondary": "#1a81c2", "tertiary-fixed-dim": "#e0b7ff", "on-background": "#1b1c1c",
                        "on-surface-variant": "#434651", "tertiary-fixed": "#f2daff", "surface-container-high": "#eae8e7",
                        "on-tertiary-fixed": "#2d004f", "on-error": "#ffffff", "surface-container-highest": "#e4e2e2",
                        "on-primary-fixed": "#001945", "background": "#fbf9f8", "surface-bright": "#fbf9f8",
                        "primary": "#002155", "surface-dim": "#dbd9d9", "on-error-container": "#93000a",
                        "on-secondary-container": "#003d79", "tertiary": "#46166c", "on-primary-container": "#7fa1f2",
                        "on-primary": "#ffffff", "surface-container": "#f0eded", "on-tertiary-fixed-variant": "#5e3085",
                        "on-tertiary-container": "#c08ee9", "on-secondary-fixed": "#001b3c", "secondary-container": "#72a9ff",
                        "on-secondary-fixed-variant": "#00468a", "surface-variant": "#e4e2e2", "tertiary-container": "#502176",
                        "surface": "#fbf9f8", "primary-fixed-dim": "#b0c6ff", "surface-container-low": "#f5f3f3",
                        "secondary-fixed-dim": "#a8c8ff", "inverse-primary": "#b0c6ff", "outline": "#747782",
                        "primary-fixed": "#d9e2ff", "inverse-on-surface": "#f2f0f0", "error-container": "#ffdad6",
                        "primary-container": "#003580", "outline-variant": "#c4c6d3", "on-surface": "#1b1c1c",
                        "on-secondary": "#ffffff", "on-tertiary": "#ffffff", "surface-tint": "#375ca8",
                        "secondary-fixed": "#d6e3ff", "inverse-surface": "#303030"
                    },
                    fontFamily: { "sans": ["Hanken Grotesk", "sans-serif"] },
                    fontSize: {
                        "headline-sm": ["18px", { "lineHeight": "26px", "fontWeight": "600" }],
                        "label-md": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
                        "label-sm": ["11px", { "lineHeight": "14px", "fontWeight": "500" }],
                        "body-md": ["14px", { "lineHeight": "22px", "fontWeight": "400" }],
                        "body-sm": ["12px", { "lineHeight": "18px", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/pdlk-app.css') }}">
    <style>
        :root { --accent: #1a81c2; --accent-dark: #003580; --accent-soft: #e7f3fa; }
        body { font-family: 'Hanken Grotesk', sans-serif; background-color: #eef2f7; margin: 0; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#eef2f7] font-body-md text-on-surface antialiased min-h-screen flex flex-col">
    <header class="sticky top-0 z-50 w-full shadow-[0_2px_4px_rgba(0,53,128,0.12)]">
        <div class="h-1 bg-[#46166c] w-full"></div>
        <div class="h-16 bg-[#1a81c2] text-white px-4 lg:px-8">
            <div class="max-w-7xl mx-auto h-full flex items-center justify-between gap-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 shrink-0 no-underline text-white">
                    <div class="w-10 h-10 rounded-lg bg-white p-1 flex items-center justify-center shadow-sm border border-[#8cc5dd] overflow-hidden">
                        <img src="{{ asset('images/logo-del.png') }}" alt="Logo IT Del" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-[15px] text-white leading-none tracking-tight">Sistem Informasi PDLK</span>
                        <span class="text-[10px] text-white/85 uppercase tracking-wider font-semibold mt-0.5">UPT PP-ESTEM · IT Del</span>
                    </div>
                </a>

                <nav class="hidden xl:flex items-center gap-1 h-full">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-lg text-[13px] transition-colors flex items-center gap-1.5 no-underline {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white font-semibold border-b-2 border-[#8cc5dd]' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                        Dashboard Prodi
                    </a>

                    @if (sprint(2))
                        <div class="relative group h-full flex items-center">
                            <a href="{{ route('peserta.index') }}"
                               class="px-3 py-2 rounded-lg text-[13px] transition-colors flex items-center gap-1 no-underline {{ request()->routeIs('peserta.*') ? 'bg-white/20 text-white font-semibold border-b-2 border-[#8cc5dd]' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                                <span>Mahasiswa PDLK</span><span class="material-symbols-outlined text-[16px]">expand_more</span>
                            </a>
                            <div class="absolute top-full left-0 hidden group-hover:flex flex-col bg-white text-on-surface shadow-[0_8px_24px_rgba(0,53,128,0.16)] rounded-lg py-2 min-w-[210px] border-t-2 border-[#46166c] z-50">
                                <a class="px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary no-underline" href="{{ route('peserta.index') }}">Daftar Mahasiswa Prodi</a>
                                @if ($peranAktif === 'pdlk_coordinator')
                                    <a class="px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container-high hover:text-primary no-underline" href="{{ route('peserta.create') }}">Tambah Peserta Prodi</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if (sprint(3))
                        <a href="{{ route('rekap-monev.index') }}"
                           class="px-3 py-2 rounded-lg text-[13px] transition-colors flex items-center gap-1.5 no-underline {{ request()->routeIs('rekap-monev.*') ? 'bg-white/20 text-white font-semibold border-b-2 border-[#8cc5dd]' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                            Rekap Monev Prodi
                        </a>
                    @endif

                    <a href="{{ route('rekapitulasi') }}"
                       class="px-3 py-2 rounded-lg text-[13px] transition-colors flex items-center gap-1.5 no-underline text-white/90 hover:text-white hover:bg-white/10">
                        Rekapitulasi Data
                    </a>
                </nav>

                <div class="flex items-center gap-2.5 shrink-0">
                    {{-- Notifikasi --}}
                    <div class="relative" data-portal-dropdown-wrap>
                        <button aria-label="Notifikasi" onclick="togglePortalMenu('koorNotifMenu', event)"
                                class="relative p-2 rounded-lg text-white hover:bg-white/10 transition-colors" type="button">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            @if ($unreadNotif > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error ring-2 ring-[#1a81c2]"></span>
                            @endif
                        </button>
                        <div id="koorNotifMenu" data-portal-dropdown class="hidden absolute right-0 top-full mt-2 w-72 bg-white text-on-surface rounded-xl shadow-xl border border-outline-variant/20 py-2 z-50">
                            <div class="px-4 py-2 border-b border-surface-variant text-xs font-bold text-primary">Notifikasi</div>
                            @forelse ($notifikasiPortal as $notif)
                                <a href="{{ $notif->url ?: '#' }}" class="block px-4 py-2.5 border-b border-surface-container hover:bg-surface-container-low no-underline">
                                    <div class="text-xs font-semibold text-primary">{{ $notif->title }}</div>
                                    @if ($notif->message)
                                        <div class="text-[11px] text-on-surface-variant mt-0.5">{{ $notif->message }}</div>
                                    @endif
                                </a>
                            @empty
                                <div class="px-4 py-4 text-xs text-on-surface-variant text-center">Belum ada notifikasi baru.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-1.5">
                        <span class="bg-white/15 border border-white/40 text-white px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide whitespace-nowrap">
                            {{ $peranAktif === 'pdlk_coordinator' ? 'KOORDINATOR PDLK' : 'KAPRODI' }}
                        </span>
                        <span class="bg-[#46166c] text-white px-2.5 py-0.5 rounded-full text-[10px] font-semibold tracking-wide whitespace-nowrap max-w-[140px] truncate" title="{{ $labelProdiNav }}">
                            {{ $labelProdiNav }}
                        </span>
                    </div>

                    <div class="relative pl-2 border-l border-white/20" data-portal-dropdown-wrap>
                        <button type="button" onclick="togglePortalMenu('koorProfileMenu', event)" class="flex items-center gap-2 text-left focus:outline-none">
                            <div class="text-right hidden lg:block leading-tight">
                                <p class="text-xs text-white font-semibold m-0 truncate max-w-[130px]">{{ $displayName }}</p>
                                <p class="text-[10px] text-[#d6e3ff] m-0">{{ $roleLabel }}</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-[#003580] text-white flex items-center justify-center text-xs font-bold border border-white/40 shrink-0">
                                {{ $initial }}
                            </div>
                        </button>
                        <div id="koorProfileMenu" data-portal-dropdown class="hidden absolute right-0 top-full mt-2 w-56 bg-white text-on-surface rounded-xl shadow-xl border border-outline-variant/20 py-2 z-50">
                            <div class="px-4 py-2 border-b border-surface-variant">
                                <p class="text-xs font-bold text-primary truncate m-0">{{ $displayName }}</p>
                                <p class="text-[11px] text-on-surface-variant truncate m-0">{{ $labelProdiNav }}</p>
                            </div>

                            @if (count($semuaPeran) > 1)
                                <div class="px-4 pt-2 pb-1 text-[10px] uppercase tracking-wider text-outline font-bold">Ganti Peran</div>
                                @foreach ($semuaPeran as $kodePeran)
                                    @continue($kodePeran === $peranAktif)
                                    <form method="POST" action="{{ route('peran.ganti', $kodePeran) }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-1.5 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary">
                                            <i class="bi bi-arrow-left-right text-sm"></i> {{ \App\Support\Peran::label($kodePeran) }}
                                        </button>
                                    </form>
                                @endforeach
                                <div class="border-t border-surface-variant my-1"></div>
                            @endif

                            <a class="flex items-center gap-2.5 px-4 py-2 text-xs text-on-surface-variant hover:bg-surface-container hover:text-primary no-underline" href="{{ route('beranda') }}">
                                <i class="bi bi-box-arrow-up-right text-sm"></i> Lihat Situs Informasi
                            </a>
                            <div class="border-t border-surface-variant my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs text-error hover:bg-error-container hover:text-on-error-container">
                                    <i class="bi bi-box-arrow-right text-sm"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <button type="button" onclick="document.getElementById('koorMobileMenu').classList.toggle('hidden')"
                            class="xl:hidden p-1.5 rounded-lg hover:bg-white/10 text-white" aria-label="Buka menu">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="koorMobileMenu" class="hidden xl:hidden bg-[#1a81c2] border-t border-white/15 px-4 py-3 space-y-1 text-xs shadow-lg">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded font-semibold text-white hover:bg-white/15 no-underline">Dashboard Prodi</a>
            @if (sprint(2))
                <a href="{{ route('peserta.index') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Daftar Mahasiswa Prodi</a>
                @if ($peranAktif === 'pdlk_coordinator')
                    <a href="{{ route('peserta.create') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Tambah Peserta Prodi</a>
                @endif
            @endif
            @if (sprint(3))
                <a href="{{ route('rekap-monev.index') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Rekap Monev Prodi</a>
            @endif
            <a href="{{ route('rekapitulasi') }}" class="block px-3 py-2 rounded text-white hover:bg-white/15 no-underline">Rekapitulasi Data</a>
        </div>
    </header>

    <main class="w-full bg-[#eef2f7] flex-1 p-4 lg:p-6">
        <div class="max-w-[1400px] mx-auto">
            @if (! request()->routeIs('dashboard'))
                <div class="mb-4">
                    <div class="text-xs text-on-surface-variant mb-1">@yield('breadcrumb', 'Dashboard')</div>
                    <h1 class="text-xl font-bold text-[#003580] m-0">@yield('page-title', 'Pemantauan Prodi')</h1>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="w-full bg-surface-container-lowest border-t border-outline-variant/30 py-6">
        <div class="max-w-7xl mx-auto px-4 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <p class="font-body-sm text-body-sm text-on-surface-variant m-0">&copy; {{ date('Y') }} UPT PP-ESTEM Institut Teknologi Del · Sistem Informasi PDLK</p>
            <div class="flex items-center gap-4 text-on-surface-variant font-label-sm text-label-sm">
                <a class="hover:text-primary transition-colors no-underline" href="{{ route('beranda') }}">Situs Publik PDLK</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
    <script>
        function togglePortalMenu(id, event) {
            event.stopPropagation();
            const target = document.getElementById(id);
            if (!target) return;
            const willOpen = target.classList.contains('hidden');
            document.querySelectorAll('[data-portal-dropdown]').forEach(el => el.classList.add('hidden'));
            if (willOpen) target.classList.remove('hidden');
        }
        document.addEventListener('click', function (e) {
            if (!e.target.closest('[data-portal-dropdown-wrap]')) {
                document.querySelectorAll('[data-portal-dropdown]').forEach(el => el.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>
@endif
