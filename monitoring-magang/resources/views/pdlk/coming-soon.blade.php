<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $namaPdlk }} | Pendaftaran PDLK</title>

    <style>
        body {
            margin: 0;
            padding: 24px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f4f7fc;
            color: #172b4d;
        }

        .card {
            width: 100%;
            max-width: 520px;
            padding: 36px;
            text-align: center;
            background: #fff;
            border: 1px solid #e2e9f4;
            border-radius: 18px;
        }

        .icon {
            font-size: 44px;
        }

        p {
            color: #71819b;
            line-height: 1.7;
        }

        a {
            display: inline-block;
            margin-top: 12px;
            padding: 12px 18px;
            color: white;
            background: #2458db;
            text-decoration: none;
            border-radius: 9px;
        }
    </style>
</head>

@extends('layouts.pdlk')

@section('title', 'Pendaftaran ' . $namaPdlk . ' | Monitoring PDLK')
@section('breadcrumb', 'Kegiatan PDLK / ' . $namaPdlk)
@section('page-title', 'Pendaftaran ' . $namaPdlk)

@section('content')

<style>
    .pdlk-coming-page {
        --coming-blue: #348db8;
        --coming-blue-dark: #287ba5;
        --coming-text: #26364d;
        --coming-muted: #718096;
        --coming-border: #e1e7ef;

        color: var(--coming-text);
    }

    .pdlk-coming-page,
    .pdlk-coming-page * {
        box-sizing: border-box;
    }

    .pdlk-coming-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 350px;
        padding: 40px 24px;
        border: 1px solid var(--coming-border);
        border-radius: 5px;
        background: #fff;
        text-align: center;
    }

    .pdlk-coming-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        margin-bottom: 22px;
        border: 1px solid #d8e8f3;
        border-radius: 8px;
        background: #eaf4fa;
        font-size: 32px;
    }

    .pdlk-coming-badge {
        display: inline-block;
        padding: 6px 11px;
        margin-bottom: 14px;
        border: 1px solid #f2d28b;
        border-radius: 3px;
        background: #fff8e7;
        color: #946200;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    .pdlk-coming-card h2 {
        margin: 0 0 12px;
        color: var(--coming-text);
        font-size: 22px;
        font-weight: 600;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .pdlk-coming-description {
        width: 100%;
        max-width: 480px;
        margin: 0;
        color: var(--coming-muted);
        font-size: 13px;
        line-height: 1.9;
    }

    .pdlk-coming-divider {
        width: 100%;
        max-width: 420px;
        height: 1px;
        margin: 25px 0;
        background: #edf0f4;
    }

    .pdlk-coming-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pdlk-coming-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 40px;
        padding: 10px 16px;
        border: 1px solid var(--coming-blue);
        border-radius: 4px;
        background: var(--coming-blue);
        color: #fff;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        text-decoration: none;
        transition: background .15s ease;
    }

    .pdlk-coming-button:hover {
        border-color: var(--coming-blue-dark);
        background: var(--coming-blue-dark);
        color: #fff;
        text-decoration: none;
    }

    .pdlk-coming-button:focus-visible {
        outline: 3px solid rgba(52, 141, 184, .2);
        outline-offset: 3px;
    }

    @media (max-width: 600px) {
        .pdlk-coming-card {
            min-height: 300px;
            padding: 28px 18px;
        }

        .pdlk-coming-card h2 {
            font-size: 19px;
        }

        .pdlk-coming-description {
            font-size: 12px;
        }

        .pdlk-coming-actions {
            width: 100%;
        }

        .pdlk-coming-button {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .pdlk-coming-button {
            transition: none;
        }
    }
</style>

<section class="panel pdlk-coming-page">

    <div class="pdlk-coming-card">

        <div class="pdlk-coming-icon" aria-hidden="true">
            📋
        </div>

        <span class="pdlk-coming-badge">
            DALAM PENGEMBANGAN
        </span>

        <h2>
            Pendaftaran {{ $namaPdlk }}
        </h2>

        <p class="pdlk-coming-description">
            Formulir pendaftaran untuk kegiatan {{ $namaPdlk }}
            sedang disiapkan. Silakan kembali ke Daftar PDLK
            untuk melihat pilihan kegiatan lainnya.
        </p>

        <div class="pdlk-coming-divider"></div>

        <div class="pdlk-coming-actions">
            <a
                href="{{ route('pdlk.index') }}"
                class="pdlk-coming-button"
            >
                ← Kembali ke Daftar PDLK
            </a>
        </div>

    </div>

</section>

@endsection

<body>
    <main class="card">
        <div class="icon">📋</div>

        <h1>Pendaftaran {{ $namaPdlk }}</h1>

        <p>
            Formulir pendaftaran untuk kegiatan ini sedang disiapkan.
            Silakan kembali ke pilihan PDLK untuk memilih kegiatan lain.
        </p>

        <a href="{{ route('pdlk.index') }}">
            Kembali ke Pilihan PDLK
        </a>
    </main>
</body>
</html>
