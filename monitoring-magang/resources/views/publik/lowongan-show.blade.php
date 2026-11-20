@extends($portal ? 'layouts.pdlk' : 'layouts.publik')

@php
    $v = $lowongan;
    $dibuka = lowongan_dibuka($v);

    $sekarang = \Carbon\Carbon::now('Asia/Jakarta');
    $akanDibuka = !$dibuka && !$v->is_closed && $v->registration_start_date
        && \Carbon\Carbon::parse($v->registration_start_date, 'Asia/Jakarta')->startOfDay()->isFuture();

    $segeraTutup = false;
    $sisaTeks = null;
    $persenBerjalan = null;

    if ($dibuka && $v->registration_deadline) {
        $deadline = \Carbon\Carbon::parse($v->registration_deadline, 'Asia/Jakarta')->endOfDay();
        if ($deadline->isFuture()) {
            $diff = $sekarang->diff($deadline);
            $segeraTutup = $diff->days <= 7;
            $sisaTeks = $diff->days > 0
                ? $diff->days . ' Hari ' . $diff->h . ' Jam lagi'
                : $diff->h . ' Jam ' . $diff->i . ' Menit lagi';
        }
        if ($v->registration_start_date) {
            $start = \Carbon\Carbon::parse($v->registration_start_date, 'Asia/Jakarta')->startOfDay();
            $totalDetik = max(1, $start->diffInSeconds($deadline));
            $berjalan = max(0, min($totalDetik, $start->diffInSeconds($sekarang, false)));
            $persenBerjalan = (int) round(($berjalan / $totalDetik) * 100);
        }
    }

    $paragrafDeskripsi = collect(preg_split('/\r?\n+/', trim((string) ($v->content ?? ''))))
        ->map(fn ($p) => trim($p))
        ->filter()
        ->values();

    $poinKriteria = collect(preg_split('/\r?\n+/', trim((string) ($v->criteria ?? ''))))
        ->map(fn ($p) => trim(preg_replace('/^(\d+[\.\)]\s*|[-•*]\s*)/', '', $p)))
        ->filter()
        ->values();
@endphp

@section('title', $v->title . ($portal ? ' | Monitoring PDLK' : ''))
@section('breadcrumb', 'Kegiatan PDLK / Lowongan / ' . $v->activity_name)
@section('page-title', $v->title)

