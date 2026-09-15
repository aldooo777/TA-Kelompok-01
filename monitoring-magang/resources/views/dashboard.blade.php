<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Monitoring Magang</title>
</head>

<body>

    <h1>Dashboard Monitoring Magang</h1>

    <p>
        Selamat datang,
        <strong>{{ session('cis_user.username') }}</strong>
    </p>

    <p>
        Login berhasil menggunakan CIS Del.
    </p>

    <p>
    Role:
    <strong>{{ session('cis_user')['role'] ?? 'Tidak ada role' }}</strong>
</p>

<p>
    NIM:
    <strong>{{ session('cis_user')['nim'] ?? 'Belum tersedia' }}</strong>
</p>

<p>
    <a href="{{ route('registration.create') }}">
        <button type="button">Daftar Magang</button>
    </a>
</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">
            Logout
        </button>
    </form>

</body>

</html>
