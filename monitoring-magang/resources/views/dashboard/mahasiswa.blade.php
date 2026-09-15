<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Mahasiswa - Monitoring Magang</title>
</head>

<body>

    <h1>Dashboard Mahasiswa</h1>

    <p>
        Selamat datang,
        <strong>
            {{ session('cis_user')['username'] ?? '' }}
        </strong>
    </p>

    <p>
        Role:
        <strong>Mahasiswa</strong>
    </p>

    <p>
        NIM:
        <strong>
            {{ session('cis_user')['nim'] ?? 'Belum tersedia' }}
        </strong>
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
