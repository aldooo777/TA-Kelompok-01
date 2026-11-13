@extends('layouts.pdlk')

@section('title', 'Dashboard | Monitoring PDLK')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="p-card">
    <h2 class="p-card-title">Akun belum memiliki peran di sistem PDLK</h2>
    <p class="p-card-subtitle">
        Anda berhasil login melalui CIS, tetapi akun ini belum terdaftar sebagai Staf UPT PP-ESTEM,
        Koordinator PDLK Prodi, atau Koordinator Program Studi. Hubungi UPT PP-ESTEM untuk mendapatkan akses.
    </p>
    <p style="margin-top:16px"><a href="{{ route('beranda') }}" class="p-btn p-btn-light">Ke halaman publik</a></p>
</div>
@endsection
