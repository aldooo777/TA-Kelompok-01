
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Monitoring PDLK</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --cis-blue: #398db8;
            --cis-blue-dark: #287ba5;
            --cis-input: #e8f0fe;
            --cis-border: #dedede;
            --cis-text: #555;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #fff;
            color: var(--cis-text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }


.login-page {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    min-height: 100vh;
    padding: 115px 16px 30px;
}



.login-card {
    width: 386px;
    max-width: 100%;
    padding: 22px 22px 24px;
    border: 1px solid #e5e5e5;
    background: #fff;
    transform: none;
}


        /* Logo dan identitas institusi */

        .institution-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 45px;
        }


.institution-logo {
    display: block;
    width: 76px;
    height: auto;
    object-fit: contain;
    margin: 0 auto;
}


        .institution-name {
            margin-top: 7px;
            color: #555;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Pemisah */

        .login-divider {
            width: 100%;
            height: 1px;
            margin-bottom: 25px;
            border: 0;
            background: #ededed;
        }

        /* Form login */

        .login-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #555;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control {
            display: block;
            width: 100%;
            height: 37px;
            padding: 8px 12px;
            border: 1px solid #cfd7e2;
            border-radius: 0;
            background: var(--cis-input);
            color: #333;
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .form-control:focus {
            border-color: #78a8d9;
            box-shadow: 0 0 0 2px rgba(57, 141, 184, .10);
        }

        .form-control::placeholder {
            color: #8b96a5;
        }

        /* Pesan kesalahan */

        .login-error {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #f1c6c6;
            background: #fff0f0;
            color: #a12626;
            font-size: 12px;
            line-height: 1.6;
        }

        /* Bagian bawah form */

        .login-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
        }

        .remember-option {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #777;
            font-size: 13px;
            line-height: 1.5;
            cursor: pointer;
        }

        .remember-option input {
            width: 19px;
            height: 19px;
            margin: 0;
            accent-color: var(--cis-blue);
            cursor: pointer;
        }

        .login-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            height: 37px;
            padding: 0 20px;
            border: 1px solid var(--cis-blue);
            border-radius: 0;
            background: var(--cis-blue);
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease;
        }

        .login-button:hover {
            border-color: var(--cis-blue-dark);
            background: var(--cis-blue-dark);
        }

        .login-button:focus-visible {
            outline: 3px solid rgba(57, 141, 184, .25);
            outline-offset: 2px;
        }

        /* Tampilan layar kecil */

/* Ukuran logo untuk semua ukuran layar */
.login-logo {
    display: block !important;
    width: 100px !important;
    height: 100px !important;
    max-width: 100px !important;
    object-fit: contain;
    margin: 0 auto 12px !important;
}

.institution-name {
    margin-top: 12px;
    color: #354052;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
}

.institution-subtitle {
    margin-top: 4px;
    color: #718096;
    font-size: 12px;
    text-align: center;
}

@media (max-width: 420px) {
    .login-page {
        align-items: flex-start;
        padding: 30px 16px;
    }

    .login-card {
        width: 386px;
        padding: 20px 18px 22px;
        transform: none;
    }

    .institution-brand {
        margin-bottom: 34px;
    }

    .login-divider {
        margin-bottom: 22px;
    }

    .login-actions {
        gap: 8px;
    }
}

    </style>
</head>

<body>

    <main class="login-page">

        <section class="login-card" aria-label="Formulir login">

            {{-- Identitas institusi --}}
            <div class="institution-brand">


<img
    src="{{ asset('images/logo-monitoring-pdlk.png') }}"
    alt="Logo Monitoring PDLK"
    class="login-logo"
>

                <div class="institution-name">
                    <div class="institution-name">Monitoring PDLK</div>
<div class="institution-subtitle">Institut Teknologi Del</div>
                </div>

            </div>

            <hr class="login-divider">

            {{-- Pesan kesalahan login --}}
            @if ($errors->any())
                <div class="login-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Formulir login --}}
            <form
                class="login-form"
                method="POST"
                action="{{ route('login.process') }}"
            >
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div class="login-actions">

                    <label class="remember-option">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                        >

                        <span>Remember Me</span>
                    </label>

                    <button type="submit" class="login-button">
                        Sign In
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>
</html>
