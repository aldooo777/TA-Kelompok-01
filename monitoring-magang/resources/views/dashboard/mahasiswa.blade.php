
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa | Monitoring Magang</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f5f7fb;
            color: #17233c;
            font-size: 14px;
        }

        .app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 245px minmax(0, 1fr);
        }

        /* SIDEBAR */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e8edf4;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 42px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: #2458db;
            color: white;
            display: grid;
            place-items: center;
            font-size: 23px;
            font-weight: 800;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 750;
            letter-spacing: -0.4px;
        }

        .brand-subtitle {
            color: #8792a6;
            font-size: 11px;
            margin-top: 4px;
        }

        .menu-label {
            color: #99a3b5;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.3px;
            margin: 0 0 12px 12px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            border-radius: 10px;
            color: #68758d;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .menu-link.active {
            color: #2458db;
            background: #edf3ff;
        }

        .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 17px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding-top: 30px;
        }

        .help-card {
            border-radius: 13px;
            background: #f5f7fb;
            padding: 16px;
            margin-bottom: 20px;
        }

        .help-card strong {
            display: block;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .help-card p {
            color: #7c8799;
            font-size: 11px;
            line-height: 1.7;
        }

        .logout-form button {
            border: 1px solid #e8edf4;
            color: #c04450;
            background: white;
            padding: 12px;
            width: 100%;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 650;
            cursor: pointer;
        }

        .logout-form button:hover {
            background: #fff1f2;
        }

        /* KONTEN */
        .main {
            min-width: 0;
            padding: 28px 36px 35px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 27px;
            border-bottom: 1px solid #e8edf4;
            margin-bottom: 30px;
        }

        .breadcrumb {
            color: #8a95a8;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .topbar h2 {
            font-size: 21px;
            letter-spacing: -0.5px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 42px;
            height: 42px;
            background: #e3ebff;
            color: #2458db;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 750;
        }

        .profile-name {
            font-weight: 700;
            font-size: 12px;
        }

        .profile-role {
            color: #8993a5;
            font-size: 11px;
            margin-top: 4px;
        }

        .welcome {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            padding: 34px 36px;
            background: linear-gradient(120deg, #2149b8, #3979ec);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
            min-height: 205px;
            margin-bottom: 30px;
        }

        .welcome::after {
            content: "";
            width: 240px;
            height: 240px;
            border: 35px solid rgba(255,255,255,0.08);
            border-radius: 50%;
            position: absolute;
            right: 50px;
            top: -90px;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            max-width: 570px;
        }

        .welcome-tag {
            display: inline-block;
            padding: 7px 11px;
            border: 1px solid rgba(255,255,255,0.28);
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            margin-bottom: 17px;
        }

        .welcome h1 {
            font-size: clamp(23px, 3vw, 30px);
            line-height: 1.3;
            letter-spacing: -0.7px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #e0e9ff;
            font-size: 13px;
            line-height: 1.8;
            max-width: 500px;
        }

        .welcome-art {
            z-index: 1;
            flex-shrink: 0;
            width: 110px;
            height: 110px;
            border-radius: 25px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            display: grid;
            place-items: center;
            font-size: 48px;
        }

        .section-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 17px;
        }

        .section-heading h3 {
            font-size: 17px;
            letter-spacing: -0.3px;
        }

        .section-heading p {
            color: #919bad;
            font-size: 11px;
            margin-top: 5px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
            margin-bottom: 32px;
        }

        .stat-card {
            padding: 21px;
            background: white;
            border: 1px solid #e9edf4;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(30, 55, 90, 0.025);
            min-width: 0;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 43px;
            height: 43px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 21px;
        }

        .blue {
            color: #3265dc;
            background: #edf3ff;
        }

        .green {
            color: #19966c;
            background: #e8f8f1;
        }

        .orange {
            color: #d68b22;
            background: #fff4e3;
        }

        .stat-label {
            color: #778398;
            font-size: 12px;
            font-weight: 600;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 750;
            margin-bottom: 8px;
        }

        .stat-description {
            color: #97a0b0;
            font-size: 11px;
            line-height: 1.7;
        }

        .content-panel {
            background: white;
            border: 1px solid #e9edf4;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 22px;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 25px;
        }

        .step {
            position: relative;
            min-width: 0;
        }

        .step-number {
            width: 37px;
            height: 37px;
            border-radius: 50%;
            background: #edf3ff;
            color: #3265dc;
            display: grid;
            place-items: center;
            font-weight: 750;
            margin-bottom: 13px;
        }

        .step h4 {
            font-size: 12px;
            margin-bottom: 6px;
        }

        .step p {
            color: #96a0b1;
            font-size: 10px;
            line-height: 1.7;
        }

        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            background: #eff5ff;
            border: 1px solid #dfebff;
            padding: 18px;
            border-radius: 13px;
        }

        .info-icon {
            font-size: 22px;
        }

        .info-box strong {
            display: block;
            color: #244d9f;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .info-box p {
            color: #60779f;
            line-height: 1.8;
            font-size: 11px;
        }

        .footer {
            color: #9ba4b4;
            text-align: center;
            font-size: 11px;
            padding-top: 12px;
        }

        @media (max-width: 1000px) {
            .app {
                grid-template-columns: 205px minmax(0, 1fr);
            }

            .main {
                padding: 25px 22px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 17px;
            }
        }

        @media (max-width: 650px) {
            .app {
                display: block;
            }

            .sidebar {
                padding: 17px 20px;
                border-right: none;
                border-bottom: 1px solid #e8edf4;
            }

            .brand {
                margin-bottom: 17px;
            }

            .menu-label,
            .sidebar-bottom {
                display: none;
            }

            .menu-link {
                width: fit-content;
                margin-bottom: 0;
            }

            .main {
                padding: 22px 16px;
            }

            .topbar {
                align-items: flex-start;
            }

            .profile > div:last-child {
                display: none;
            }

            .welcome {
                padding: 27px 23px;
            }

            .welcome-art {
                display: none;
            }

            .steps {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                row-gap: 25px;
            }

            .content-panel {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="app">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">M</div>
                <div>
                    <div class="brand-name">Monitoring Magang</div>
                    <div class="brand-subtitle">Portal PDLK IT Del</div>
                </div>
            </div>

            <div class="menu-label">MENU UTAMA</div>

            <a href="{{ route('dashboard') }}" class="menu-link active">
                <span class="menu-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-bottom">
                <div class="help-card">
                    <strong>Portal Mahasiswa</strong>
                    <p>
                        Pusat informasi kegiatan Pembelajaran
                        di Luar Kampus (PDLK).
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('logout') }}"
                      class="logout-form">
                    @csrf
                    <button type="submit">↪ &nbsp; Logout</button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <main class="main">
            <header class="topbar">
                <div>
                    <div class="breadcrumb">Portal / Dashboard</div>
                    <h2>Dashboard Mahasiswa</h2>
                </div>

                <div class="profile">
                    <div class="avatar">
                        {{ strtoupper(substr(session('cis_user')['username'] ?? 'M', 0, 2)) }}
                    </div>
                    <div>
                        <div class="profile-name">
                            {{ session('cis_user')['username'] ?? 'Mahasiswa' }}
                        </div>
                        <div class="profile-role">Mahasiswa</div>
                    </div>
                </div>
            </header>

            <section class="welcome">
                <div class="welcome-content">
                    <span class="welcome-tag">PORTAL MAHASISWA PDLK</span>

                    <h1>
                        Selamat datang,
                        {{ session('cis_user')['username'] ?? 'Mahasiswa' }}!
                    </h1>

                    <p>
                        Pantau informasi kegiatan Pembelajaran di Luar Kampus
                        melalui satu portal. Informasi kegiatan dan laporan
                        akan ditampilkan sesuai modul yang tersedia.
                    </p>
                </div>

                <div class="welcome-art" aria-hidden="true">🎓</div>
            </section>

            <section>
                <div class="section-heading">
                    <div>
                        <h3>Ringkasan</h3>
                        <p>Informasi umum kegiatan PDLK Anda</p>
                    </div>
                </div>

                <div class="cards">
                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Status Kegiatan</span>
                            <div class="stat-icon blue">◷</div>
                        </div>
                        <div class="stat-value">Belum tersedia</div>
                        <p class="stat-description">
                            Status kegiatan akan muncul setelah data
                            terhubung ke dashboard.
                        </p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Aktivitas</span>
                            <div class="stat-icon green">▤</div>
                        </div>
                        <div class="stat-value">Belum tersedia</div>
                        <p class="stat-description">
                            Ringkasan aktivitas akan ditampilkan
                            pada tahap pengembangan berikutnya.
                        </p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">Laporan</span>
                            <div class="stat-icon orange">▧</div>
                        </div>
                        <div class="stat-value">Belum tersedia</div>
                        <p class="stat-description">
                            Informasi laporan akan ditampilkan setelah
                            modul pelaporan dikembangkan.
                        </p>
                    </article>
                </div>
            </section>

            <section class="content-panel">
                <div class="section-heading">
                    <div>
                        <h3>Alur Kegiatan PDLK</h3>
                        <p>Gambaran umum proses kegiatan mahasiswa</p>
                    </div>
                </div>

                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Pendaftaran</h4>
                        <p>Pengajuan dan pemeriksaan persyaratan.</p>
                    </div>

                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Seleksi</h4>
                        <p>Pemeriksaan dan penetapan status peserta.</p>
                    </div>

                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Pelaksanaan</h4>
                        <p>Pencatatan aktivitas dan penyusunan laporan.</p>
                    </div>

                    <div class="step">
                        <div class="step-number">4</div>
                        <h4>Evaluasi</h4>
                        <p>Pemantauan dan penilaian hasil kegiatan.</p>
                    </div>
                </div>
            </section>

            <section class="info-box">
                <div class="info-icon">ℹ️</div>
                <div>
                    <strong>Informasi untuk Mahasiswa</strong>
                    <p>
                        Dashboard ini merupakan pusat informasi mahasiswa.
                        Data status kegiatan, aktivitas, dan laporan belum
                        terhubung ke kartu ringkasan ini.
                    </p>
                </div>
            </section>

            <footer class="footer">
                Monitoring Magang · Sistem Informasi UPT PP-ESTEM
            </footer>
        </main>

    </div>
</body>
</html>
