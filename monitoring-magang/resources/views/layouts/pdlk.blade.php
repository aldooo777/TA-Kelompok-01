
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Monitoring PDLK')</title>

    <style>
        :root {
            --accent: #318ab7;
            --accent-dark: #24749b;
            --accent-soft: #e7f3fa;
            --page-bg: #f1f2f4;
            --text: #354052;
            --muted: #7b8797;
            --border: #e2e6eb;
            --sidebar-width: 245px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                linear-gradient(135deg, #f7f7f7, #e9ecef);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: var(--text);
        }

        button,
        input {
            font: inherit;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        a {
            color: var(--accent-dark);
            text-decoration: none;
        }

        button {
            cursor: pointer;
        }

        [hidden] {
            display: none !important;
        }

        /* =====================================================
           KERANGKA APLIKASI
        ===================================================== */

        .app-shell {
            width: 100%;
            max-width: 1220px;
            min-height: 100vh;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 22px rgba(0, 0, 0, .09);
        }

        /* =====================================================
           HEADER ATAS
        ===================================================== */

        .topbar {
            position: relative;
            z-index: 20;
            display: flex;
            align-items: stretch;
            height: 56px;
            color: white;
            background: var(--accent);
        }

        .brand-area {
            display: flex;
            flex: 0 0 var(--sidebar-width);
            align-items: center;
            gap: 9px;
            min-width: 0;
            padding: 0 15px;
            color: white;
            background-color: var(--accent-dark);
            background-image:
                repeating-linear-gradient(
                    0deg,
                    rgba(255,255,255,.045) 0,
                    rgba(255,255,255,.045) 1px,
                    transparent 1px,
                    transparent 4px
                ),
                repeating-linear-gradient(
                    90deg,
                    rgba(255,255,255,.045) 0,
                    rgba(255,255,255,.045) 1px,
                    transparent 1px,
                    transparent 4px
                );
        }

        .brand-mark {
            display: flex;
            flex: 0 0 42px;
            align-items: center;
            justify-content: center;
            height: 38px;
            border: 1px solid rgba(255,255,255,.6);
            border-radius: 3px;
            background: rgba(255,255,255,.08);
            font-size: 23px;
            font-weight: 800;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: .2px;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #e0f0f8;
            font-size: 10px;
        }

        .topbar-main {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
            padding: 0 12px;
        }

        .icon-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            padding: 0;
            border: 0;
            border-radius: 4px;
            color: white;
            background: transparent;
            font-size: 21px;
        }

        .icon-button:hover,
        .icon-button[aria-expanded="true"] {
            background: rgba(255,255,255,.13);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 7px;
            height: 7px;
            border: 1px solid var(--accent);
            border-radius: 50%;
            background: #ffd166;
        }

        .profile-toggle {
            display: flex;
            align-items: center;
            gap: 9px;
            max-width: 220px;
            height: 48px;
            padding: 0 10px;
            border: 0;
            color: white;
            background: transparent;
        }

        .profile-toggle:hover,
        .profile-toggle[aria-expanded="true"] {
            background: rgba(255,255,255,.12);
        }

        .avatar {
            display: flex;
            flex: 0 0 30px;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: var(--accent-dark);
            background: #e9f2ff;
            font-size: 12px;
            font-weight: 800;
        }


.profile-meta {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 3px;
    min-width: 0;
    text-align: left;
}

.profile-name {
    display: block;
    max-width: 150px;
    overflow: hidden;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-role-label {
    color: #e0eef7;
    font-size: 10px;
    line-height: 1.3;
}


        .profile-caret {
            font-size: 10px;
        }

        /* =====================================================
           DROPDOWN HEADER
        ===================================================== */

        .dropdown-wrap {
            position: relative;
        }

        .dropdown-panel {
            position: absolute;
            top: calc(100% + 1px);
            right: 0;
            z-index: 100;
            width: 290px;
            overflow: hidden;
            border: 1px solid #dfe5eb;
            border-radius: 2px;
            color: var(--text);
            background: white;
            box-shadow: 0 7px 22px rgba(0,0,0,.16);
        }

        .dropdown-heading {
            padding: 14px 16px;
            border-bottom: 1px solid #edf0f3;
            font-weight: 700;
        }

        .notification-content {
            padding: 20px 16px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .profile-panel-header {
            padding: 22px 15px;
            text-align: center;
            color: white;
            background: var(--accent);
        }

        .profile-big-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            margin: 0 auto 10px;
            border: 2px solid rgba(255,255,255,.5);
            border-radius: 50%;
            color: var(--accent-dark);
            background: #eff5ff;
            font-size: 23px;
            font-weight: 800;
        }

        .profile-full-name {
            font-weight: 700;
        }


.profile-username {
    margin-top: 5px;
    color: #e0eef7;
    font-size: 11px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}


        .profile-email {
            margin-top: 6px;
            color: #e5f2fb;
            font-size: 11px;
            overflow-wrap: anywhere;
        }

        .role-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 9px;
            border-radius: 3px;
            color: var(--accent-dark);
            background: #fff;
            font-size: 10px;
            font-weight: 700;
        }

        .profile-panel-footer {
            display: flex;
            justify-content: flex-end;
            padding: 12px;
            background: #fafafa;
        }

        .signout-button {
            padding: 8px 12px;
            border: 1px solid #dce2e8;
            border-radius: 3px;
            color: #465367;
            background: white;
        }

        .signout-button:hover {
            background: #f0f3f6;
        }

        .theme-panel {
            padding: 15px;
        }

        .theme-panel-title {
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .theme-options {
            display: flex;
            gap: 11px;
        }

        /* =====================================================
           AREA UTAMA DAN SIDEBAR
        ===================================================== */

        .workspace {
            display: flex;
            align-items: stretch;
            min-height: calc(100vh - 56px);
        }

        .sidebar {
            flex: 0 0 var(--sidebar-width);
            width: var(--sidebar-width);
            overflow: hidden;
            padding: 10px 0 25px;
            border-right: 1px solid #dce1e7;
            background: #f8f9fb;
            transition:
                width .2s ease,
                flex-basis .2s ease,
                padding .2s ease;
        }

        .sidebar-collapsed .sidebar {
            flex-basis: 0;
            width: 0;
            padding-right: 0;
            padding-left: 0;
            border-right: 0;
        }

        .theme-strip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 0 12px 17px;
            border-bottom: 1px solid #edf0f4;
        }

        .theme-swatch {
            flex: 0 0 19px;
            width: 19px;
            height: 19px;
            padding: 0;
            border: 2px solid transparent;
            border-radius: 0;
            outline: 1px solid transparent;
        }

        .theme-swatch:hover,
        .theme-swatch.active {
            outline: 1px solid #475569;
            outline-offset: 2px;
        }

        .swatch-blue {
            background: #318ab7;
        }

        .swatch-red {
            background: #e74c3c;
        }

        .swatch-black {
            background: #22272e;
        }

        .swatch-green {
            background: #159c78;
        }

        .swatch-purple {
            background: #6258a8;
        }

        .menu-heading {
            padding: 19px 16px 12px;
            color: #8993a1;
            font-size: 10px;
            letter-spacing: .65px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            padding: 0 0 12px;
        }

        .nav-link,
        .nav-group-toggle,
        .nav-disabled {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            min-height: 43px;
            padding: 11px 16px;
            border: 0;
            border-radius: 0;
            text-align: left;
            color: #364152;
            background: transparent;
            font-size: 13px;
        }

        .nav-link:hover,
        .nav-group-toggle:hover {
            color: #1d5c88;
            background: #eef2f6;
        }

        .nav-link.active {
            color: #182536;
            background: #eeeeef;
            font-weight: 700;
        }

        .nav-icon {
            display: inline-flex;
            flex: 0 0 19px;
            align-items: center;
            justify-content: center;
            width: 19px;
            font-size: 15px;
        }

        .nav-label {
            flex: 1;
            min-width: 0;
        }

        .nav-arrow {
            margin-left: auto;
            color: #7d8897;
            font-size: 12px;
            transition: transform .15s ease;
        }

        .nav-group.open > .nav-group-toggle .nav-arrow {
            transform: rotate(90deg);
        }

        .nav-submenu {
            display: none;
            padding: 2px 0 5px;
            background: #f0f2f5;
        }

        .nav-group.open .nav-submenu {
            display: block;
        }

        .nav-submenu .nav-link {
            min-height: 37px;
            padding-top: 9px;
            padding-bottom: 9px;
            padding-left: 45px;
            font-size: 12px;
        }

        .nav-disabled {
            color: #8c96a5;
            cursor: default;
        }

        /* =====================================================
           AREA KONTEN
        ===================================================== */

        .main-content {
            flex: 1;
            min-width: 0;
            padding: 0;
            background: white;
        }

        .breadcrumb-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 42px;
            padding: 0 18px;
            border-bottom: 1px solid #edf0f2;
            color: #7b8796;
            background: #fff;
            font-size: 12px;
        }

        .breadcrumb-home {
            color: var(--accent-dark);
            font-weight: 700;
        }

        .content-body {
            min-height: 570px;
            padding: 18px;
        }

        .page-heading {
            margin-bottom: 22px;
            padding-bottom: 17px;
            border-bottom: 1px solid #e7ebef;
        }

        .page-heading .page-title {
            margin: 0;
            color: var(--accent-dark);
            font-size: 22px;
            font-weight: 400;
        }

        /* Komponen untuk konten dashboard */

        .welcome-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
            padding: 24px;
            border-radius: 3px;
            color: white;
            background: var(--accent);
        }

        .welcome-banner h2 {
            margin: 10px 0;
            font-size: 24px;
        }

        .welcome-banner p {
            max-width: 650px;
            margin: 0;
            color: #f0f7fb;
            font-size: 13px;
            line-height: 1.8;
        }

        .panel {
            margin-bottom: 22px;
            padding: 22px;
            border: 1px solid #e4e7eb;
            border-radius: 2px;
            background: #fff;
        }

        .panel h2 {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 500;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.7;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-top: 16px;
        }

        .summary-card {
            min-width: 0;
            padding: 17px;
            border: 1px solid #e5e9ed;
            border-radius: 2px;
            background: #fff;
        }

        .summary-label {
            margin-bottom: 16px;
            color: #748196;
            font-size: 12px;
        }

        .summary-value {
            margin-bottom: 8px;
            color: #26364d;
            font-size: 20px;
            font-weight: 700;
        }

        .status-pill {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 2px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-waiting {
            color: #956400;
            background: #fff2cf;
        }

        .status-accepted {
            color: #087443;
            background: #e1f7eb;
        }

        .status-rejected {
            color: #b42336;
            background: #ffeaed;
        }

        .primary-link {
            display: inline-block;
            margin-top: 12px;
            padding: 9px 13px;
            color: white;
            background: var(--accent);
            font-size: 12px;
            font-weight: 700;
        }

        .primary-link:hover {
            background: var(--accent-dark);
            color: white;
        }

        .footer {
            padding: 17px 18px;
            border-top: 1px solid #dce1e7;
            color: #616d7d;
            background: #fff;
            font-size: 11px;
        }

        /* =====================================================
           RESPONSIF
        ===================================================== */

        @media (max-width: 900px) {
            :root {
                --sidebar-width: 215px;
            }

            .content-body {
                padding: 15px;
            }

            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .topbar {
                height: 56px;
            }

            .brand-area {
                flex-basis: auto;
                flex: 1;
                padding: 0 10px;
            }

            .brand-mark {
                flex-basis: 34px;
                height: 34px;
                font-size: 19px;
            }

            .brand-name {
                font-size: 12px;
            }

            .brand-subtitle {
                display: none;
            }

            .topbar-main {
                flex: 0 0 auto;
                padding: 0 4px;
            }

            .topbar-actions {
                gap: 0;
            }

            .topbar-actions > .dropdown-wrap:first-child {
                display: none;
            }

            .profile-toggle {
                gap: 5px;
                padding: 0 4px;
            }

            .profile-meta {
    display: none;
}

            .profile-caret {
                display: none;
            }

            .workspace {
                display: block;
            }

            .sidebar {
                width: 100%;
                padding-top: 8px;
                border-right: 0;
                border-bottom: 1px solid #dce1e7;
            }

            .sidebar-collapsed .sidebar {
                display: none;
            }

            .theme-strip {
                justify-content: flex-start;
                padding-left: 18px;
            }

            .menu-heading {
                padding-top: 13px;
            }

            .main-content {
                width: 100%;
            }

            .content-body {
                min-height: 450px;
                padding: 14px;
            }

            .welcome-banner {
                padding: 19px;
            }

            .welcome-banner h2 {
                font-size: 21px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .dropdown-panel {
                position: fixed;
                top: 57px;
                right: 8px;
                width: min(290px, calc(100vw - 16px));
            }
        }
    </style>
</head>

<body>

@php
    $cisUser = session('cis_user', []);

    $role = $cisUser['role'] ?? 'Pengguna';
    $username = $cisUser['username'] ?? 'Pengguna';
    $email = $cisUser['email'] ?? '';

    $displayName = trim(
        (string) ($cisUser['nama'] ?? $cisUser['name'] ?? $username)
    );

    $nameParts = preg_split(
        '/\s+/',
        $displayName,
        -1,
        PREG_SPLIT_NO_EMPTY
    ) ?: [];

    $initial = strtoupper(
        substr($nameParts[0] ?? $username, 0, 1)
        . (
            count($nameParts) > 1
                ? substr($nameParts[count($nameParts) - 1], 0, 1)
                : ''
        )
    );
@endphp


    <div class="app-shell">

        {{-- HEADER UTAMA --}}
        <header class="topbar">

            <div class="brand-area">
                <div class="brand-mark">M</div>

                <div>
                    <div class="brand-name">MONITORING PDLK</div>
                    <div class="brand-subtitle">Institut Teknologi Del</div>
                </div>
            </div>

            <div class="topbar-main">
                <button
                    type="button"
                    class="icon-button"
                    id="sidebarToggle"
                    aria-label="Buka atau tutup menu"
                    title="Buka atau tutup menu"
                >
                    ☰
                </button>

                <div class="topbar-actions">

                    {{-- NOTIFIKASI --}}
                    <div class="dropdown-wrap">
                        <button
                            type="button"
                            class="icon-button"
                            id="notificationToggle"
                            aria-expanded="false"
                            aria-label="Notifikasi"
                            title="Notifikasi"
                        >
                            ♧
                            <span class="notification-dot"></span>
                        </button>

                        <div
                            class="dropdown-panel"
                            id="notificationPanel"
                            hidden
                        >
                            <div class="dropdown-heading">
                                Notifikasi
                            </div>

                            <div class="notification-content">
                                Belum ada notifikasi baru.

                                <br><br>

                                Informasi pendaftaran, monitoring,
                                dan perubahan status akan ditampilkan
                                di bagian ini ketika modul terkait tersedia.
                            </div>
                        </div>
                    </div>

                    {{-- PROFIL PENGGUNA --}}
                    <div class="dropdown-wrap">

<button
    type="button"
    class="profile-toggle"
    id="profileToggle"
    aria-expanded="false"
    aria-label="Profil pengguna"
>
    <span class="avatar">{{ $initial }}</span>

    <span class="profile-name">
    {{ count($nameParts) >= 2
        ? $nameParts[0] . ' ' . $nameParts[count($nameParts) - 1]
        : $displayName }}
</span>

        <span class="profile-role-label">
            {{ $role }}
        </span>
    </span>

    <span class="profile-caret">▼</span>
</button>


                        <div
                            class="dropdown-panel"
                            id="profilePanel"
                            hidden
                        >
                            <div class="profile-panel-header">
                                <div class="profile-big-avatar">
                                    {{ $initial }}
                                </div>


<div class="profile-full-name">
    {{ $displayName }}
</div>

<div class="profile-username">
    Akun: {{ $username }}
</div>

@if (!empty($cisUser['nim']))
    <div class="profile-username">
        NIM: {{ $cisUser['nim'] }}
    </div>
@endif


                                @if ($email)
                                    <div class="profile-email">
                                        {{ $email }}
                                    </div>
                                @endif

                                <span class="role-badge">
                                    {{ $role }}
                                </span>
                            </div>

                            <div class="profile-panel-footer">
                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="signout-button"
                                    >
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- PENGATURAN TEMA --}}
                    <div class="dropdown-wrap">
                        <button
                            type="button"
                            class="icon-button"
                            id="themeToggle"
                            aria-label="Pengaturan tampilan"
                            aria-expanded="false"
                            title="Pengaturan tampilan"
                        >
                            ⚙
                        </button>

                        <div
                            class="dropdown-panel"
                            id="themePanel"
                            hidden
                        >
                            <div class="theme-panel">
                                <div class="theme-panel-title">
                                    Warna tampilan
                                </div>

                                <div class="theme-options">
                                    <button
                                        type="button"
                                        class="theme-swatch swatch-blue"
                                        data-theme="blue"
                                        aria-label="Tema biru"
                                        title="Biru"
                                    ></button>

                                    <button
                                        type="button"
                                        class="theme-swatch swatch-red"
                                        data-theme="red"
                                        aria-label="Tema merah"
                                        title="Merah"
                                    ></button>

                                    <button
                                        type="button"
                                        class="theme-swatch swatch-black"
                                        data-theme="black"
                                        aria-label="Tema hitam"
                                        title="Hitam"
                                    ></button>

                                    <button
                                        type="button"
                                        class="theme-swatch swatch-green"
                                        data-theme="green"
                                        aria-label="Tema hijau"
                                        title="Hijau"
                                    ></button>

                                    <button
                                        type="button"
                                        class="theme-swatch swatch-purple"
                                        data-theme="purple"
                                        aria-label="Tema ungu"
                                        title="Ungu"
                                    ></button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="workspace" id="workspace">

            {{-- SIDEBAR --}}
            <aside class="sidebar">

                <div class="theme-strip" aria-label="Pilihan warna">
                    <button
                        type="button"
                        class="theme-swatch swatch-blue"
                        data-theme="blue"
                        aria-label="Tema biru"
                        title="Biru"
                    ></button>

                    <button
                        type="button"
                        class="theme-swatch swatch-red"
                        data-theme="red"
                        aria-label="Tema merah"
                        title="Merah"
                    ></button>

                    <button
                        type="button"
                        class="theme-swatch swatch-black"
                        data-theme="black"
                        aria-label="Tema hitam"
                        title="Hitam"
                    ></button>

                    <button
                        type="button"
                        class="theme-swatch swatch-green"
                        data-theme="green"
                        aria-label="Tema hijau"
                        title="Hijau"
                    ></button>

                    <button
                        type="button"
                        class="theme-swatch swatch-purple"
                        data-theme="purple"
                        aria-label="Tema ungu"
                        title="Ungu"
                    ></button>
                </div>

                <div class="menu-heading">Main Navigation</div>

                <nav class="sidebar-nav">

                    {{-- MENU UMUM --}}
                    <a
                        href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    >
                        <span class="nav-icon">◉</span>
                        <span class="nav-label">Dashboard</span>
                    </a>

                    {{-- MENU MAHASISWA --}}
                    @if ($role === 'Mahasiswa')


