<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pendaftaran Magang</title>
</head>

<body>

    <h1>Pendaftaran Magang</h1>

    <p>
        Silakan lengkapi tempat magang Anda.
    </p>

    @if (session('success'))
    <p>
        <strong>{{ session('success') }}</strong>
    </p>
@endif

@if ($errors->has('registration'))
    <p>
        <strong>{{ $errors->first('registration') }}</strong>
    </p>
@endif

    <form method="POST" action="{{ route('registration.store') }}">
        @csrf

        <p>
            <label>NIM</label><br>

            <input
                type="text"
                value="{{ session('cis_user')['nim'] ?? '' }}"
                readonly
            >
        </p>

        <p>
            <label>Nama</label><br>

            <input
                type="text"
                value="{{ session('cis_user')['nama'] ?? '' }}"
                readonly
            >
        </p>

        <p>
            <label>Tempat Magang</label><br>

            <input
                type="text"
                name="tempat_magang"
                placeholder="Masukkan tempat magang"
                required
            >
        </p>

        <button type="submit">
            Daftar Magang
        </button>
    </form>

    <p>
        <a href="{{ route('dashboard') }}">
            Kembali ke Dashboard
        </a>
    </p>

</body>
</html>
