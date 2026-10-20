
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Dosen | Monitoring Magang</title>

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
            background: #fff;
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
            font-size: 22px;
            font-weight: 800;
        }

        .brand-name {
            font-size: 15px;
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
            background: #f5f7fb;
            border-radius: 13px;
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
            width: 100%;
            padding: 12px;
            border: 1px solid #e8edf4;
            border-radius: 10px;
            background: #fff;
            color: #c04450;
            font-size: 13px;
            font-weight: 650;
            cursor: pointer;
        }

        .logout-form button:hover {
            background: #fff1f2;
        }

        /* KONTEN UTAMA */
        .main {
            min-width: 0;
            padding: 28px 36px 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            flex-shrink: 0;
            border-radius: 50%;
            background: #e3ebff;
            color: #2458db;
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

        /* BANNER */
        .welcome {
            position: relative;
            overflow: hidden;
            min-height: 205px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
            padding: 34px 36px;
            margin-bottom: 30px;
            border-radius: 20px;
            background: linear-gradient(120deg, #2149b8, #3979ec);
            color: white;
        }

        .welcome::after {
            content: "";
            position: absolute;
            width: 240px;
            height: 240px;
            top: -90px;
            right: 50px;
            border: 35px solid rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            max-width: 620px;
        }

        .welcome-tag {
            display: inline-block;
            padding: 7px 11px;
            margin-bottom: 17px;
            border: 1px solid rgba(255,255,255,0.28);
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
        }

        .welcome h1 {
            font-size: clamp(23px, 3vw, 30px);
            line-height: 1.35;
            letter-spacing: -0.7px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #e0e9ff;
            font-size: 13px;
            line-height: 1.8;
            max-width: 520px;
        }

        .welcome-art {
            position: relative;
            z-index: 1;
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 25px;
            background: rgba(255,255,255,0.14);
            font-size: 48px;
        }

        /* JUDUL BAGIAN */
        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            line-height: 1.6;
        }

        /* KARTU RINGKASAN */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
            margin-bottom: 32px;
        }

        .stat-card {
            min-width: 0;
            padding: 21px;
            border: 1px solid #e9edf4;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(30,55,90,0.025);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-label {
            color: #778398;
            font-size: 12px;
            font-weight: 600;
        }

        .stat-icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            border-radius: 12px;
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

        .stat-value {
            font-size: 16px;
            font-weight: 750;
            margin-bottom: 8px;
            overflow-wrap: anywhere;
        }

        .stat-description {
            color: #97a0b0;
            font-size: 11px;
            line-height: 1.7;
        }

        /* PANEL */
        .content-panel {
            padding: 25px;
            margin-bottom: 22px;
            border: 1px solid #e9edf4;
            border-radius: 16px;
            background: white;
        }

        .panel-description {
            color: #8c97a9;
            font-size: 12px;
            line-height: 1.8;
            margin-top: -5px;
            margin-bottom: 20px;
        }

        /* DAFTAR FUNGSI DOSEN */
        .function-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .function-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            min-width: 0;
            padding: 17px;
            border: 1px solid #edf0f5;
            border-radius: 13px;
            background: #fff;
        }

        .function-icon {
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #edf3ff;
            font-size: 21px;
        }

        .function-item h4 {
            font-size: 13px;
            margin: 3px 0 7px;
        }

        .function-item p {
            color: #8994a6;
            font-size: 11px;
            line-height: 1.7;
        }

        .status-label {
            display: inline-block;
            padding: 5px 8px;
            margin-top: 10px;
            border-radius: 7px;
            background: #f1f3f7;
            color: #727f93;
            font-size: 10px;
            font-weight: 650;
        }

        /* INFORMASI */
        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding: 18px;
            border: 1px solid #dfebff;
            border-radius: 13px;
            background: #eff5ff;
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
            font-size: 11px;
            line-height: 1.8;
        }

        .footer {
            padding-top: 25px;
            color: #9ba4b4;
            text-align: center;
            font-size: 11px;
        }

        /* RESPONSIVE */
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

            .function-list {
                grid-template-columns: 1fr;
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

            <div class="menu-label">MENU DOSEN</div>

            <a href="{{ route('dashboard') }}"
               class="menu-link active">
                <span class="menu-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-bottom">
                <div class="help-card">
                    <strong>Portal Dosen</strong>
                    <p>
                        Pusat informasi untuk mendukung pemantauan
                        dan evaluasi kegiatan PDLK mahasiswa.
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
                    <h2>Dashboard Dosen</h2>
                </div>

                <div class="profile">
                    <div class="avatar">
                        {{ strtoupper(substr(session('cis_user')['username'] ?? 'D', 0, 2)) }}
                    </div>

                    <div>
                        <div class="profile-name">
                            {{ session('cis_user')['username'] ?? 'Dosen' }}
                        </div>
                        <div class="profile-role">Dosen Pembimbing</div>
                    </div>
                </div>
            </header>

            <!-- BANNER SAMBUTAN -->
            <section class="welcome">
                <div class="welcome-content">
                    <span class="welcome-tag">
                        PORTAL DOSEN PEMBIMBING
                    </span>

                    <h1>
                        Selamat datang,
                        {{ session('cis_user')['username'] ?? 'Bapak/Ibu Dosen' }}!
                    </h1>

                    <p>
                        Kelola dan pantau perkembangan kegiatan PDLK
                        mahasiswa melalui satu portal terintegrasi.
                        Informasi akan ditampilkan sesuai modul yang tersedia.
                    </p>
                </div>

                <div class="welcome-art" aria-hidden="true">
                    👨‍🏫
                </div>
            </section>

            <!-- RINGKASAN -->
            <section>
                <div class="section-heading">
                    <div>
                        <h3>Ringkasan Pembimbingan</h3>
                        <p>Informasi umum tugas pembimbingan Anda</p>
                    </div>
                </div>

                <div class="cards">

                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">
                                Mahasiswa Bimbingan
                            </span>

                            <div class="stat-icon blue">♙</div>
                        </div>

                        <div class="stat-value">Belum tersedia</div>

                        <p class="stat-description">
                            Data mahasiswa bimbingan akan ditampilkan
                            setelah modul pengelolaan PDLK terhubung.
                        </p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">
                                Aktivitas Mahasiswa
                            </span>

                            <div class="stat-icon green">▤</div>
                        </div>

                        <div class="stat-value">Belum tersedia</div>

                        <p class="stat-description">
                            Ringkasan aktivitas mahasiswa akan ditampilkan
                            setelah modul monitoring dikembangkan.
                        </p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-top">
                            <span class="stat-label">
                                Penilaian
                            </span>

                            <div class="stat-icon orange">☆</div>
                        </div>

                        <div class="stat-value">Belum tersedia</div>

                        <p class="stat-description">
                            Informasi penilaian akan tersedia setelah
                            modul evaluasi terhubung.
                        </p>
                    </article>

                </div>
            </section>

            <!-- FUNGSI DOSEN -->
            <section class="content-panel">
                <div class="section-heading">
                    <div>
                        <h3>Fungsi Pembimbingan</h3>
                        <p>Ruang lingkup pekerjaan dosen dalam sistem</p>
                    </div>
                </div>

                <p class="panel-description">
                    Berikut fungsi utama yang disiapkan untuk mendukung
                    pemantauan kegiatan PDLK mahasiswa.
                </p>

                <div class="function-list">

                    <article class="function-item">
                        <div class="function-icon">♙</div>

                        <div>
                            <h4>Daftar Mahasiswa Bimbingan</h4>

                            <p>
                                Melihat informasi mahasiswa yang menjadi
                                tanggung jawab pembimbing.
                            </p>

                            <span class="status-label">
                                Belum terhubung
                            </span>
                        </div>
                    </article>

                    <article class="function-item">
                        <div class="function-icon">◷</div>

                        <div>
                            <h4>Monitoring Aktivitas</h4>

                            <p>
                                Memantau perkembangan dan catatan aktivitas
                                mahasiswa selama menjalani PDLK.
                            </p>

                            <span class="status-label">
                                Belum terhubung
                            </span>
                        </div>
                    </article>

                    <article class="function-item">
                        <div class="function-icon">▤</div>

                        <div>
                            <h4>Pemeriksaan Laporan</h4>

                            <p>
                                Memeriksa laporan perkembangan dan laporan
                                akhir mahasiswa sesuai ketentuan kegiatan.
                            </p>

                            <span class="status-label">
                                Belum terhubung
                            </span>
                        </div>
                    </article>

                    <article class="function-item">
                        <div class="function-icon">☆</div>

                        <div>
                            <h4>Penilaian Magang</h4>

                            <p>
                                Melakukan evaluasi dan memberikan penilaian
                                sesuai kewenangan dosen pembimbing.
                            </p>

                            <span class="status-label">
                                Belum terhubung
                            </span>
                        </div>
                    </article>

                </div>
            </section>

            <!-- INFORMASI -->
            <section class="info-box">
                <div class="info-icon">ℹ️</div>

                <div>
                    <strong>Informasi Sistem</strong>

                    <p>
                        Tampilan dashboard dosen sudah disiapkan.
                        Data mahasiswa bimbingan, aktivitas, laporan,
                        dan penilaian belum diambil dari backend.
                        Fungsi tersebut akan dihubungkan pada tahap
                        pengembangan modul berikutnya.
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
