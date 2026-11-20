@extends($portal ? 'layouts.pdlk' : 'layouts.publik')

@section('title', 'Lowongan PDLK' . ($portal ? ' | Monitoring PDLK' : ''))
@section('breadcrumb', 'Kegiatan PDLK / Lowongan')
@section('page-title', 'Lowongan PDLK')

@if (! $portal)
@push('styles')
<style>
    .card-top-gradient {
        height: 4px;
        background: linear-gradient(90deg, #1a81c2 0%, #46166c 100%);
    }
    .step-connector::after {
        content: '';
        position: absolute;
        top: 2rem;
        left: 50%;
        width: 100%;
        height: 2px;
        border-top: 2px dashed #8cc5dd;
        z-index: 1;
    }
    @media (max-width: 768px) {
        .step-connector::after { display: none; }
    }
</style>
@endpush

@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative bg-cover bg-center overflow-hidden min-h-[220px] flex items-center"
             style="background-image: url('{{ asset('images/kampus-del.jpg') }}');">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(0, 53, 128, 0.90) 0%, rgba(0, 85, 165, 0.85) 100%);"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 md:px-8 w-full py-10 pb-16">
            <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#46166c] text-[#8cc5dd] text-[11px] font-semibold tracking-wider uppercase mb-2 shadow-sm">
                <i class="bi bi-stars"></i>
                <span>KESEMPATAN</span>
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight leading-tight">Lowongan PDLK</h1>
            <p class="text-sky-100 text-sm md:text-base mt-1.5 font-light max-w-2xl">
                Temukan kesempatan magang, riset, dan kegiatan PDLK lainnya bersama mitra terbaik. Baca deskripsi dan kriteria sebelum mendaftar.
            </p>
            <nav class="flex items-center space-x-2 text-xs text-[#8cc5dd] mt-3 font-medium">
                <a class="hover:underline flex items-center gap-1 text-[#8cc5dd]" href="{{ route('beranda') }}">
                    <i class="bi bi-house-door"></i> Beranda
                </a>
                <span>/</span>
                <span class="text-white">Lowongan PDLK</span>
            </nav>
        </div>

        <div class="absolute bottom-0 left-0 right-0 pointer-events-none w-full overflow-hidden leading-none z-10 -mb-px">
            <svg class="relative block w-full h-8 text-[#dce3eb] fill-current" preserveAspectRatio="none" viewBox="0 0 1200 120">
                <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,30 L1200,120 L0,120 Z"></path>
            </svg>
        </div>
    </section>

    {{-- 2. KOTAK PENCARIAN & FILTER --}}
    <section class="max-w-7xl mx-auto px-4 md:px-8 -mt-10 relative z-20 w-full">
        <div class="bg-white rounded-2xl shadow-xl p-5 md:p-6 border border-slate-100">
            <form action="{{ route('lowongan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end" method="GET">
                @if (request('urut'))
                    <input type="hidden" name="urut" value="{{ request('urut') }}">
                @endif

                <div class="md:col-span-4">
                    <label for="q" class="block text-xs font-semibold text-gray-700 mb-1.5">Pencarian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="bi bi-search text-sm"></i>
                        </span>
                        <input id="q" name="q" value="{{ $q }}"
                               class="w-full pl-9 pr-3 py-2 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a81c2] focus:border-[#1a81c2] placeholder-gray-400"
                               placeholder="Cari posisi, judul, atau nama mitra..." type="text">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="jenis" class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Kegiatan</label>
                    <select id="jenis" name="jenis" class="w-full py-2 px-2.5 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a81c2] focus:border-[#1a81c2] text-gray-700">
                        <option value="">Semua Kegiatan</option>
                        @foreach ($jenis as $j)
                            <option value="{{ $j->slug }}" @selected(request('jenis') === $j->slug)>{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="periode" class="block text-xs font-semibold text-gray-700 mb-1.5">Periode</label>
                    <select id="periode" name="periode" class="w-full py-2 px-2.5 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a81c2] focus:border-[#1a81c2] text-gray-700">
                        <option value="">Semua Periode</option>
                        @foreach ($periode as $pr)
                            <option value="{{ $pr->id }}" @selected((string) request('periode') === (string) $pr->id)>{{ $pr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="pendaftaran" class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Pendaftaran</label>
                    <select id="pendaftaran" name="pendaftaran" class="w-full py-2 px-2.5 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a81c2] focus:border-[#1a81c2] text-gray-700">
                        <option value="">Semua Jenis</option>
                        <option value="internal" @selected(request('pendaftaran') === 'internal')>Internal</option>
                        <option value="external" @selected(request('pendaftaran') === 'external')>Eksternal</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
                    <select id="status" name="status" class="w-full py-2 px-2.5 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a81c2] focus:border-[#1a81c2] text-gray-700">
                        <option value="">Semua Status</option>
                        <option value="dibuka" @selected(request('status') === 'dibuka')>Dibuka</option>
                        <option value="segera_ditutup" @selected(request('status') === 'segera_ditutup')>Segera Ditutup</option>
                        <option value="akan_dibuka" @selected(request('status') === 'akan_dibuka')>Akan Dibuka</option>
                        <option value="ditutup" @selected(request('status') === 'ditutup')>Ditutup</option>
                    </select>
                </div>

                {{-- Filter Cepat Pills & Tombol Cari --}}
                <div class="md:col-span-12 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-gray-100 mt-2">
                    <div class="flex items-center flex-wrap gap-1.5 py-1">
                        <span class="text-xs font-semibold text-gray-500 mr-1 flex items-center gap-1">
                            <i class="bi bi-funnel"></i> Filter Cepat:
                        </span>
                        <a href="{{ route('lowongan.index', array_filter(request()->except(['jenis', 'page']))) }}"
                           class="px-3 py-1 rounded-full text-xs font-medium transition no-underline {{ !request('jenis') ? 'bg-[#1a81c2] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-slate-200' }}">
                            Semua
                        </a>
                        @foreach ($jenis as $j)
                            <a href="{{ route('lowongan.index', array_merge(request()->except('page'), ['jenis' => $j->slug])) }}"
                               class="px-3 py-1 rounded-full text-xs font-medium transition no-underline {{ request('jenis') === $j->slug ? 'bg-[#1a81c2] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-slate-200' }}">
                                {{ $j->name }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 shrink-0 self-end sm:self-center">
                        <a class="text-xs font-medium text-[#664780] hover:text-[#46166c] underline" href="{{ route('lowongan.index') }}">
                            Reset
                        </a>
                        <button class="inline-flex items-center gap-2 bg-[#003580] hover:bg-[#0055A5] text-white font-medium text-xs md:text-sm px-5 py-2 rounded-lg transition duration-200 shadow-sm" type="submit">
                            <i class="bi bi-search"></i>
                            <span>Cari</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- 3. DAFTAR LOWONGAN --}}
    <section class="bg-[#dce3eb] py-10 mt-6">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 gap-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg md:text-xl font-bold text-[#003580]">Daftar Lowongan</h2>
                    <span class="bg-[#003580] text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $lowongan->total() }} Lowongan
                    </span>
                </div>

                <form method="GET" action="{{ route('lowongan.index') }}" class="flex items-center gap-2 text-xs">
                    @foreach (request()->except(['urut', 'page']) as $key => $val)
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <label for="urut" class="text-gray-600 font-medium">Urutkan:</label>
                    <select id="urut" name="urut" onchange="this.form.submit()"
                            class="bg-white border border-gray-300 text-gray-700 text-xs rounded-md px-2.5 py-1.5 focus:ring-[#1a81c2] focus:border-[#1a81c2] font-medium">
                        <option value="terbaru" @selected(request('urut', 'terbaru') === 'terbaru')>Terbaru Dipublikasi</option>
                        <option value="terdekat" @selected(request('urut') === 'terdekat')>Batas Daftar Terdekat</option>
                        <option value="kuota" @selected(request('urut') === 'kuota')>Kuota Terbanyak</option>
                    </select>
                </form>
            </div>

            @if ($lowongan->isEmpty())
                <div class="bg-white rounded-xl p-10 text-center text-gray-500 shadow-sm">
                    <i class="bi bi-briefcase text-4xl text-gray-300 block mb-2"></i>
                    <p class="font-medium text-gray-700">Tidak ada lowongan yang sesuai dengan pencarian Anda.</p>
                    <a href="{{ route('lowongan.index') }}" class="inline-block mt-3 text-xs font-semibold text-[#1a81c2] hover:underline">Tampilkan semua lowongan</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($lowongan as $item)
                        @php
                            $dibuka = lowongan_dibuka($item);
                            $sisaHari = $item->registration_deadline
                                ? \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->registration_deadline)->startOfDay(), false)
                                : null;
                            $akanDibuka = !$dibuka && !$item->is_closed && $item->registration_start_date
                                && \Carbon\Carbon::parse($item->registration_start_date)->isFuture();
                            $segeraTutup = $dibuka && $sisaHari !== null && $sisaHari >= 0 && $sisaHari <= 7;
                        @endphp

                        <article class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden border border-slate-200/80 group">
                            <div class="card-top-gradient"></div>
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-[#1a81c2] text-lg shrink-0 overflow-hidden">
                                            @if ($item->cover_image)
                                                <img src="{{ file_url($item->cover_image) }}" alt="{{ $item->partner_name }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="bi bi-building"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-[10px] text-gray-400 block font-normal leading-tight">Mitra</span>
                                            <span class="text-xs font-semibold text-gray-800 leading-tight block truncate">{{ $item->partner_name }}</span>
                                        </div>
                                    </div>

                                    @if ($segeraTutup)
                                        <span class="bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0">Segera Ditutup</span>
                                    @elseif ($dibuka)
                                        <span class="bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0">Dibuka</span>
                                    @elseif ($akanDibuka)
                                        <span class="bg-[#0055A5] text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0">Akan Dibuka</span>
                                    @else
                                        <span class="bg-slate-500 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0">Ditutup</span>
                                    @endif
                                </div>

                                <h3 class="text-base font-bold text-[#003580] mb-2.5 group-hover:text-[#1a81c2] transition-colors leading-snug">
                                    {{ $item->title }}
                                </h3>

                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    <span class="bg-[#46166c] text-white text-[11px] font-medium px-2 py-0.5 rounded">{{ $item->activity_name }}</span>
                                    <span class="text-white text-[11px] font-medium px-2 py-0.5 rounded {{ $item->registration_type === 'external' ? 'bg-[#664780]' : 'bg-[#0055A5]' }}">
                                        {{ $item->registration_type === 'external' ? 'Eksternal' : 'Internal' }}
                                    </span>
                                </div>

                                <div class="space-y-1.5 text-xs text-gray-600 mb-5 border-t border-gray-100 pt-3">
                                    @if ($item->location)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-geo-alt text-[#1a81c2]"></i>
                                            <span>{{ $item->location }}</span>
                                        </div>
                                    @endif
                                    @if ($item->quota)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-people text-[#1a81c2]"></i>
                                            <span>Kuota {{ $item->quota }} Mahasiswa</span>
                                        </div>
                                    @endif
                                    @if ($item->period_name)
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-calendar-event text-[#1a81c2]"></i>
                                            <span>{{ $item->period_name }}</span>
                                        </div>
                                    @endif
                                    @if ($akanDibuka && $item->registration_start_date)
                                        <div class="flex items-center gap-2 text-[#0055A5] font-medium">
                                            <i class="bi bi-clock"></i>
                                            <span>Dibuka: {{ tgl($item->registration_start_date) }}</span>
                                        </div>
                                    @elseif ($item->registration_deadline)
                                        <div class="flex items-center gap-2 {{ $segeraTutup ? 'text-rose-600 font-medium' : '' }}">
                                            <i class="bi {{ $segeraTutup ? 'bi-clock-history' : 'bi-clock text-[#1a81c2]' }}"></i>
                                            <span class="{{ $segeraTutup ? '' : 'font-medium text-gray-700' }}">
                                                Batas: {{ tgl($item->registration_deadline) }}{{ $segeraTutup ? ' (Segera)' : '' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    <a class="w-full inline-flex justify-center items-center gap-1.5 bg-[#003580] hover:bg-[#0055A5] text-white text-xs font-semibold py-2.5 px-4 rounded-lg transition duration-200"
                                       href="{{ route('lowongan.show', $item->slug) }}">
                                        <span>Lihat Detail Lowongan</span>
                                        <i class="bi bi-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Paginasi --}}
                @if ($lowongan->lastPage() > 1)
                    @php $paginator = $lowongan->withQueryString(); @endphp
                    <div class="mt-10 flex justify-center items-center gap-1.5 text-xs font-medium">
                        @if ($paginator->onFirstPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white/60 text-gray-300 cursor-not-allowed">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition" title="Sebelumnya">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        @endif

                        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                            @if ($page === $paginator->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#1a81c2] text-white font-semibold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $paginator->url($page) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition" title="Berikutnya">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @else
                            <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-white/60 text-gray-300 cursor-not-allowed">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- 4. ALUR PENDAFTARAN LOWONGAN INTERNAL --}}
    <section class="bg-white py-14">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#46166c]">CARA MENDAFTAR</span>
                <h2 class="text-2xl md:text-3xl font-bold text-[#003580] mt-1">Alur Pendaftaran Lowongan Internal</h2>
                <div class="flex justify-center items-center mt-2.5 space-x-1">
                    <div class="h-1 w-10 bg-[#1a81c2] rounded-full"></div>
                    <div class="h-1 w-5 bg-[#46166c] rounded-full"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <div class="text-center flex flex-col items-center relative step-connector">
                    <div class="relative z-10 w-16 h-16 rounded-full bg-[#1a81c2] text-white flex items-center justify-center shadow-md mb-4 transition transform hover:scale-105">
                        <i class="bi bi-box-arrow-in-right text-2xl"></i>
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-white text-[#1a81c2] font-bold text-xs flex items-center justify-center border-2 border-[#1a81c2]">1</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#003580] mb-1.5">Login Akun CIS</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs">Gunakan akun Centralized Information System (CIS) IT Del Anda untuk masuk ke portal.</p>
                </div>

                <div class="text-center flex flex-col items-center relative step-connector">
                    <div class="relative z-10 w-16 h-16 rounded-full bg-[#46166c] text-white flex items-center justify-center shadow-md mb-4 transition transform hover:scale-105">
                        <i class="bi bi-file-earmark-text text-2xl"></i>
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-white text-[#46166c] font-bold text-xs flex items-center justify-center border-2 border-[#46166c]">2</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#003580] mb-1.5">Pilih Lowongan &amp; Formulir</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs">Baca kriteria lowongan yang dibuka, lalu lengkapi formulir pendaftaran secara lengkap.</p>
                </div>

                <div class="text-center flex flex-col items-center relative step-connector">
                    <div class="relative z-10 w-16 h-16 rounded-full bg-[#1a81c2] text-white flex items-center justify-center shadow-md mb-4 transition transform hover:scale-105">
                        <i class="bi bi-file-earmark-pdf text-2xl"></i>
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-white text-[#1a81c2] font-bold text-xs flex items-center justify-center border-2 border-[#1a81c2]">3</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#003580] mb-1.5">Unggah Dokumen (1 PDF)</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs">Satukan CV, portofolio, surat keterangan, dan transkrip/KHS dalam satu berkas PDF.</p>
                </div>

                <div class="text-center flex flex-col items-center relative">
                    <div class="relative z-10 w-16 h-16 rounded-full bg-[#46166c] text-white flex items-center justify-center shadow-md mb-4 transition transform hover:scale-105">
                        <i class="bi bi-check2-circle text-2xl"></i>
                        <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-white text-[#46166c] font-bold text-xs flex items-center justify-center border-2 border-[#46166c]">4</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#003580] mb-1.5">Pantau Seleksi</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs">Pantau hasil seleksi pada dashboard dan lengkapi data kegiatan setelah dinyatakan lulus.</p>
                </div>
            </div>

            <div class="mt-10 max-w-3xl mx-auto bg-sky-50 border border-sky-200 text-sky-900 rounded-xl p-3.5 flex items-start gap-3 text-xs leading-relaxed">
                <i class="bi bi-info-circle-fill text-[#1a81c2] text-base shrink-0 mt-0.5"></i>
                <div>
                    <span class="font-semibold text-[#003580]">Catatan:</span> Lowongan eksternal didaftarkan langsung melalui situs resmi mitra terkait. Mahasiswa yang dinyatakan lulus kegiatan tetap wajib melengkapi pendataan melalui sistem PDLK IT Del.
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@else
{{-- TAMPILAN PORTAL (SETELAH LOGIN, MEMAKAI layouts.pdlk) --}}
@section('content')
<div class="p-stack">
    @include('partials.flash')
    <p class="p-muted" style="margin:0">Baca deskripsi dan kriteria setiap lowongan sebelum memutuskan untuk mendaftar.</p>

    <form method="GET" class="p-toolbar">
        <div class="p-field">
            <label for="q">Cari</label>
            <input id="q" name="q" class="p-input" value="{{ $q }}" placeholder="Judul, posisi, atau nama mitra">
        </div>
        <div class="p-field">
            <label for="jenis">Bentuk kegiatan</label>
            <select id="jenis" name="jenis" class="p-select">
                <option value="">Semua bentuk</option>
                @foreach ($jenis as $j)
                    <option value="{{ $j->slug }}" @selected(request('jenis') === $j->slug)>{{ $j->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="p-btn p-btn-primary">Terapkan</button>
    </form>

    @if ($lowongan->isEmpty())
        <div class="p-card p-empty">Tidak ada lowongan yang sesuai.</div>
    @else
        <div class="p-grid-cards">
            @foreach ($lowongan as $item)
                @include('partials.kartu-lowongan', ['item' => $item, 'portal' => $portal])
            @endforeach
        </div>
        @include('partials.paginasi', ['paginator' => $lowongan])
    @endif
</div>
@endsection
@endif
