
@extends('layouts.pdlk')

@section('title', 'Formulir Pendaftaran PDLK | Monitoring PDLK')
@section('breadcrumb', 'Kegiatan PDLK / Formulir Pendaftaran')
@section('page-title', 'Formulir Pendaftaran PDLK')

@section('content')

@php
    $jenisIkon = [
        'magang' => '💼',
        'kerja-praktik' => '🏢',
        'penelitian' => '🔬',
        'pertukaran-pelajar' => '🌐',
        'kkn' => '🤝',
        'studi-independen' => '📚',
        'kewirausahaan' => '💡',
    ];

    $ikonTerpilih = $jenisIkon[$jenisSlug] ?? '📋';
@endphp

<style>
    .registration-create-page {
        --create-blue: #348db8;
        --create-blue-dark: #287ba5;
        --create-text: #26364d;
        --create-muted: #718096;
        --create-border: #d8e1eb;
        --create-green: #00a65a;
        --create-red: #c62828;

        color: var(--create-text);
    }

    .registration-create-page,
    .registration-create-page * {
        box-sizing: border-box;
    }

    /* Judul formulir */
    .registration-create-heading {
        margin-bottom: 23px;
    }

    .registration-create-heading h2 {
        margin: 0 0 7px;
        color: var(--create-text);
        font-size: 20px;
        font-weight: 600;
        line-height: 1.5;
    }

    .registration-create-heading p {
        margin: 0;
        color: var(--create-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    /* Notifikasi */
    .registration-create-alert {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 14px 15px;
        margin-bottom: 21px;
        border: 1px solid #f0c4c7;
        border-left: 4px solid #dc3545;
        border-radius: 4px;
        background: #fff4f4;
        color: #9f2730;
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .registration-create-alert-success {
        border-color: #bce5ce;
        border-left-color: #198754;
        background: #edf9f1;
        color: #176a40;
    }

    .registration-create-alert-icon {
        flex: 0 0 auto;
        font-size: 17px;
    }

    .registration-create-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .registration-create-alert ul {
        padding-left: 18px;
        margin: 5px 0 0;
    }

    .registration-create-alert li + li {
        margin-top: 3px;
    }

    /* Panel jenis kegiatan */
    .registration-selected-type {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 17px;
        margin-bottom: 27px;
        border: 1px solid #d8e8f3;
        border-left: 4px solid var(--create-blue);
        border-radius: 4px;
        background: #f2f8fc;
    }

    .registration-selected-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        border-radius: 5px;
        background: #e0eff9;
        font-size: 24px;
    }

    .registration-selected-info {
        min-width: 0;
        flex: 1;
    }

    .registration-selected-label {
        margin-bottom: 4px;
        color: var(--create-muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .registration-selected-name {
        color: var(--create-blue-dark);
        font-size: 16px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    /* Formulir */
    .registration-create-form {
        width: 100%;
    }

    .registration-create-section-title {
        margin: 0 0 19px;
        padding-bottom: 12px;
        border-bottom: 1px solid #edf0f4;
        color: var(--create-text);
        font-size: 15px;
        font-weight: 600;
        line-height: 1.5;
    }

    .registration-create-field {
        min-width: 0;
        margin-bottom: 22px;
    }

    .registration-create-label {
        display: block;
        margin-bottom: 8px;
        color: #354052;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.6;
    }

    .registration-create-required {
        color: #dc3545;
    }

    .registration-create-control {
        display: block;
        width: 100%;
        min-width: 0;
        height: 43px;
        padding: 10px 12px;
        border: 1px solid #ccd6e3;
        border-radius: 4px;
        outline: none;
        background: #fff;
        color: #354052;
        font-family: inherit;
        font-size: 13px;
        line-height: 1.5;
        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .registration-create-control::placeholder {
        color: #9aa5b4;
    }

    .registration-create-control:hover {
        border-color: #b7c7d8;
    }

    .registration-create-control:focus {
        border-color: var(--create-blue);
        box-shadow: 0 0 0 3px rgba(52, 141, 184, .12);
    }

    .registration-create-control.is-invalid {
        border-color: #dc3545;
    }

    .registration-create-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, .10);
    }

    .registration-create-help {
        margin-top: 7px;
        color: var(--create-muted);
        font-size: 11px;
        line-height: 1.7;
    }

    .registration-create-error {
        margin-top: 6px;
        color: var(--create-red);
        font-size: 11px;
        line-height: 1.6;
    }

    /* Tombol */
    .registration-create-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 21px;
        margin-top: 5px;
        border-top: 1px solid #e5e9ef;
    }

    .registration-create-action-right {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 9px;
        margin-left: auto;
    }

    .registration-create-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 10px 15px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition:
            background .15s ease,
            border-color .15s ease;
    }

    .registration-create-button-secondary {
        border-color: #d8e1eb;
        background: #f3f6f9;
        color: #465367;
    }

    .registration-create-button-secondary:hover {
        border-color: #c7d2df;
        background: #e7edf3;
        color: #354052;
        text-decoration: none;
    }

    .registration-create-button-primary {
        border-color: var(--create-blue);
        background: var(--create-blue);
        color: #fff;
    }

    .registration-create-button-primary:hover {
        border-color: var(--create-blue-dark);
        background: var(--create-blue-dark);
        color: #fff;
    }

    .registration-create-button:focus-visible {
        outline: 3px solid rgba(52, 141, 184, .2);
        outline-offset: 3px;
    }

    /* Tampilan responsif */
    @media (max-width: 600px) {
        .registration-create-heading h2 {
            font-size: 19px;
        }

        .registration-selected-type {
            align-items: flex-start;
            padding: 14px;
        }

        .registration-selected-icon {
            flex-basis: 40px;
            width: 40px;
            height: 40px;
            font-size: 21px;
        }

        .registration-create-actions {
            align-items: stretch;
            flex-direction: column-reverse;
        }

        .registration-create-action-right {
            width: 100%;
            margin-left: 0;
        }

        .registration-create-button {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .registration-create-control,
        .registration-create-button {
            transition: none;
        }
    }
</style>

<section class="panel registration-create-page">

    {{-- Judul --}}
    <div class="registration-create-heading">
        <h2>Informasi Pendaftaran</h2>

        <p>
            Lengkapi data berikut untuk mengajukan kegiatan PDLK.
            Pastikan informasi yang dimasukkan sudah benar.
        </p>
    </div>

    {{-- Notifikasi berhasil --}}
    @if (session('success'))
        <div
            class="registration-create-alert registration-create-alert-success"
            role="status"
        >
            <span class="registration-create-alert-icon" aria-hidden="true">
                ✓
            </span>

            <div>
                <strong>Pendaftaran Berhasil</strong>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Ringkasan kesalahan --}}
    @if ($errors->any())
        <div class="registration-create-alert" role="alert">
            <span class="registration-create-alert-icon" aria-hidden="true">
                ⚠
            </span>

            <div>
                <strong>Pendaftaran belum dapat diproses.</strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Jenis kegiatan terpilih --}}
    <div class="registration-selected-type">
        <div class="registration-selected-icon" aria-hidden="true">
            {{ $ikonTerpilih }}
        </div>

        <div class="registration-selected-info">
            <div class="registration-selected-label">
                Jenis kegiatan yang dipilih
            </div>

            <div class="registration-selected-name">
                {{ $jenisPdlk }}
            </div>
        </div>
    </div>

    {{-- Formulir pendaftaran --}}
    <form
        class="registration-create-form"
        method="POST"
        action="{{ route('registration.store') }}"
    >
        @csrf

        <input
            type="hidden"
            name="jenis_pdlk"
            value="{{ old('jenis_pdlk', $jenisSlug) }}"
        >

        <h3 class="registration-create-section-title">
            Data Pendaftaran
        </h3>

        {{-- NIM --}}
        <div class="registration-create-field">
            <label
                for="mahasiswa_id"
                class="registration-create-label"
            >
                NIM Mahasiswa
                <span class="registration-create-required">*</span>
            </label>

            <input
                type="text"
                id="mahasiswa_id"
                name="mahasiswa_id"
                class="registration-create-control @error('mahasiswa_id') is-invalid @enderror"
                value="{{ old('mahasiswa_id') }}"
                placeholder="Masukkan NIM mahasiswa"
                maxlength="50"
                autocomplete="off"
                aria-describedby="mahasiswa-id-help"
                @if ($errors->has('mahasiswa_id')) aria-invalid="true" @endif
                required
            >

            <div
                id="mahasiswa-id-help"
                class="registration-create-help"
            >
                Gunakan NIM sesuai data mahasiswa di CIS.
            </div>

            @error('mahasiswa_id')
                <div class="registration-create-error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Tempat PDLK --}}
        <div class="registration-create-field">
            <label
                for="tempat_magang"
                class="registration-create-label"
            >
                Mitra / Tempat Pelaksanaan
                <span class="registration-create-required">*</span>
            </label>

            <input
                type="text"
                id="tempat_magang"
                name="tempat_magang"
                class="registration-create-control @error('tempat_magang') is-invalid @enderror"
                value="{{ old('tempat_magang') }}"
                placeholder="Contoh: nama perusahaan atau institusi"
                maxlength="200"
                autocomplete="organization"
                aria-describedby="tempat-magang-help"
                @if ($errors->has('tempat_magang')) aria-invalid="true" @endif
                required
            >

            <div
                id="tempat-magang-help"
                class="registration-create-help"
            >
                Masukkan nama perusahaan, institusi, atau tempat kegiatan PDLK.
            </div>

            @error('tempat_magang')
                <div class="registration-create-error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="registration-create-actions">

            <a
                href="{{ route('pdlk.index') }}"
                class="registration-create-button registration-create-button-secondary"
            >
                ← Kembali ke Daftar PDLK
            </a>

            <div class="registration-create-action-right">
                <button
                    type="submit"
                    class="registration-create-button registration-create-button-primary"
                >
                    Kirim Pendaftaran →
                </button>
            </div>

        </div>

    </form>

</section>

@endsection
