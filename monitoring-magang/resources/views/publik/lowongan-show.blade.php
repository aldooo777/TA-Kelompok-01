@extends($portal ? 'layouts.pdlk' : 'layouts.publik')

@php
    $v = $lowongan;
    $dibuka = lowongan_dibuka($v);
@endphp

@section('title', $v->title . ($portal ? ' | Monitoring PDLK' : ''))
@section('breadcrumb', 'Kegiatan PDLK / Lowongan / ' . $v->activity_name)
@section('page-title', $v->title)

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <div class="pub-crumb"><a href="{{ route('lowongan.index') }}">Lowongan</a> / {{ $v->activity_name }}</div>
        <h1>{{ $v->title }}</h1>
        <p>{{ $v->partner_name }}@if ($v->location) · {{ $v->location }}@endif</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    @if ($portal)
        @include('partials.flash')
        <div>
            <a href="{{ route('portal.lowongan.index') }}" class="p-btn p-btn-light p-btn-sm">← Semua lowongan</a>
            <span class="p-muted" style="margin-left:8px">{{ $v->partner_name }}@if ($v->location) · {{ $v->location }}@endif</span>
        </div>
    @endif
    <div class="p-card">
        <dl class="p-dl">
            <dt>Mitra</dt><dd>{{ $v->partner_name }}</dd>
            <dt>Bentuk kegiatan</dt><dd>{{ $v->activity_name }}</dd>
            @if ($v->period_name)<dt>Periode</dt><dd>{{ $v->period_name }}</dd>@endif
            @if ($v->position)<dt>Posisi</dt><dd>{{ $v->position }}</dd>@endif
            @if ($v->quota)<dt>Kuota</dt><dd>{{ $v->quota }} orang</dd>@endif
            <dt>Periode pendaftaran</dt>
            <dd>
                {{ tgl($v->registration_start_date) ?: 'Sekarang' }} – {{ tgl($v->registration_deadline) ?: 'Sampai kuota terpenuhi' }}
                <span class="p-badge {{ $dibuka ? 'p-badge-dibuka' : 'p-badge-ditutup' }}">{{ $dibuka ? 'Dibuka' : 'Ditutup' }}</span>
            </dd>
        </dl>
    </div>

    @if ($v->cover_image)
        <div class="p-detail-media"><img src="{{ file_url($v->cover_image) }}" alt="Poster {{ $v->title }}"></div>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Deskripsi</h2>
        <div class="p-prose" style="margin-top:12px">{{ $v->content }}</div>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Kriteria Pendaftar</h2>
        <div class="p-prose" style="margin-top:12px">{{ $v->criteria }}</div>
    </div>

    @include('partials.lampiran', ['lampiran' => $lampiran])

    @if (sprint(2) && $hasil->isNotEmpty())
        <div class="p-alert p-alert-success">
            Hasil seleksi sudah diumumkan:
            @foreach ($hasil as $h)<a href="{{ route('hasil.show', $h->slug) }}"><strong>{{ $h->title }}</strong></a>@if (!$loop->last), @endif @endforeach
        </div>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Cara Mendaftar</h2>
        @if ($v->registration_type === 'external')
            <p class="p-card-subtitle">
                Pendaftaran lowongan ini dilakukan langsung di situs mitra. Mahasiswa yang dinyatakan lulus
                akan didata oleh UPT PP-ESTEM / koordinator PDLK prodi sebagai mahasiswa aktif PDLK.
            </p>
            <div style="margin-top:16px">
                @if ($dibuka)
                    <a href="{{ $v->external_url }}" target="_blank" rel="noopener" class="p-btn p-btn-primary">Daftar di Situs Mitra ↗</a>
                @else
                    <button class="p-btn p-btn-light" disabled>Pendaftaran ditutup</button>
                @endif
            </div>
        @else
            <p class="p-card-subtitle">
                Siapkan satu file PDF yang menggabungkan KTM, KTP, CV, portofolio, transkrip nilai, dan surat pengantar.
                Seleksi dilakukan oleh mitra; UPT PP-ESTEM meneruskan data pendaftar dan memperbarui status pendaftaran Anda.
            </p>
            <div style="margin-top:16px">
                @if ($dibuka)
                    <a href="{{ route('mahasiswa.pendaftaran.create', $v->slug) }}" class="p-btn p-btn-primary">
                        {{ session()->has('cis_user') ? 'Daftar Sekarang →' : 'Login untuk Mendaftar →' }}
                    </a>
                @else
                    <button class="p-btn p-btn-light" disabled>Pendaftaran ditutup</button>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
