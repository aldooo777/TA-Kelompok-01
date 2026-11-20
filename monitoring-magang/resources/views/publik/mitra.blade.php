@extends('layouts.publik')

@section('title', 'Mitra PDLK')

@section('fullwidth')
@php
    $q = $q ?? '';
    $bidang = $bidang ?? '';
    $status = $status ?? 'all';
    $stat = $stat ?? (object) ['total' => 0, 'cooperating' => 0, 'in_process' => 0, 'jumlah_bidang' => 0];
    $bidangList = $bidangList ?? collect();
    $lowonganPerMitra = $lowonganPerMitra ?? collect();
@endphp

<div class="flex flex-col w-full">

    {{-- 1. BANNER UTAMA --}}
    <section class="relative w-full bg-gradient-to-r from-primary-container via-secondary to-[#0055A5] text-on-primary overflow-hidden">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin pt-10 pb-16 relative z-10 flex flex-col items-start gap-3">
            <nav class="flex items-center gap-2 font-label-sm text-label-sm text-[#8cc5dd] mb-1">
                <a class="hover:underline flex items-center gap-1 text-white/90" href="{{ route('beranda') }}">
                    <i class="bi bi-house-door"></i> Beranda
                </a>
                <span class="text-white/50">/</span>
                <span class="font-bold text-[#8cc5dd]">Mitra</span>
            </nav>

            <span class="inline-flex items-center px-3 py-1 rounded bg-[#46166c] text-white font-label-sm text-label-sm tracking-wider uppercase font-semibold shadow-sm">
                KOLABORASI
            </span>

            <h1 class="font-headline-xl text-headline-xl md:text-[40px] md:leading-[48px] font-bold text-white tracking-tight">
                Mitra PDLK
            </h1>
            <p class="font-body-lg text-body-lg text-white/85 max-w-3xl leading-relaxed">
                Sinergi strategis antara UPT PP-ESTEM Institut Teknologi Del bersama dunia industri, BUMN, lembaga riset, dan instansi pemerintah.
            </p>
        </div>

        <div class="w-full overflow-hidden leading-none absolute bottom-0 left-0 right-0 z-0 -mb-px">
            <svg class="w-full h-8 block text-surface-container-lowest" fill="none" preserveAspectRatio="none" viewBox="0 0 1440 32" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,16 C320,32 540,4 720,18 C900,32 1120,6 1440,20 L1440,32 L0,32 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    {{-- 2. RINGKASAN STATISTIK (Dinamis dari database) --}}
    <section class="w-full bg-surface-container-lowest py-10">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="w-full rounded-2xl bg-gradient-to-r from-[#46166c] to-[#664780] text-white p-8 md:p-10 shadow-md relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 md:gap-0 relative z-10">
                    <div class="flex flex-col items-center text-center px-4 md:border-r border-white/20">
                        <span class="font-display-lg text-display-lg font-bold tracking-tight text-white mb-1">{{ $stat->total ?? 0 }}</span>
                        <span class="font-headline-sm text-headline-sm font-semibold text-white">Total Mitra</span>
                        <span class="font-body-sm text-body-sm text-white/80 mt-1">Institusi &amp; Korporasi Terdaftar</span>
                    </div>

                    <div class="flex flex-col items-center text-center px-4 lg:border-r border-white/20">
                        <span class="font-display-lg text-display-lg font-bold tracking-tight text-white mb-1">{{ $stat->cooperating ?? 0 }}</span>
                        <span class="font-headline-sm text-headline-sm font-semibold text-white">Bekerja Sama</span>
                        <span class="font-body-sm text-body-sm text-white/80 mt-1">Kerja Sama Aktif</span>
                    </div>

                    <div class="flex flex-col items-center text-center px-4 md:border-r border-white/20">
                        <span class="font-display-lg text-display-lg font-bold tracking-tight text-white mb-1">{{ $stat->in_process ?? 0 }}</span>
                        <span class="font-headline-sm text-headline-sm font-semibold text-white">Dalam Proses</span>
                        <span class="font-body-sm text-body-sm text-white/80 mt-1">Tahap Pengajuan Kerja Sama</span>
                    </div>

                    <div class="flex flex-col items-center text-center px-4">
                        <span class="font-display-lg text-display-lg font-bold tracking-tight text-white mb-1">{{ $stat->jumlah_bidang ?? 0 }}</span>
                        <span class="font-headline-sm text-headline-sm font-semibold text-white">Bidang Usaha</span>
                        <span class="font-body-sm text-body-sm text-white/80 mt-1">Klaster Industri &amp; Riset</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. FILTER & PENCARIAN --}}
    <section class="w-full bg-surface-container-lowest pb-6">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <form method="GET" action="{{ route('mitra') }}"
                  class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-surface-container-low p-4 rounded-xl">
                @if ($status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto flex-1">
                    <div class="relative w-full lg:w-80">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-outline"></i>
                        <input name="q" value="{{ $q }}"
                               class="w-full bg-surface-container-lowest pl-10 pr-4 py-2.5 rounded-lg text-on-surface text-body-md border-0 focus:ring-2 focus:ring-secondary placeholder:text-outline/70 shadow-sm"
                               placeholder="Cari nama atau lokasi mitra..." type="text">
                    </div>

                    <div class="w-full lg:w-64">
                        <select name="bidang" onchange="this.form.submit()"
                                class="w-full bg-surface-container-lowest px-4 py-2.5 rounded-lg text-on-surface text-body-md border-0 focus:ring-2 focus:ring-secondary shadow-sm">
                            <option value="">Semua Bidang Usaha</option>
                            @foreach ($bidangList as $b)
                                <option value="{{ $b->field }}" @selected($bidang === $b->field)>{{ $b->field }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="px-5 py-2.5 rounded-lg bg-[#003580] hover:bg-[#0055A5] text-white font-label-md text-label-md font-semibold transition shadow-sm shrink-0">
                        Cari
                    </button>
                </div>

                {{-- Filter Status Tab Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
                    <a href="{{ route('mitra', array_filter(['q' => $q, 'bidang' => $bidang])) }}"
                       class="px-5 py-2 rounded-full font-label-md text-label-md font-semibold transition shadow-sm whitespace-nowrap no-underline {{ $status === 'all' ? 'bg-[#1a81c2] text-white' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container' }}">
                        Semua
                    </a>
                    <a href="{{ route('mitra', array_filter(['status' => 'cooperating', 'q' => $q, 'bidang' => $bidang])) }}"
                       class="px-5 py-2 rounded-full font-label-md text-label-md font-semibold transition shadow-sm whitespace-nowrap no-underline {{ $status === 'cooperating' ? 'bg-[#1a81c2] text-white' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container' }}">
                        Bekerja Sama
                    </a>
                    <a href="{{ route('mitra', array_filter(['status' => 'in_process', 'q' => $q, 'bidang' => $bidang])) }}"
                       class="px-5 py-2 rounded-full font-label-md text-label-md font-semibold transition shadow-sm whitespace-nowrap no-underline {{ $status === 'in_process' ? 'bg-[#1a81c2] text-white' : 'bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container' }}">
                        Dalam Proses
                    </a>
                </div>
            </form>
        </div>
    </section>

    {{-- 4. DAFTAR MITRA --}}
    <section class="w-full bg-[#8cc5dd]/15 py-12">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div class="flex flex-col items-start">
                    <span class="font-label-sm text-label-sm font-bold uppercase text-[#46166c] tracking-widest mb-1">
                        JARINGAN KEMITRAAN DUDI
                    </span>
                    <h2 class="font-headline-lg text-headline-lg font-bold text-primary-container">
                        Daftar Mitra Terverifikasi &amp; Kolaborator Aktif
                    </h2>
                    <div class="flex items-center gap-1 mt-2.5">
                        <div class="w-10 h-1 bg-[#1a81c2] rounded-full"></div>
                        <div class="w-5 h-1 bg-[#46166c] rounded-full"></div>
                    </div>
                </div>
                @if ($q !== '' || $bidang !== '' || $status !== 'all')
                    <a href="{{ route('mitra') }}" class="text-xs font-semibold text-[#46166c] hover:underline">Reset Filter</a>
                @endif
            </div>

            @if ($mitra->isEmpty())
                <div class="bg-surface-container-lowest rounded-xl p-10 text-center text-on-surface-variant shadow-sm">
                    <i class="bi bi-buildings text-4xl text-outline/40 block mb-2"></i>
                    <p class="font-headline-sm text-headline-sm text-primary">Belum ada data mitra yang sesuai</p>
                    <p class="font-body-sm text-body-sm mt-1">Silakan ubah kata kunci pencarian atau filter status kerja sama.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($mitra as $m)
                        @php
                            $isGanjil = $loop->iteration % 2 === 1;
                            $borderWarna = $isGanjil ? '#1a81c2' : '#46166c';
                            $statusText = match ($m->cooperation_status) {
                                'cooperating' => 'Bekerja Sama',
                                'in_process' => 'Dalam Proses',
                                'non_cooperating' => 'Belum Bekerja Sama',
                                default => 'Terdaftar',
                            };
                            $badgeBg = match ($m->cooperation_status) {
                                'cooperating' => 'bg-emerald-50 text-emerald-700',
                                'in_process' => 'bg-purple-50 text-[#664780]',
                                default => 'bg-slate-100 text-slate-600',
                            };
                            $dotBg = match ($m->cooperation_status) {
                                'cooperating' => 'bg-emerald-500',
                                'in_process' => 'bg-[#664780]',
                                default => 'bg-slate-400',
                            };
                            $webUrl = $m->website
                                ? (str_starts_with($m->website, 'http') ? $m->website : 'https://' . $m->website)
                                : '';
                            $vacanciesJson = ($lowonganPerMitra->get($m->id) ?? collect())->map(fn ($v) => [
                                'title' => $v->title,
                                'activity' => $v->activity_name,
                                'location' => $v->location,
                                'url' => route('lowongan.show', $v->slug),
                            ])->values();
                        @endphp

                        <div class="partner-card bg-surface-container-lowest rounded-xl p-5 shadow-sm hover:shadow-md transition cursor-pointer relative group flex flex-col justify-between border-b-[3px]"
                             style="border-bottom-color: {{ $borderWarna }};"
                             data-title="{{ $m->name }}"
                             data-sector="{{ $m->field ?: 'Mitra PDLK' }}"
                             data-location="{{ $m->address ?: '-' }}"
                             data-website="{{ $webUrl }}"
                             data-status="{{ $m->cooperation_status }}"
                             data-status-label="{{ $statusText }}"
                             data-vacancies='@json($vacanciesJson)'
                             onclick="openPartnerModal(this)">
                            <div>
                                <div class="flex justify-end mb-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-label-sm text-label-sm font-medium {{ $badgeBg }}">
                                        <span class="w-2 h-2 rounded-full {{ $dotBg }}"></span> {{ $statusText }}
                                    </span>
                                </div>

                                <div class="rounded-lg bg-surface-container-low flex items-center justify-center p-4 h-24 mb-4 group-hover:bg-[#1a81c2]/10 transition">
                                    <div class="flex items-center gap-2 text-primary font-headline-sm text-headline-sm font-bold opacity-85 group-hover:opacity-100 text-center">
                                        <i class="bi bi-buildings text-2xl shrink-0" style="color: {{ $borderWarna }};"></i>
                                        <span class="line-clamp-2 text-sm">{{ $m->name }}</span>
                                    </div>
                                </div>

                                <span class="block font-label-sm text-label-sm text-outline font-medium uppercase tracking-wider mb-1 truncate">
                                    {{ $m->field ?: 'Mitra Kolaborasi' }}
                                </span>
                                <h3 class="font-headline-sm text-headline-sm font-bold text-primary-container group-hover:text-secondary transition line-clamp-2 mb-2">
                                    {{ $m->name }}
                                </h3>
                                @if ($m->address)
                                    <div class="flex items-start gap-1.5 text-on-surface-variant font-body-sm text-body-sm mb-4">
                                        <i class="bi bi-geo-alt text-[#1a81c2] mt-0.5 shrink-0"></i>
                                        <span class="line-clamp-2">{{ $m->address }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-surface-container-high flex items-center justify-between font-label-sm text-label-sm text-secondary font-semibold">
                                <span class="flex items-center gap-1.5 group-hover:text-primary transition">
                                    <i class="bi bi-info-circle"></i>
                                    {{ $vacanciesJson->isNotEmpty() ? $vacanciesJson->count() . ' Lowongan Aktif' : 'Lihat Detail Mitra' }}
                                </span>
                                <i class="bi bi-arrow-right text-[12px] opacity-70 group-hover:translate-x-1 transition"></i>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginasi --}}
                @if ($mitra->lastPage() > 1)
                    @php $paginator = $mitra->withQueryString(); @endphp
                    <div class="flex items-center justify-center gap-2 mt-12">
                        @if ($paginator->onFirstPage())
                            <span class="w-10 h-10 rounded-lg bg-surface-container-lowest/60 text-outline/40 flex items-center justify-center cursor-not-allowed">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 rounded-lg bg-surface-container-lowest text-on-surface hover:bg-surface-container transition flex items-center justify-center shadow-sm">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                            @if ($page === $paginator->currentPage())
                                <span class="w-10 h-10 rounded-lg bg-[#1a81c2] text-white font-label-md text-label-md font-bold flex items-center justify-center shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}" class="w-10 h-10 rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md hover:bg-surface-container transition flex items-center justify-center shadow-sm no-underline">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 rounded-lg bg-surface-container-lowest text-on-surface hover:bg-surface-container transition flex items-center justify-center shadow-sm">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="w-10 h-10 rounded-lg bg-surface-container-lowest/60 text-outline/40 flex items-center justify-center cursor-not-allowed">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- 5. PITA AJAKAN KERJA SAMA (CTA) --}}
    <section class="w-full bg-surface-container-lowest py-4">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="bg-gradient-to-r from-primary-container to-[#1a81c2] rounded-2xl py-10 px-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 my-10 shadow-lg relative overflow-hidden">
                <div class="flex flex-col items-start gap-2 relative z-10 max-w-2xl">
                    <span class="inline-block px-3 py-1 rounded bg-[#46166c] text-white font-label-sm text-label-sm uppercase font-semibold">
                        INISIASI KEMITRAAN BARU
                    </span>
                    <h3 class="font-headline-lg text-headline-lg font-bold text-white tracking-tight">
                        Tertarik Bermitra dengan UPT PP-ESTEM?
                    </h3>
                    <p class="font-body-md text-body-md text-white/90 leading-relaxed">
                        Hubungi kami melalui kontak di bawah untuk inisiasi kerja sama, rekrutmen peserta PDLK, riset bersama, atau program kolaborasi lainnya.
                    </p>
                </div>
                <div class="relative z-10 shrink-0">
                    <button class="bg-white text-primary-container hover:bg-surface-container font-label-lg text-label-lg font-bold px-7 py-3.5 rounded-xl shadow-md flex items-center gap-2 transition"
                            onclick="document.querySelector('footer')?.scrollIntoView({ behavior: 'smooth' })" type="button">
                        <i class="bi bi-envelope"></i>
                        <span>Hubungi Kami</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- MODAL DETAIL MITRA --}}
    <div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-primary/65 backdrop-blur-sm" id="partner-modal">
        <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-gradient-to-r from-primary-container to-[#1a81c2] text-white p-6 relative">
                <button aria-label="Tutup modal" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" onclick="closePartnerModal()" type="button">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
                <span class="inline-block text-white/80 font-label-sm text-label-sm uppercase tracking-wider mb-1" id="modal-sector">
                    Bidang Usaha
                </span>
                <h3 class="font-headline-md text-headline-md font-bold text-white pr-8" id="modal-title">
                    Nama Mitra
                </h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800" id="modal-status-badge">
                        Bekerja Sama
                    </span>
                </div>
            </div>

            <div class="p-6 overflow-y-auto space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-surface-container-low p-4 rounded-xl">
                    <div>
                        <span class="font-label-sm text-label-sm text-outline block">Alamat / Lokasi</span>
                        <span class="font-body-md text-body-md font-semibold text-on-surface flex items-start gap-1.5 mt-0.5" id="modal-location">
                            <i class="bi bi-geo-alt text-[#1a81c2] mt-1"></i>
                            <span id="modal-location-text">-</span>
                        </span>
                    </div>
                    <div>
                        <span class="font-label-sm text-label-sm text-outline block">Website Resmi</span>
                        <div id="modal-website-wrap" class="mt-0.5"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-label-lg text-label-lg font-bold text-primary-container">Lowongan Aktif dari Mitra Ini</h4>
                        <span class="font-label-sm text-label-sm text-[#46166c] font-semibold" id="modal-vacancies-count">0 Posisi Tersedia</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="modal-vacancies-list"></div>
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low flex justify-end">
                <button class="px-5 py-2 rounded-lg bg-outline/20 hover:bg-outline/30 text-on-surface font-label-md text-label-md font-semibold transition" onclick="closePartnerModal()" type="button">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function openPartnerModal(card) {
        const modal = document.getElementById('partner-modal');
        if (!modal) return;

        document.getElementById('modal-title').textContent = card.getAttribute('data-title') || '';
        document.getElementById('modal-sector').textContent = card.getAttribute('data-sector') || '';
        document.getElementById('modal-location-text').textContent = card.getAttribute('data-location') || '-';

        const webUrl = card.getAttribute('data-website') || '';
        const webWrap = document.getElementById('modal-website-wrap');
        webWrap.innerHTML = '';
        if (webUrl) {
            const a = document.createElement('a');
            a.href = webUrl;
            a.target = '_blank';
            a.rel = 'noopener';
            a.className = 'font-body-md text-body-md font-semibold text-secondary hover:underline inline-flex items-center gap-1.5 break-all';
            a.innerHTML = '<i class="bi bi-box-arrow-up-right text-[12px]"></i>';
            const span = document.createElement('span');
            span.textContent = webUrl.replace(/^https?:\/\//, '');
            a.appendChild(span);
            webWrap.appendChild(a);
        } else {
            const span = document.createElement('span');
            span.className = 'font-body-md text-body-md text-on-surface-variant';
            span.textContent = '-';
            webWrap.appendChild(span);
        }

        const status = card.getAttribute('data-status');
        const statusLabel = card.getAttribute('data-status-label') || 'Terdaftar';
        const badge = document.getElementById('modal-status-badge');
        badge.textContent = statusLabel;
        if (status === 'in_process') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-[#664780]';
        } else if (status === 'cooperating') {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700';
        }

        const listEl = document.getElementById('modal-vacancies-list');
        const countEl = document.getElementById('modal-vacancies-count');
        listEl.innerHTML = '';
        let vacancies = [];
        try {
            vacancies = JSON.parse(card.getAttribute('data-vacancies') || '[]');
        } catch (e) {}

        countEl.textContent = vacancies.length + ' Posisi Tersedia';
        if (vacancies.length === 0) {
            listEl.className = 'grid grid-cols-1';
            const empty = document.createElement('div');
            empty.className = 'p-4 rounded-lg bg-surface-container text-center text-body-sm text-on-surface-variant';
            empty.textContent = 'Saat ini belum ada lowongan aktif yang dibuka oleh mitra ini.';
            listEl.appendChild(empty);
        } else {
            listEl.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3';
            vacancies.forEach(v => {
                const item = document.createElement('div');
                item.className = 'p-3.5 rounded-lg bg-surface-container flex flex-col justify-between';

                const top = document.createElement('div');
                const tag = document.createElement('span');
                tag.className = 'inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-[#1a81c2]/10 text-secondary mb-1';
                tag.textContent = v.activity || 'PDLK';

                const title = document.createElement('p');
                title.className = 'font-label-md text-label-md font-bold text-on-surface m-0';
                title.textContent = v.title || '';

                top.appendChild(tag);
                top.appendChild(title);
                if (v.location) {
                    const loc = document.createElement('p');
                    loc.className = 'font-body-sm text-body-sm text-outline m-0 mt-0.5';
                    loc.textContent = v.location;
                    top.appendChild(loc);
                }

                const btn = document.createElement('a');
                btn.href = v.url;
                btn.className = 'mt-3 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded bg-primary-container text-white font-label-sm text-label-sm font-semibold hover:bg-primary transition no-underline';
                btn.innerHTML = '<span>Lihat Lowongan</span><i class="bi bi-chevron-right text-[10px]"></i>';

                item.appendChild(top);
                item.appendChild(btn);
                listEl.appendChild(item);
            });
        }

        modal.classList.remove('hidden');
    }

    function closePartnerModal() {
        document.getElementById('partner-modal')?.classList.add('hidden');
    }

    document.getElementById('partner-modal')?.addEventListener('click', function (e) {
        if (e.target === this) closePartnerModal();
    });
</script>
@endpush
