<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Sistem Informasi PDLK IT Del</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Hanken Grotesk', 'sans-serif'],
                    },
                    colors: {
                        del: {
                            primary: '#003580',
                            secondary: '#0055A5',
                            navbar: '#1a81c2',
                            accent: '#8cc5dd',
                            purpleDark: '#46166c',
                            purpleMid: '#664780',
                            bgLight: '#dce3eb',
                            textDark: '#444444',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Hanken Grotesk', sans-serif;
            color: #444444;
            background-color: #dce3eb;
        }
        .dot-pattern-subtle {
            background-image: radial-gradient(rgba(0, 53, 128, 0.07) 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
        .input-del:focus {
            border-color: #1a81c2;
            box-shadow: 0 0 0 4px rgba(140, 197, 221, 0.35);
            outline: none;
        }
        .input-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }
    </style>
</head>
<body class="min-h-screen relative flex flex-col justify-center items-center py-10 px-4 bg-[#dce3eb] antialiased overflow-x-hidden">

    {{-- Stripe tipis di paling atas layar --}}
    <div class="fixed top-0 left-0 w-full h-[4px] bg-[#46166c] z-50"></div>

    {{-- DEKORASI LATAR --}}
    <div class="w-[500px] h-[500px] rounded-full blur-3xl bg-[#1a81c2]/12 pointer-events-none fixed -top-40 -left-40 z-0"></div>
    <div class="w-[500px] h-[500px] rounded-full blur-3xl bg-[#46166c]/10 pointer-events-none fixed -bottom-40 -right-40 z-0"></div>
    <div class="fixed inset-0 dot-pattern-subtle pointer-events-none z-0"></div>

    <div class="fixed bottom-0 left-0 right-0 h-44 pointer-events-none z-0 overflow-hidden">
        <svg class="absolute bottom-0 w-[120%] -left-10 text-[#8cc5dd] opacity-35" preserveAspectRatio="none" viewBox="0 0 1200 120">
            <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
        </svg>
        <svg class="absolute bottom-0 w-[130%] -left-16 text-[#1a81c2] opacity-15" preserveAspectRatio="none" viewBox="0 0 1200 120">
            <path d="M0,30 C200,100 450,10 650,70 C850,130 1050,40 1200,85 L1200,120 L0,120 Z" fill="currentColor"></path>
        </svg>
    </div>

    {{-- KARTU LOGIN --}}
    <main class="w-full max-w-[460px] relative z-10 my-auto">
        <div class="bg-white rounded-[20px] shadow-2xl p-6 sm:p-8 border border-white/80 relative overflow-hidden transition-all duration-300">
            <div class="absolute top-0 left-0 right-0 h-[5px] bg-gradient-to-r from-[#1a81c2] via-[#0055A5] to-[#46166c]"></div>

            {{-- Header Kartu: Bingkai Rounded Persegi agar Logo Del Tidak Terpotong --}}
            <div class="text-center pt-2 pb-5">
                <div class="mx-auto w-24 h-24 rounded-2xl bg-white p-2 flex items-center justify-center relative shadow-md mb-4 ring-2 ring-[#1a81c2] ring-offset-4 ring-offset-white before:content-[''] before:absolute before:-inset-2 before:rounded-2xl before:border before:border-[#46166c]/60">
                    <img src="{{ asset('images/logo-del.png') }}" alt="Logo Institut Teknologi Del" class="w-full h-full object-contain">
                </div>

                <div class="font-bold text-[#003580] text-base leading-tight">UPT PP-ESTEM</div>
                <div class="font-medium text-[#0055A5] text-xs tracking-wider uppercase mb-3">Sistem Informasi PDLK</div>
                <div class="text-[11px] font-extrabold text-[#46166c] tracking-[0.2em] uppercase mb-1">SELAMAT DATANG</div>
                <h1 class="text-2xl font-extrabold text-[#003580] tracking-tight mb-2">Masuk ke Sistem Informasi PDLK</h1>

                <div class="flex items-center justify-center gap-1.5 mb-2.5">
                    <span class="w-10 h-[3px] bg-[#1a81c2] rounded-full"></span>
                    <span class="w-5 h-[3px] bg-[#46166c] rounded-full"></span>
                </div>
                <p class="text-xs text-gray-500 font-normal">Gunakan akun CIS Institut Teknologi Del Anda</p>
            </div>

            {{-- Flash Info / Warning --}}
            @if (session('warning'))
                <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start space-x-2.5 text-xs">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1">{{ session('warning') }}</div>
                </div>
            @endif

            {{-- Alert Error Otentikasi / Validasi --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start space-x-2.5 text-xs" id="errorAlert" role="alert">
                    <i class="bi bi-exclamation-circle-fill text-red-500 text-base shrink-0 mt-0.5"></i>
                    <div class="flex-1 space-y-1">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button aria-label="Tutup pesan error" class="text-red-400 hover:text-red-700 p-0.5"
                            onclick="document.getElementById('errorAlert').classList.add('hidden')" type="button">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- FORM LOGIN CIS --}}
            <form method="POST" action="{{ route('login.process') }}" class="space-y-4" id="loginForm">
                @csrf

                {{-- Username CIS --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5" for="username">
                        Username CIS <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-person text-lg text-gray-400"></i>
                        </div>
                        <input class="input-del {{ $errors->has('username') ? 'input-error' : '' }} w-full pl-10 pr-4 py-3 h-12 text-sm bg-gray-50/70 border border-[#c9d3de] rounded-[10px] text-gray-800 placeholder-gray-400 transition-all font-medium"
                               id="username"
                               name="username"
                               type="text"
                               value="{{ old('username', request()->cookie('pdlk_remember_username')) }}"
                               placeholder="Masukkan username CIS"
                               autocomplete="username"
                               required
                               autofocus>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5" for="password">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="bi bi-lock text-lg text-gray-400"></i>
                        </div>
                        <input class="input-del {{ $errors->has('password') || $errors->has('username') ? 'input-error' : '' }} w-full pl-10 pr-11 py-3 h-12 text-sm bg-gray-50/70 border border-[#c9d3de] rounded-[10px] text-gray-800 placeholder-gray-400 transition-all font-medium"
                               id="password"
                               name="password"
                               type="password"
                               placeholder="Masukkan kata sandi CIS"
                               autocomplete="current-password"
                               required>
                        <button class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-[#003580] focus:outline-none transition-colors"
                                id="togglePasswordBtn"
                                onclick="togglePasswordVisibility()"
                                title="Lihat kata sandi"
                                type="button">
                            <i class="bi bi-eye text-lg" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Checkbox Ingat Saya --}}
                <div class="flex items-center text-xs pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer text-gray-600 hover:text-gray-900 select-none">
                        <input class="w-4 h-4 rounded text-[#003580] focus:ring-[#8cc5dd] border-gray-300 accent-[#003580] cursor-pointer"
                               id="rememberMe"
                               name="remember"
                               type="checkbox"
                               value="1"
                               @checked(old('remember') || request()->cookie('pdlk_remember_username'))>
                        <span class="font-medium">Ingat saya</span>
                    </label>
                </div>

                {{-- Tombol Masuk --}}
                <div class="pt-2">
                    <button class="w-full h-12 bg-[#003580] hover:bg-[#0055A5] active:bg-[#002255] text-white font-semibold rounded-xl shadow-lg shadow-[#003580]/20 flex items-center justify-center space-x-2 text-sm transition-all transform active:scale-[0.99] cursor-pointer"
                            id="submitBtn"
                            type="submit">
                        <span class="flex items-center gap-2" id="btnText">
                            <i class="bi bi-box-arrow-in-right text-lg"></i>
                            <span>Masuk</span>
                        </span>
                        <span class="hidden flex items-center gap-2" id="btnSpinner">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tautan Kembali & Hak Cipta --}}
        <div class="relative z-10 mt-6 flex flex-col items-center gap-2 text-center">
            <a class="inline-flex items-center space-x-1.5 text-sm font-semibold text-[#003580] hover:underline transition-colors group"
               href="{{ route('beranda') }}">
                <i class="bi bi-arrow-left text-base transition-transform group-hover:-translate-x-1"></i>
                <span>Kembali ke Beranda</span>
            </a>
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} UPT PP-ESTEM Institut Teknologi Del
            </p>
        </div>
    </main>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('passwordEyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function () {
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const submitBtn = document.getElementById('submitBtn');

            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            submitBtn.classList.add('opacity-90', 'cursor-not-allowed');
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