<div class="nav-group {{ (request()->routeIs('pdlk.*') || request()->routeIs('registration.create')) ? 'open' : '' }}">
    <button
        type="button"
        class="nav-group-toggle"
        data-menu-toggle
        aria-expanded="{{ (request()->routeIs('pdlk.*') || request()->routeIs('registration.create')) ? 'true' : 'false' }}"
    >
        <span class="nav-icon">▦</span>
        <span class="nav-label">Kegiatan PDLK</span>
        <span class="nav-arrow">›</span>
    </button>

    <div class="nav-submenu">
        <a
            href="{{ route('pdlk.index') }}"
            class="nav-link {{ (request()->routeIs('pdlk.index') || request()->routeIs('registration.create')) ? 'active' : '' }}"
        >
            Daftar PDLK
        </a>
    </div>
</div>





<div class="nav-group {{ request()->routeIs('monitoring.*') ? 'open' : '' }}">
    <button
        type="button"
        class="nav-group-toggle"
        data-menu-toggle
        aria-expanded="{{ request()->routeIs('monitoring.*') ? 'true' : 'false' }}"
    >
        <span class="nav-icon">◷</span>
        <span class="nav-label">Monitoring</span>
        <span class="nav-arrow">›</span>
    </button>

    <div class="nav-submenu">
        <a
            href="{{ route('monitoring.index') }}"
            class="nav-link {{ request()->routeIs('monitoring.index') ? 'active' : '' }}"
        >
            Pilih Kegiatan
        </a>
    </div>
