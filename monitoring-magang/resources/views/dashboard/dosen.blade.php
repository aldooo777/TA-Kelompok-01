<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Dosen - Monitoring Magang</title>
</head>

<body>

    <h1>Dashboard Dosen</h1>

    <p>
        Selamat datang,
        <strong>
            {{ session('cis_user')['username'] ?? '' }}
        </strong>
    </p>

    <p>
        Role:
        <strong>Dosen</strong>
    </p>

    <h2>Menu Dosen</h2>

    <ul>
        <li>Monitoring Mahasiswa Magang</li>
        <li>Daftar Mahasiswa Bimbingan</li>
        <li>Penilaian Magang</li>
    </ul>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit">
            Logout
        </button>
    </form>

</body>
</html>