@if (! $portal)
@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- 1. BANNER DETAIL LOWONGAN --}}
    <section class="relative w-full overflow-hidden"
             style="background: linear-gradient(135deg, rgba(0, 33, 85, 0.94) 0%, rgba(0, 85, 165, 0.9) 100%), url('{{ asset('images/kampus-del.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin pt-8 pb-20">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-label-md text-label-md text-[#8cc5dd] mb-6 flex-wrap">
                <a class="hover:text-white transition-colors flex items-center gap-1.5 text-[#8cc5dd]" href="{{ route('beranda') }}">
                    <i class="bi bi-house-door"></i><span>Beranda</span>
                </a>
                <span class="text-white/60">/</span>
                <a class="hover:text-white transition-colors text-[#8cc5dd]" href="{{ route('lowongan.index') }}">Lowongan PDLK</a>
                <span class="text-white/60">/</span>
                <span class="text-white font-semibold">{{ $v->title }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-20 h-20 rounded-2xl bg-white text-[#003580] shadow-lg flex items-center justify-center shrink-0 border border-white/20 overflow-hidden">
                    @if ($v->cover_image)
                        <img src="{{ file_url($v->cover_image) }}" alt="{{ $v->partner_name }}" class="w-full h-full object-cover">
                    @else
                        <i class="bi bi-building text-4xl text-[#1a81c2]"></i>
                    @endif
                </div>

                <div class="flex flex-col items-start">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full bg-[#46166c] text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm">
                            {{ $v->activity_name }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm {{ $v->registration_type === 'external' ? 'bg-[#664780]' : 'bg-[#0055A5]' }}">
                            {{ $v->registration_type === 'external' ? 'Eksternal' : 'Internal' }}
                        </span>
                        @if ($segeraTutup)
                            <span class="px-3 py-1 rounded-full bg-rose-600 text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Segera Ditutup
                            </span>
                        @elseif ($dibuka)
                            <span class="px-3 py-1 rounded-full bg-emerald-700 text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-white"></span> Dibuka
                            </span>
                        @elseif ($akanDibuka)
                            <span class="px-3 py-1 rounded-full bg-[#1a81c2] text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm">
                                Akan Dibuka
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-slate-600 text-white font-label-sm text-xs font-semibold uppercase tracking-wide shadow-sm">
                                Ditutup
                            </span>
                        @endif
                    </div>

                    <h1 class="text-white font-headline-xl text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight mb-2 leading-tight">
                        {{ $v->title }}
                    </h1>
                    <div class="text-[#8cc5dd] font-body-lg text-lg font-medium flex items-center gap-2 flex-wrap">
                        <i class="bi bi-buildings-fill text-base"></i>
                        <span>{{ $v->partner_name }}</span>
                        @if ($v->position)
                            <span class="text-white/50">·</span>
                            <span class="text-white/90 text-base">{{ $v->position }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-1">
            <svg class="w-full h-12 sm:h-16 block" preserveAspectRatio="none" viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,20 Q360,60 720,25 T1440,25 L1440,80 L0,80 Z" fill="#ffffff"></path>
            </svg>
        </div>
    </section>

    {{-- 2. KONTEN UTAMA DUA KOLOM --}}
    <div class="w-full bg-white py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">

            @if (sprint(2) && $hasil->isNotEmpty())
                <div class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-xl shrink-0"></i>
                    <div class="font-body-md text-body-md">
                        Hasil seleksi telah diumumkan:
                        @foreach ($hasil as $h)
                            <a href="{{ route('hasil.show', $h->slug) }}" class="font-bold underline hover:text-emerald-700">{{ $h->title }}</a>@if (!$loop->last), @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- KOLOM KIRI (±65%) --}}
                <div class="lg:col-span-8 space-y-10">

                    {{-- KARTU RINGKASAN LOWONGAN --}}
                    <div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30 shadow-sm">
                        <h2 class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-wider font-bold mb-4 flex items-center gap-1.5">
                            <i class="bi bi-info-circle"></i>
                            <span>Ringkasan Informasi Lowongan</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#1a81c2] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-buildings-fill"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Mitra</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $v->partner_name }}</span>
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#46166c] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-briefcase-fill"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Jenis Kegiatan</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $v->activity_name }}</span>
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#1a81c2] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Periode</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $v->period_name ?: 'Sesuai jadwal' }}</span>
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#46166c] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Lokasi Penempatan</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $v->location ?: '-' }}</span>
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#1a81c2] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Kuota Penerimaan</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $v->quota ? $v->quota . ' Mahasiswa' : 'Tidak dibatasi' }}</span>
                                </div>
                            </div>

                            <div class="bg-white p-3.5 rounded-lg border border-outline-variant/30 flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#46166c] text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-[11px] text-on-surface-variant uppercase tracking-wider font-semibold">Bidang Usaha Mitra</span>
                                    <span class="font-label-md text-label-md text-primary font-bold">{{ $mitra->field ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- POSTER LOWONGAN (jika ada) --}}
                    @if ($v->cover_image)
                        <div class="rounded-2xl overflow-hidden border border-outline-variant/30 shadow-sm bg-surface-container-low">
                            <img src="{{ file_url($v->cover_image) }}" alt="Poster {{ $v->title }}" class="w-full max-h-[520px] object-contain mx-auto">
                        </div>
                    @endif

                    {{-- DESKRIPSI LOWONGAN --}}
                    <div>
                        <div class="mb-4">
                            <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">TENTANG LOWONGAN</span>
                            <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Deskripsi Posisi &amp; Peran</h2>
                            <div class="flex items-center gap-1 mt-2">
                                <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                                <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                            </div>
                        </div>
                        <div class="space-y-4 font-body-md text-body-md text-on-surface-variant leading-relaxed text-justify">
                            @forelse ($paragrafDeskripsi as $p)
                                <p>{{ $p }}</p>
                            @empty
                                <p class="text-on-surface-variant/70">Belum ada deskripsi rinci untuk lowongan ini.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- KRITERIA PENDAFTAR --}}
                    <div>
                        <div class="mb-4">
                            <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">SYARAT &amp; KETENTUAN</span>
                            <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Kriteria Pendaftar</h2>
                            <div class="flex items-center gap-1 mt-2">
                                <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                                <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                            </div>
                        </div>

                        @if ($poinKriteria->isEmpty())
                            <div class="p-4 rounded-lg bg-surface-container-low text-on-surface-variant">
                                Tidak ada kriteria khusus yang dicantumkan.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($poinKriteria as $kriteria)
                                    @php $isGenap = $loop->iteration % 2 === 0; @endphp
                                    <div class="flex items-start gap-3.5 p-3.5 rounded-lg bg-surface-container-low border border-outline-variant/30">
                                        <div class="w-7 h-7 rounded-full text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm text-sm font-bold {{ $isGenap ? 'bg-[#46166c]' : 'bg-[#1a81c2]' }}">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <p class="font-body-md text-body-md text-on-surface">{{ $kriteria }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- DOKUMEN YANG DIPERLUKAN (Untuk Jalur Internal) --}}
                    @if ($v->registration_type !== 'external')
                        <div>
                            <div class="mb-4">
                                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">PERSYARATAN BERKAS</span>
                                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Dokumen yang Diperlukan</h2>
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl border-l-4 border-[#1a81c2] bg-[#8cc5dd]/10 mb-5 flex items-start gap-3.5">
                                <i class="bi bi-exclamation-circle-fill text-2xl text-[#1a81c2] shrink-0 mt-0.5"></i>
                                <div>
                                    <h3 class="font-label-md text-label-md font-bold text-[#003580] mb-1">Catatan Penting Pendaftaran:</h3>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                        Siapkan satu file <strong>PDF</strong> yang menggabungkan seluruh berkas pendaftaran di bawah ini sebelum mengisi formulir pendaftaran.
                                    </p>
                                </div>
                            </div>

                            <div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/30 space-y-2.5">
                                <div class="flex items-center gap-2.5 font-body-md text-body-md text-on-surface">
                                    <i class="bi bi-file-earmark-check text-[#1a81c2] text-lg"></i>
                                    <span>Curriculum Vitae (CV) dan Portofolio terbaru.</span>
                                </div>
                                <div class="flex items-center gap-2.5 font-body-md text-body-md text-on-surface">
                                    <i class="bi bi-file-earmark-check text-[#46166c] text-lg"></i>
                                    <span>Transkrip Nilai / KHS resmi terkini.</span>
                                </div>
                                <div class="flex items-center gap-2.5 font-body-md text-body-md text-on-surface">
                                    <i class="bi bi-file-earmark-check text-[#1a81c2] text-lg"></i>
                                    <span>KTM, KTP, dan Surat Keterangan / Pengantar terkait.</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- LAMPIRAN DOKUMEN (Dinamis dari announcement_attachments) --}}
                    @if ($lampiran->isNotEmpty())
                        <div>
                            <div class="mb-4">
                                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">UNDUHAN</span>
                                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Dokumen &amp; Lampiran Terkait</h2>
                                <div class="flex items-center gap-1 mt-2">
                                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($lampiran as $f)
                                    @php
                                        $ukuranTeks = null;
                                        if ($f->file_size) {
                                            $ukuranTeks = $f->file_size >= 1048576
                                                ? number_format($f->file_size / 1048576, 2) . ' MB'
                                                : max(1, (int) round($f->file_size / 1024)) . ' KB';
                                        }
                                    @endphp
                                    <div class="bg-white p-4 rounded-xl border border-outline-variant/30 shadow-sm flex items-center justify-between gap-3 hover:shadow-md transition-shadow">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-11 h-11 rounded-lg bg-red-700/10 text-red-700 flex items-center justify-center text-2xl shrink-0">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-label-md text-label-md text-primary font-bold truncate" title="{{ $f->file_name }}">{{ $f->file_name }}</span>
                                                @if ($ukuranTeks)
                                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Ukuran: {{ $ukuranTeks }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ file_url($f->file_path) }}" target="_blank" rel="noopener"
                                           class="px-3 py-1.5 rounded border border-[#003580] text-[#003580] hover:bg-[#003580] hover:text-white transition-colors font-label-sm text-label-sm font-semibold flex items-center gap-1.5 shrink-0 no-underline">
                                            <i class="bi bi-download"></i>
                                            <span>Unduh</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- KOLOM KANAN: SIDEBAR AKSI & INFORMASI (~35%) --}}
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-28">

                    {{-- KARTU PENDAFTARAN --}}
                    @if ($v->registration_type === 'external')
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-[#664780]/30">
                            <div class="p-5 text-white flex items-center gap-3" style="background: linear-gradient(135deg, #46166c 0%, #664780 100%);">
                                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-xl shrink-0">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </div>
                                <div>
                                    <h2 class="font-headline-sm text-headline-sm font-bold text-white">Jalur Eksternal / Mitra</h2>
                                    <span class="font-label-sm text-label-sm text-white/80">Pendaftaran via Situs Mitra</span>
                                </div>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="space-y-1.5 font-label-md text-label-md border-b border-outline-variant/30 pb-3">
                                    <div class="flex items-center justify-between text-on-surface-variant">
                                        <span>Tanggal Buka:</span>
                                        <span class="font-semibold text-primary">{{ tgl($v->registration_start_date) ?: 'Sekarang' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-on-surface-variant">
                                        <span>Batas Akhir:</span>
                                        <span class="font-bold text-error">{{ tgl($v->registration_deadline) ?: 'Sampai kuota terpenuhi' }}</span>
                                    </div>
                                </div>

                                @if ($sisaTeks)
                                    <div class="bg-[#46166c] text-white p-4 rounded-xl text-center shadow-inner">
                                        <span class="text-[11px] text-[#8cc5dd] uppercase tracking-wider font-semibold block mb-0.5">Sisa Waktu Pendaftaran:</span>
                                        <div class="font-headline-md text-xl font-bold tracking-tight text-white">{{ $sisaTeks }}</div>
                                        @if ($persenBerjalan !== null)
                                            <div class="mt-3 w-full bg-white/20 h-2.5 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full" style="width: {{ $persenBerjalan }}%; background: linear-gradient(to right, #1a81c2, #8cc5dd);"></div>
                                            </div>
                                            <span class="text-[10px] text-white/70 block text-right mt-1">{{ $persenBerjalan }}% masa pendaftaran berjalan</span>
                                        @endif
                                    </div>
                                @endif

                                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                    Pendaftaran lowongan ini dilakukan langsung di situs resmi mitra. Mahasiswa yang lulus akan didata oleh UPT PP-ESTEM / koordinator PDLK prodi.
                                </p>

                                @if ($dibuka && $v->external_url)
                                    <a href="{{ $v->external_url }}" target="_blank" rel="noopener"
                                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-[#664780] hover:bg-[#46166c] text-white font-label-md text-label-md font-bold transition-all shadow-md no-underline">
                                        <span>Daftar di Situs Mitra</span>
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @else
                                    <button disabled class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-slate-200 text-slate-500 font-label-md text-label-md font-bold cursor-not-allowed">
                                        <span>{{ $akanDibuka ? 'Pendaftaran Belum Dibuka' : 'Pendaftaran Ditutup' }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-outline-variant/30">
                            <div class="p-5 text-white flex items-center gap-3" style="background: linear-gradient(135deg, #003580 0%, #1a81c2 100%);">
                                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-xl shrink-0">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <h2 class="font-headline-sm text-headline-sm font-bold text-white">Informasi &amp; Pendaftaran</h2>
                                    <span class="font-label-sm text-label-sm text-white/80">Jalur Internal Kampus IT Del</span>
                                </div>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="space-y-1.5 font-label-md text-label-md border-b border-outline-variant/30 pb-3">
                                    <div class="flex items-center justify-between text-on-surface-variant">
                                        <span>Tanggal Buka:</span>
                                        <span class="font-semibold text-primary">{{ tgl($v->registration_start_date) ?: 'Sekarang' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-on-surface-variant">
                                        <span>Batas Akhir:</span>
                                        <span class="font-bold text-error">{{ tgl($v->registration_deadline) ?: 'Sampai kuota terpenuhi' }}</span>
                                    </div>
                                </div>

                                @if ($sisaTeks)
                                    <div class="bg-[#46166c] text-white p-4 rounded-xl text-center shadow-inner">
                                        <span class="text-[11px] text-[#8cc5dd] uppercase tracking-wider font-semibold block mb-0.5">Sisa Waktu Pendaftaran:</span>
                                        <div class="font-headline-md text-xl font-bold tracking-tight text-white">{{ $sisaTeks }}</div>
                                        @if ($persenBerjalan !== null)
                                            <div class="mt-3 w-full bg-white/20 h-2.5 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full" style="width: {{ $persenBerjalan }}%; background: linear-gradient(to right, #1a81c2, #8cc5dd);"></div>
                                            </div>
                                            <span class="text-[10px] text-white/70 block text-right mt-1">{{ $persenBerjalan }}% masa pendaftaran berjalan</span>
                                        @endif
                                    </div>
                                @elseif (! $dibuka)
                                    <div class="bg-slate-100 text-slate-700 p-4 rounded-xl text-center font-label-md font-semibold">
                                        {{ $akanDibuka ? 'Pendaftaran dibuka mulai ' . tgl($v->registration_start_date) : 'Pendaftaran telah ditutup' }}
                                    </div>
                                @endif

                                @if ($dibuka)
                                    <a href="{{ route('mahasiswa.pendaftaran.create', $v->slug) }}"
                                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-[#003580] hover:bg-[#00173d] text-white font-label-md text-label-md font-bold transition-all shadow-md no-underline">
                                        <i class="bi {{ session()->has('cis_user') ? 'bi-pencil-square' : 'bi-box-arrow-in-right' }} text-lg"></i>
                                        <span>{{ session()->has('cis_user') ? 'Daftar Sekarang' : 'Login untuk Mendaftar' }}</span>
                                    </a>
                                @else
                                    <button disabled class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-lg bg-slate-200 text-slate-500 font-label-md text-label-md font-bold cursor-not-allowed">
                                        <span>Pendaftaran Ditutup</span>
                                    </button>
                                @endif

                                <p class="text-[11px] text-center text-on-surface-variant leading-snug">
                                    Pendaftaran internal terintegrasi menggunakan akun CIS mahasiswa IT Del. Seleksi dilakukan oleh mitra.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- KARTU TENTANG MITRA (Dinamis dari tabel partners) --}}
                    @if ($mitra)
                        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm space-y-4">
                            <h2 class="font-headline-sm text-headline-sm font-bold text-[#003580] border-b border-outline-variant/30 pb-2.5 flex items-center gap-2">
                                <i class="bi bi-building text-[#1a81c2]"></i>
                                <span>Tentang Mitra</span>
                            </h2>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-2xl text-[#1a81c2] shrink-0">
                                        <i class="bi bi-buildings-fill"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-label-lg text-label-lg text-primary font-bold leading-tight">{{ $mitra->name }}</h3>
                                        @if ($mitra->field)
                                            <span class="text-[12px] text-on-surface-variant">{{ $mitra->field }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($mitra->address || $mitra->website)
                                    <div class="space-y-2 text-body-sm text-body-sm text-on-surface-variant border-y border-outline-variant/30 py-3">
                                        @if ($mitra->address)
                                            <div class="flex items-start gap-2">
                                                <i class="bi bi-geo-alt-fill text-[#46166c] mt-0.5 shrink-0"></i>
                                                <span>{{ $mitra->address }}</span>
                                            </div>
                                        @endif
                                        @if ($mitra->website)
                                            @php
                                                $webUrl = str_starts_with($mitra->website, 'http') ? $mitra->website : 'https://' . $mitra->website;
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <i class="bi bi-globe-americas text-[#1a81c2] shrink-0"></i>
                                                <a class="text-[#1a81c2] hover:underline font-medium break-all" href="{{ $webUrl }}" target="_blank" rel="noopener">{{ $mitra->website }}</a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($mitra->cooperation_status === 'cooperating')
                                    <div class="p-3 rounded-lg bg-emerald-700/10 text-emerald-700 flex items-center gap-2 font-label-sm text-label-sm font-semibold">
                                        <i class="bi bi-shield-check text-lg shrink-0"></i>
                                        <span>Mitra Resmi Bekerja Sama</span>
                                    </div>
                                @elseif ($mitra->cooperation_status === 'in_process')
                                    <div class="p-3 rounded-lg bg-[#664780]/10 text-[#46166c] flex items-center gap-2 font-label-sm text-label-sm font-semibold">
                                        <i class="bi bi-hourglass-split text-lg shrink-0"></i>
                                        <span>Kerja Sama Dalam Proses</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- 3. SECTION LOWONGAN LAINNYA (Dinamis dari database) --}}
    @if ($lowonganLainnya->isNotEmpty())
        <section class="w-full bg-[#dce3eb] py-space-xl border-t border-outline-variant/30">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
                <div class="text-center max-w-2xl mx-auto mb-10 flex flex-col items-center">
                    <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">REKOMENDASI PROGRAM</span>
                    <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Lowongan PDLK Terkait Lainnya</h2>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                        <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-2">Jelajahi kesempatan magang dan riset terapan lainnya yang tersedia.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-10">
                    @foreach ($lowonganLainnya as $lain)
                        @php $bukaLain = lowongan_dibuka($lain); @endphp
                        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col justify-between transition-all hover:shadow-md relative">
                            <div class="h-1 w-full" style="background: linear-gradient(to right, #1a81c2, #46166c);"></div>
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center text-primary font-bold text-lg shrink-0 overflow-hidden">
                                            @if ($lain->cover_image)
                                                <img src="{{ file_url($lain->cover_image) }}" alt="{{ $lain->partner_name }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="bi bi-building text-2xl text-[#1a81c2]"></i>
                                            @endif
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Mitra</span>
                                            <span class="font-label-md text-label-md text-primary leading-tight font-semibold truncate">{{ $lain->partner_name }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-white font-label-sm text-[11px] font-bold tracking-wide uppercase shrink-0 shadow-sm {{ $bukaLain ? 'bg-emerald-700' : 'bg-slate-500' }}">
                                        {{ $bukaLain ? 'Dibuka' : 'Ditutup' }}
                                    </span>
                                </div>

                                <h3 class="font-headline-sm text-headline-sm text-primary mb-3 font-bold">{{ $lain->title }}</h3>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-2.5 py-1 rounded bg-[#46166c] text-white font-label-sm text-label-sm">{{ $lain->activity_name }}</span>
                                    <span class="px-2.5 py-1 rounded text-white font-label-sm text-label-sm {{ $lain->registration_type === 'external' ? 'bg-[#664780]' : 'bg-[#0055A5]' }}">
                                        {{ $lain->registration_type === 'external' ? 'Eksternal' : 'Internal' }}
                                    </span>
                                </div>
                                <div class="space-y-2 py-3 border-y border-outline-variant/30 text-on-surface-variant font-body-sm text-body-sm">
                                    @if ($lain->location)
                                        <div class="flex items-center gap-2"><i class="bi bi-geo-alt text-[#46166c]"></i><span>{{ $lain->location }}</span></div>
                                    @endif
                                    @if ($lain->quota)
                                        <div class="flex items-center gap-2"><i class="bi bi-people text-[#46166c]"></i><span>Kuota: {{ $lain->quota }} Mahasiswa</span></div>
                                    @endif
                                    @if ($lain->period_name)
                                        <div class="flex items-center gap-2"><i class="bi bi-calendar text-[#46166c]"></i><span>{{ $lain->period_name }}</span></div>
                                    @endif
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                @if ($lain->registration_deadline)
                                    <div class="flex items-center justify-between font-label-sm text-label-sm text-on-surface-variant mb-3">
                                        <span>Batas pendaftaran:</span>
                                        <span class="font-semibold text-primary">{{ tgl($lain->registration_deadline) }}</span>
                                    </div>
                                @endif
                                <a class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded bg-primary text-white font-label-md text-label-md hover:bg-primary-container transition-colors no-underline"
                                   href="{{ route('lowongan.show', $lain->slug) }}">
                                    <span>Lihat Detail Lowongan</span>
                                    <i class="bi bi-arrow-right-short text-lg"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center">
                    <a class="inline-flex items-center gap-2 px-8 py-3.5 rounded-lg bg-[#003580] hover:bg-[#002155] text-white font-label-lg text-label-lg font-semibold shadow-md transition-colors mx-auto no-underline"
                       href="{{ route('lowongan.index') }}">
                        <i class="bi bi-briefcase"></i>
                        <span>Lihat Semua Lowongan</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

</div>
@endsection

@else
{{-- TAMPILAN PORTAL (SETELAH LOGIN, MEMAKAI layouts.pdlk) --}}
@section('content')
<div class="p-stack">
    @include('partials.flash')
    <div>
        <a href="{{ route('portal.lowongan.index') }}" class="p-btn p-btn-light p-btn-sm">← Semua lowongan</a>
        <span class="p-muted" style="margin-left:8px">{{ $v->partner_name }}@if ($v->location) · {{ $v->location }}@endif</span>
    </div>

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
@endif