</div>





{{-- MENU LAPORAN --}}
<div class="nav-group">
    <button
        type="button"
        class="nav-group-toggle"
        data-menu-toggle
        aria-expanded="false"
    >
        <span class="nav-icon">▤</span>

        <span class="nav-label">
            Laporan
        </span>

        <span class="nav-arrow">›</span>
    </button>

    <div class="nav-submenu">
        <span
            class="nav-disabled"
            title="Modul laporan belum tersedia"
        >
            Rekap Monitoring PDLK
        </span>
    </div>
</div>


                    {{-- MENU DOSEN --}}

@elseif ($role === 'Dosen')

    {{-- Pengelolaan PDLK --}}
    <div class="nav-group {{ request()->routeIs('registration.*') ? 'open' : '' }}">
        <button
            type="button"
            class="nav-group-toggle"
            data-menu-toggle
            aria-expanded="{{ request()->routeIs('registration.*') ? 'true' : 'false' }}"
        >
            <span class="nav-icon">▦</span>

            <span class="nav-label">
                Pengelolaan PDLK
            </span>

            <span class="nav-arrow">›</span>
        </button>

        <div class="nav-submenu">
            <a
                href="{{ route('registration.index') }}"
                class="nav-link {{ request()->routeIs('registration.index') ? 'active' : '' }}"
                @if (request()->routeIs('registration.index')) aria-current="page" @endif
            >
                Daftar Pendaftaran
            </a>
        </div>
    </div>

    {{-- Monitoring Mahasiswa --}}
    <div class="nav-group">
        <button
            type="button"
            class="nav-group-toggle"
            data-menu-toggle
            aria-expanded="false"
        >
            <span class="nav-icon">◷</span>

            <span class="nav-label">
                Monitoring Mahasiswa
            </span>

            <span class="nav-arrow">›</span>
        </button>

        <div class="nav-submenu">
            <span
                class="nav-disabled"
                title="Modul belum tersedia"
            >
                Monitoring Pertengahan
            </span>

            <span
                class="nav-disabled"
                title="Modul belum tersedia"
            >
                Monitoring Akhir
            </span>
        </div>
    </div>

    {{-- Penilaian --}}
    <div class="nav-group">
        <button
            type="button"
            class="nav-group-toggle"
            data-menu-toggle
            aria-expanded="false"
        >
            <span class="nav-icon">☆</span>

            <span class="nav-label">
                Penilaian
            </span>

            <span class="nav-arrow">›</span>
        </button>

        <div class="nav-submenu">
            <span
                class="nav-disabled"
                title="Modul belum tersedia"
            >
                Evaluasi Kegiatan
            </span>
        </div>
    </div>

