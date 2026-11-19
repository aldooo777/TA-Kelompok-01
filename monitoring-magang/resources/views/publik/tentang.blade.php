@extends('layouts.publik')

@section('title', 'Visi, Misi & Tujuan')

@section('fullwidth')
<div class="flex flex-col w-full">

    {{-- 1. BANNER HALAMAN --}}
    <section class="relative w-full overflow-hidden"
             style="background: linear-gradient(rgba(0, 53, 128, 0.85), rgba(0, 85, 165, 0.85)), url('{{ asset('images/kampus-del.jpg') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(rgba(255, 255, 255, 0.12) 1.5px, transparent 1.5px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin-mobile md:px-margin pt-10 pb-16 text-center flex flex-col items-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#46166c] text-white font-label-sm text-label-sm uppercase tracking-wider mb-3 font-semibold shadow-sm">
                <i class="bi bi-shield-check"></i> PROFIL
            </span>
            <h1 class="text-white font-display-lg text-display-lg tracking-tight leading-tight mb-2">Visi, Misi &amp; Tujuan</h1>
            <div class="flex items-center justify-center gap-2 font-body-sm text-body-sm text-[#8cc5dd]">
                <a class="hover:underline text-[#8cc5dd]" href="{{ route('beranda') }}">Beranda</a>
                <span>/</span>
                <span>Profil</span>
                <span>/</span>
                <span class="text-white font-semibold">Visi, Misi &amp; Tujuan</span>
            </div>
        </div>

        <div class="relative w-full overflow-hidden leading-none z-10 -mb-1">
            <svg class="w-full h-12 sm:h-16 block" preserveAspectRatio="none" viewBox="0 0 1440 120" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,32L48,42.7C96,53,192,75,288,74.7C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" fill="#8cc5dd" fill-opacity="0.5"></path>
                <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,58.7C672,64,768,96,864,96C960,96,1056,64,1152,53.3C1248,43,1344,53,1392,58.7L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z" fill="#ffffff"></path>
            </svg>
        </div>
    </section>

    {{-- 2. NAVIGASI SUB-HALAMAN --}}
    <section class="w-full bg-white py-6 border-b border-outline-variant/30">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin flex flex-col items-center gap-3">
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a aria-current="page" class="px-6 py-2 rounded-full bg-[#003580] text-white font-label-md text-label-md font-semibold shadow-sm transition-all" href="{{ route('tentang') }}">
                    Visi, Misi &amp; Tujuan
                </a>
                <a class="px-6 py-2 rounded-full border-2 border-[#003580] text-[#003580] hover:bg-[#003580] hover:text-white font-label-md text-label-md font-semibold transition-all" href="#struktur-organisasi">
                    Struktur Organisasi
                </a>
            </div>
            <div class="flex items-center gap-3 text-body-sm font-medium text-on-surface-variant">
                <a class="hover:text-[#003580] transition-colors" href="#visi">Visi</a>
                <span>·</span>
                <a class="hover:text-[#003580] transition-colors" href="#misi">Misi</a>
                <span>·</span>
                <a class="hover:text-[#003580] transition-colors" href="#tujuan">Tujuan</a>
            </div>
        </div>
    </section>

    {{-- 3. SECTION PENGANTAR --}}
    <section class="w-full bg-white py-space-xl">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
                <div class="lg:col-span-7 flex flex-col items-start">
                    <div class="mb-4">
                        <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">TENTANG KAMI</span>
                        <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">UPT PP-ESTEM Institut Teknologi Del</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                            <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                        </div>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-4 text-justify">
    Pembelajaran di Luar Kampus (PDLK) merupakan model pembelajaran berbasis pengalaman langsung (<em>Experiential Learning</em>) yang dirancang terencana, terstruktur, dan terukur untuk mengintegrasikan pengetahuan teoretis dengan praktik lapangan—mengasah kemampuan berpikir kritis, pemecahan masalah, komunikasi, kolaborasi, serta kepemimpinan mahasiswa di dunia kerja, riset, pendidikan, maupun masyarakat.
</p>
<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-5 text-justify">
    Unit Pelayanan Teknis Pusat Pembelajaran Elektronik dan Sains, Teknologi, Enjinering, dan Matematika (UPT PP-ESTEM) berada di bawah koordinasi Wakil Rektor I Bidang Akademik dan Kemahasiswaan dengan dua divisi, yaitu Divisi Pelatihan dan Divisi PDLK. Unit ini bertanggung jawab dalam administrasi, sosialisasi program, pendaftaran peserta, seleksi, pengumuman hasil seleksi, pelaksanaan kegiatan PDLK, serta pelaksanaan monitoring dan evaluasi (monev) pada pertengahan dan akhir semester untuk peningkatan mutu berkelanjutan.
