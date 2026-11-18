<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Monitoring PDLK</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            background: #f4f6f9;
            color: #354052;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border: 1px solid #e2e6eb;
            border-radius: 6px;
            padding: 28px 24px;
        }
        .login-head { text-align: center; margin-bottom: 22px; }
        .login-head img { width: 64px; height: 64px; }
        .login-head h1 { margin: 10px 0 2px; font-size: 18px; color: #26364d; }
        .login-head p { margin: 0; color: #7b8797; font-size: 13px; }
        .login-error {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #f5c2c0;
            border-radius: 4px;
            background: #fdf0ef;
            color: #b42318;
            font-size: 13px;
        }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: bold; }
        .input-wrap { position: relative; }
        .form-control {
            width: 100%;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #cfd6de;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-control:focus { outline: none; border-color: #318ab7; }
        #password { padding-right: 64px; }
        .lihat {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: none;
            color: #24749b;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-button {
            width: 100%;
            height: 40px;
            margin-top: 4px;
            border: 0;
            border-radius: 4px;
            background: #318ab7;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-button:hover { background: #24749b; }
        .login-button:disabled { opacity: .7; cursor: wait; }
        .login-note { margin: 16px 0 0; color: #7b8797; font-size: 12px; text-align: center; line-height: 1.5; }
        .login-back { display: block; margin-top: 14px; text-align: center; color: #24749b; font-size: 13px; text-decoration: none; }
        .login-back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main class="login-card">
        <div class="login-head">
            <img src="{{ asset('images/logo-monitoring-pdlk.png') }}" alt="Logo Monitoring PDLK">
            <h1>Monitoring PDLK</h1>
            <p>Institut Teknologi Del</p>
        </div>

        @if ($errors->any())
            <div class="login-error" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}" id="formLogin">
            @csrf

            <div class="form-group">
                <label for="username">Username CIS</label>
                <input type="text" id="username" name="username" class="form-control"
                       value="{{ old('username') }}" placeholder="Masukkan username" autocomplete="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Masukkan password" autocomplete="current-password" required>
                    <button type="button" class="lihat" id="lihatPassword">Lihat</button>
                </div>
            </div>

            <button type="submit" class="login-button" id="tombolMasuk">Masuk</button>
        </form>

        <p class="login-note">Gunakan username dan password akun CIS IT Del.</p>
        <a href="{{ route('beranda') }}" class="login-back">&larr; Kembali ke halaman utama</a>
    </main>

    <script>
        document.getElementById('lihatPassword').addEventListener('click', function () {
            var password = document.getElementById('password');
            var tampil = password.type === 'password';
            password.type = tampil ? 'text' : 'password';
            this.textContent = tampil ? 'Sembunyi' : 'Lihat';
        });

        document.getElementById('formLogin').addEventListener('submit', function () {
            var tombol = document.getElementById('tombolMasuk');
            tombol.disabled = true;
            tombol.textContent = 'Memproses...';
        });
    </script>
</body>
</html>