@endif

                </nav>
            </aside>

            {{-- AREA KONTEN --}}
            <main class="main-content">


@php
    $breadcrumbs = [];
    $jenis = request()->route('jenis');
    $jenisQuery = request()->query('jenis');

    if (request()->routeIs('dashboard')) {
        $breadcrumbs[] = [
            'label' => 'Dashboard',
            'url' => route('dashboard'),
        ];

    } elseif (request()->routeIs('pdlk.index')) {
        $breadcrumbs[] = [
            'label' => 'Daftar PDLK',
            'url' => route('pdlk.index'),
        ];

    } elseif (request()->routeIs('registration.create')) {
        $breadcrumbs[] = [
            'label' => 'Daftar PDLK',
            'url' => route('pdlk.index'),
        ];

        $breadcrumbs[] = [
            'label' => 'Formulir Pendaftaran',
            'url' => route(
                'registration.create',
                $jenis ? ['jenis' => $jenis] : []
            ),
        ];

    } elseif (request()->routeIs('monitoring.index')) {
        $breadcrumbs[] = [
            'label' => 'Monitoring',
            'url' => route('monitoring.index'),
        ];

        $breadcrumbs[] = [
            'label' => 'Pilih Kegiatan',
            'url' => route('monitoring.index'),
        ];

    } elseif (request()->routeIs('monitoring.pertengahan')) {
        $breadcrumbs[] = [
            'label' => 'Monitoring',
            'url' => route('monitoring.index'),
        ];

        $breadcrumbs[] = [
            'label' => 'Monitoring Pertengahan',
            'url' => route(
                'monitoring.pertengahan',
                $jenisQuery ? ['jenis' => $jenisQuery] : []
            ),
        ];

    } elseif (request()->routeIs('monitoring.akhir')) {
        $breadcrumbs[] = [
            'label' => 'Monitoring',
            'url' => route('monitoring.index'),
        ];

        $breadcrumbs[] = [
            'label' => 'Monitoring Akhir',
            'url' => route(
                'monitoring.akhir',
                $jenisQuery ? ['jenis' => $jenisQuery] : []
            ),
        ];

    } elseif (request()->routeIs('registration.index')) {
        $breadcrumbs[] = [
            'label' => 'Daftar Pendaftaran PDLK',
            'url' => route('registration.index'),
        ];
    }