</p>
                    <div class="w-full p-4 rounded-lg bg-surface-container-low border-l-4 border-[#46166c]">
                        <p class="font-body-md text-body-md italic text-primary leading-relaxed">
                            “Berlandaskan nilai luhur MarTuhan, Marroha, Marbisuk untuk membentuk insan teknologi unggul dan berkarakter.”
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative flex flex-col items-center justify-center p-8 bg-surface-container-lowest rounded-2xl shadow-md border-2 border-outline-variant/30 text-center">
                        <div class="w-40 h-40 rounded-full border-4 border-[#1a81c2] p-1.5 flex items-center justify-center mb-4 relative shadow-sm">
                            <div class="w-full h-full rounded-full border-2 border-[#46166c] p-3 flex items-center justify-center bg-white">
                                <img alt="Logo Institut Teknologi Del" class="w-24 h-24 object-contain" src="{{ asset('images/logo-monitoring-pdlk.png') }}">
                            </div>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-[#003580] font-bold">Institut Teknologi Del</h3>
                        <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-wider font-semibold mt-1">UPT PP-ESTEM</span>
                        <div class="flex items-center gap-1.5 mt-3 text-label-sm text-[#1a81c2] font-semibold">
                            <i class="bi bi-stars"></i><span>MarTuhan · Marroha · Marbisuk</span><i class="bi bi-stars"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. SECTION VISI --}}
    <section class="w-full bg-white py-space-xl pt-0" id="visi">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="relative rounded-2xl p-6 sm:p-10 shadow-lg text-white text-center overflow-hidden" style="background: linear-gradient(135deg, #46166c 0%, #664780 100%);">
                <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center text-3xl text-white mb-3 backdrop-blur-sm">
                        <i class="bi bi-eye"></i>
                    </div>
                    <span class="font-label-sm text-label-sm text-[#8cc5dd] uppercase tracking-widest font-bold mb-2">VISI</span>
                    <i class="bi bi-quote text-[#8cc5dd] text-5xl leading-none block mb-2"></i>
                    <p class="font-headline-md text-headline-md sm:text-2xl italic leading-relaxed text-white font-normal mb-5">
                        “Sebagai pusat pengembangan pembelajaran yang unggul dan inovatif pada bidang Sains, Teknologi, Enjinering, dan Matematika (STEM) dan pembelajaran berbasis elektronik yang berkualitas, efektif, produktif, dan akuntabel.”
                    </p>
                    <div class="w-16 h-1 bg-[#8cc5dd] rounded-full mb-3"></div>
                    <span class="font-body-sm text-body-sm text-white/80">Ditetapkan sebagai pedoman strategis arah tridharma dan transformasi pendidikan UPT PP-ESTEM IT Del.</span>
                </div>
                <div class="absolute bottom-0 left-0 right-0 pointer-events-none opacity-[0.08]">
                    <svg class="w-full h-20 block" preserveAspectRatio="none" viewBox="0 0 1440 80" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,30 Q360,70 720,30 T1440,30 L1440,80 L0,80 Z" fill="#ffffff"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. SECTION MISI (Timeline Vertikal) --}}
    <section class="w-full bg-[#dce3eb] py-space-xl" id="misi">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">MISI</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Misi UPT PP-ESTEM</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Langkah konkret dan komitmen institusional dalam merealisasikan visi keunggulan pembelajaran.</p>
            </div>

            <div class="relative max-w-4xl mx-auto">
                <div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-1 -ml-0.5 rounded-full" style="background: linear-gradient(to bottom, #1a81c2, #46166c);"></div>
                <div class="md:hidden absolute left-5 top-4 bottom-4 w-1 -ml-0.5 rounded-full" style="background: linear-gradient(to bottom, #1a81c2, #46166c);"></div>

                <div class="space-y-8">
                    {{-- Item 1 --}}
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="w-full md:w-1/2 md:pr-10 pl-12 md:pl-0">
                            <div class="bg-white p-5 rounded-xl shadow-sm border-t-[3px] border-[#1a81c2] hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#1a81c2]/10 text-[#1a81c2] flex items-center justify-center text-lg shrink-0 mt-0.5"><i class="bi bi-search"></i></div>
                                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Melakukan kegiatan penelitian dan pengembangan terkait metode, media, alat peraga, dan sumber belajar yang inovatif dan berkualitas yang berbasiskan konsep STEM serta pengembangan dan penerapannya dalam kegiatan belajar mengajar di IT Del.</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute left-1 md:left-1/2 -translate-x-0 md:-translate-x-1/2 w-9 h-9 rounded-full bg-[#1a81c2] text-white font-bold text-sm flex items-center justify-center shadow-md z-10 border-2 border-white">1</div>
                        <div class="hidden md:block w-1/2"></div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="hidden md:block w-1/2"></div>
                        <div class="absolute left-1 md:left-1/2 -translate-x-0 md:-translate-x-1/2 w-9 h-9 rounded-full bg-[#46166c] text-white font-bold text-sm flex items-center justify-center shadow-md z-10 border-2 border-white">2</div>
                        <div class="w-full md:w-1/2 md:pl-10 pl-12">
                            <div class="bg-white p-5 rounded-xl shadow-sm border-t-[3px] border-[#46166c] hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#46166c]/10 text-[#46166c] flex items-center justify-center text-lg shrink-0 mt-0.5"><i class="bi bi-people"></i></div>
                                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Mengembangkan pembelajaran dengan pendekatan yang berpusat pada mahasiswa (SCL – Student Center Learning) dan penerapannya dalam kegiatan belajar mengajar di IT Del.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="w-full md:w-1/2 md:pr-10 pl-12 md:pl-0">
                            <div class="bg-white p-5 rounded-xl shadow-sm border-t-[3px] border-[#1a81c2] hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#1a81c2]/10 text-[#1a81c2] flex items-center justify-center text-lg shrink-0 mt-0.5"><i class="bi bi-laptop"></i></div>
                                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Mengembangkan infrastruktur dan metode pembelajaran elektronik (e-learning) serta penerapannya dalam kegiatan belajar mengajar di IT Del.</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute left-1 md:left-1/2 -translate-x-0 md:-translate-x-1/2 w-9 h-9 rounded-full bg-[#1a81c2] text-white font-bold text-sm flex items-center justify-center shadow-md z-10 border-2 border-white">3</div>
                        <div class="hidden md:block w-1/2"></div>
                    </div>

                    {{-- Item 4 --}}
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="hidden md:block w-1/2"></div>
                        <div class="absolute left-1 md:left-1/2 -translate-x-0 md:-translate-x-1/2 w-9 h-9 rounded-full bg-[#46166c] text-white font-bold text-sm flex items-center justify-center shadow-md z-10 border-2 border-white">4</div>
                        <div class="w-full md:w-1/2 md:pl-10 pl-12">
                            <div class="bg-white p-5 rounded-xl shadow-sm border-t-[3px] border-[#46166c] hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#46166c]/10 text-[#46166c] flex items-center justify-center text-lg shrink-0 mt-0.5"><i class="bi bi-easel"></i></div>
                                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Melakukan kegiatan pelatihan dan pendampingan kepada sivitas IT Del dan kepada pihak eksternal terkait dengan pembelajaran yang inovatif berbasiskan STEM, SCL, dan elektronik.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Item 5 --}}
                    <div class="relative flex flex-col md:flex-row items-center">
                        <div class="w-full md:w-1/2 md:pr-10 pl-12 md:pl-0">
                            <div class="bg-white p-5 rounded-xl shadow-sm border-t-[3px] border-[#1a81c2] hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#1a81c2]/10 text-[#1a81c2] flex items-center justify-center text-lg shrink-0 mt-0.5"><i class="bi bi-diagram-3"></i></div>
                                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Menjalin kerjasama dengan pihak eksternal seperti pemerintah, institusi pendidikan, LSM, dan dunia usaha dalam pengembangan dan penerapan pembelajaran yang inovatif.</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute left-1 md:left-1/2 -translate-x-0 md:-translate-x-1/2 w-9 h-9 rounded-full bg-[#1a81c2] text-white font-bold text-sm flex items-center justify-center shadow-md z-10 border-2 border-white">5</div>
                        <div class="hidden md:block w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. SECTION TUJUAN --}}
    <section class="w-full bg-white py-space-xl" id="tujuan">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">TUJUAN</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Tujuan UPT PP-ESTEM</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Capaian strategis penyelenggaraan program pengembangan pendidikan Institut Teknologi Del.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="group bg-white rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-4 hover:border-b-[#46166c]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-[#1a81c2] text-white flex items-center justify-center text-xl shrink-0 shadow-sm"><i class="bi bi-map"></i></div>
                        <span class="font-display-lg text-2xl font-bold text-[#8cc5dd] leading-none opacity-85 select-none">01</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Menyusun rencana (peta jalan) pengembangan, implementasi, dan penjaminan mutu pembelajaran inovatif berbasis STEM, SCL, dan elektronik.</p>
                </div>

                <div class="group bg-white rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-4 hover:border-b-[#46166c]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-[#46166c] text-white flex items-center justify-center text-xl shrink-0 shadow-sm"><i class="bi bi-person-video3"></i></div>
                        <span class="font-display-lg text-2xl font-bold text-[#8cc5dd] leading-none opacity-85 select-none">02</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Melakukan fasilitasi dosen dalam membuat konten pembelajaran berbasis STEM dan pembelajaran elektronik termasuk media penyebarluasan praktik terbaik pembelajaran.</p>
                </div>

                <div class="group bg-white rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-4 hover:border-b-[#46166c]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-[#1a81c2] text-white flex items-center justify-center text-xl shrink-0 shadow-sm"><i class="bi bi-mortarboard"></i></div>
                        <span class="font-display-lg text-2xl font-bold text-[#8cc5dd] leading-none opacity-85 select-none">03</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Meningkatkan kemampuan lulusan dalam pemecahan masalah, perancangan, berpikir logis, dan literasi teknologi dengan pendekatan STEM sehingga mendukung serapan kerja yang tinggi yang dibutuhkan industri dan masyarakat.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="group bg-white rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-4 hover:border-b-[#46166c]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-[#46166c] text-white flex items-center justify-center text-xl shrink-0 shadow-sm"><i class="bi bi-person-workspace"></i></div>
                        <span class="font-display-lg text-2xl font-bold text-[#8cc5dd] leading-none opacity-85 select-none">04</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Meningkatkan kemampuan tenaga pengajar di lembaga pendidikan yang menjadi mitra dalam melaksanakan pembelajaran yang inovatif berbasis STEM, SCL, dan elektronik.</p>
                </div>

                <div class="group bg-white rounded-xl p-6 shadow-sm border border-outline-variant/30 flex flex-col justify-between h-full relative overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-b-4 hover:border-b-[#46166c]">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-[#1a81c2] text-white flex items-center justify-center text-xl shrink-0 shadow-sm"><i class="bi bi-people-fill"></i></div>
                        <span class="font-display-lg text-2xl font-bold text-[#8cc5dd] leading-none opacity-85 select-none">05</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">Meningkatkan jalinan kerjasama dengan para pihak dalam mendukung pelaksanaan program-program pembelajaran yang inovatif berbasis STEM, SCL, dan elektronik.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. PITA AJAKAN (CTA BANNER) --}}
    <section class="w-full relative overflow-hidden py-10 px-margin-mobile md:px-margin" style="background: linear-gradient(135deg, #003580 0%, #1a81c2 100%);">
        <div class="absolute top-0 left-0 right-0 pointer-events-none opacity-10">
            <svg class="w-full h-8 block" preserveAspectRatio="none" viewBox="0 0 1440 40" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,20 Q360,0 720,20 T1440,20 L1440,0 L0,0 Z" fill="#ffffff"></path>
            </svg>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col items-start text-left">
                <h3 class="text-white font-headline-lg text-headline-lg font-bold tracking-tight">Kenali Struktur Pengelola UPT PP-ESTEM</h3>
                <p class="text-white/90 font-body-md text-body-md mt-1 max-w-xl">Tim pengelola dan pembina program Pembelajaran di Luar Kampus Institut Teknologi Del.</p>
            </div>
            <div class="flex items-center shrink-0">
                <a class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-white text-[#003580] font-label-lg text-label-lg font-bold shadow-md hover:bg-white/90 transition-all" href="#struktur-organisasi">
                    <span>Lihat Struktur Organisasi</span>
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- 8. STRUKTUR ORGANISASI (Dinamis dari database: organization_positions) --}}
    <section class="w-full bg-[#dce3eb] py-space-xl" id="struktur-organisasi">
        <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin">
            <div class="text-center max-w-2xl mx-auto mb-10 flex flex-col items-center">
                <span class="font-label-sm text-label-sm text-[#46166c] uppercase tracking-widest font-bold block mb-1">MANAJEMEN &amp; TATA KELOLA</span>
                <h2 class="font-headline-lg text-headline-lg text-[#003580] tracking-tight">Struktur Organisasi UPT PP-ESTEM</h2>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-10 h-1 bg-[#1a81c2] rounded-full inline-block"></span>
                    <span class="w-5 h-1 bg-[#46166c] rounded-full inline-block"></span>
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant mt-2">Tim Pengelola &amp; Pembina Program Pembelajaran di Luar Kampus Institut Teknologi Del.</p>
            </div>

            @if ($struktur->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                    Data struktur organisasi belum ditambahkan.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($struktur as $s)
                        <div class="group bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-md border-b-4 border-transparent hover:border-[#46166c]">
                            <div>
                                <div class="bg-[#003580] py-4 flex justify-center items-center">
                                    <div class="w-12 h-12 rounded-full bg-white text-[#003580] flex items-center justify-center text-xl shadow-sm">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <span class="font-label-sm text-label-sm text-[#46166c] block uppercase tracking-wider font-semibold">{{ $s->position_name }}</span>
                                    <h3 class="font-headline-sm text-headline-sm text-primary mt-1 leading-snug font-bold">{{ $s->officer_name ?? '-' }}</h3>
                                    @if ($s->description)
                                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-2 leading-relaxed">{{ $s->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