@endphp

<div class="breadcrumb-bar">
    <a href="{{ route('dashboard') }}" class="breadcrumb-home">
        ⌂ Home
    </a>

    @if (count($breadcrumbs) > 0)
        @foreach ($breadcrumbs as $crumb)
            <span class="breadcrumb-separator">/</span>

            <a href="{{ $crumb['url'] }}" class="breadcrumb-link">
                {{ $crumb['label'] }}
            </a>
        @endforeach
    @else
        <span class="breadcrumb-separator">/</span>

        <a href="{{ url()->current() }}" class="breadcrumb-link">
            @yield('breadcrumb', 'Dashboard')
        </a>
    @endif
</div>

<style>
    .breadcrumb-bar a {
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .breadcrumb-bar .breadcrumb-link {
        color: #60758f;
    }

    .breadcrumb-bar .breadcrumb-link:hover,
    .breadcrumb-bar .breadcrumb-home:hover {
        color: #348db8;
        text-decoration: underline;
    }

    .breadcrumb-bar .breadcrumb-separator {
        margin: 0 8px;
        color: #9aa5b4;
    }
</style>


                <div class="content-body">
                    <div class="page-heading">
                        <h1 class="page-title">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>

                    @yield('content')
                </div>

                <footer class="footer">
                    <strong>Monitoring PDLK</strong>
                    &nbsp; Copyright © UPT PP-ESTEM · Institut Teknologi Del
                </footer>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const workspace = document.getElementById('workspace');

            const sidebarToggle = document.getElementById('sidebarToggle');
            const notificationToggle = document.getElementById('notificationToggle');
            const profileToggle = document.getElementById('profileToggle');
            const themeToggle = document.getElementById('themeToggle');

            const notificationPanel = document.getElementById('notificationPanel');
            const profilePanel = document.getElementById('profilePanel');
            const themePanel = document.getElementById('themePanel');

            const dropdowns = [
                { button: notificationToggle, panel: notificationPanel },
                { button: profileToggle, panel: profilePanel },
                { button: themeToggle, panel: themePanel }
            ];

            // Buka dan tutup sidebar.
            sidebarToggle.addEventListener('click', function () {
                workspace.classList.toggle('sidebar-collapsed');
            });

            // Dropdown notifikasi, profil dan pengaturan.
            function closeDropdowns(exceptPanel = null) {
                dropdowns.forEach(function (dropdown) {
                    if (dropdown.panel !== exceptPanel) {
                        dropdown.panel.hidden = true;
                        dropdown.button.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            dropdowns.forEach(function (dropdown) {
                dropdown.button.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const shouldOpen = dropdown.panel.hidden;

                    closeDropdowns();

                    if (shouldOpen) {
                        dropdown.panel.hidden = false;
                        dropdown.button.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            // Tutup dropdown jika area di luar dropdown diklik.
            document.addEventListener('click', function (event) {
                const clickedInsideDropdown = dropdowns.some(function (dropdown) {
                    return dropdown.button.contains(event.target)
                        || dropdown.panel.contains(event.target);
                });

                if (!clickedInsideDropdown) {
                    closeDropdowns();
                }
            });

            // Buka/tutup submenu sidebar.
            document.querySelectorAll('[data-menu-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const group = button.closest('.nav-group');
                    const isOpen = group.classList.toggle('open');

                    button.setAttribute('aria-expanded', String(isOpen));
                });
            });

            // Pilihan tema. Pilihan tetap tersimpan setelah halaman dimuat ulang.
            const themes = {
                blue: {
                    accent: '#318ab7',
                    dark: '#24749b',
                    soft: '#e7f3fa'
                },
                red: {
                    accent: '#e74c3c',
                    dark: '#c0392b',
                    soft: '#fdecea'
                },
                black: {
                    accent: '#343a40',
                    dark: '#22272e',
                    soft: '#e9ecef'
                },
                green: {
                    accent: '#159c78',
                    dark: '#107a5e',
                    soft: '#e3f5ef'
                },
                purple: {
                    accent: '#6258a8',
                    dark: '#4e438d',
                    soft: '#efedfa'
                }
            };

            function applyTheme(themeName) {
                const theme = themes[themeName] || themes.blue;
                const root = document.documentElement;

                root.style.setProperty('--accent', theme.accent);
                root.style.setProperty('--accent-dark', theme.dark);
                root.style.setProperty('--accent-soft', theme.soft);

                document.querySelectorAll('[data-theme]').forEach(function (button) {
                    const isActive = button.dataset.theme === themeName;

                    button.classList.toggle('active', isActive);
                    button.setAttribute('aria-pressed', String(isActive));
                });
            }

            document.querySelectorAll('[data-theme]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const themeName = button.dataset.theme;

                    applyTheme(themeName);

                    try {
                        localStorage.setItem('monitoring-pdlk-theme', themeName);
                    } catch (error) {
                        // Tema tetap bisa digunakan meskipun penyimpanan browser tidak tersedia.
                    }
                });
            });

            try {
                const savedTheme = localStorage.getItem('monitoring-pdlk-theme');
                applyTheme(savedTheme || 'blue');
            } catch (error) {
                applyTheme('blue');
            }
        });
    </script>
</body>
</html>
