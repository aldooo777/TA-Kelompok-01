
@extends('layouts.pdlk')

@section('title', 'Monitoring Akhir | Monitoring PDLK')
@section('breadcrumb', 'Monitoring / Akhir PDLK')
@section('page-title', 'Monitoring Akhir PDLK')

@section('content')

@php
    $skala = [
        'sangat_tidak_setuju' => 'Sangat Tidak Setuju',
        'tidak_setuju' => 'Tidak Setuju',
        'netral' => 'Netral',
        'setuju' => 'Setuju',
        'sangat_setuju' => 'Sangat Setuju',
    ];

    $jenisPdlk = request()->query('jenis');

    $bagianEvaluasi = [
        'lingkungan' => [
            'judul' => 'A. Lingkungan Kerja, Tugas, dan Pembimbingan',
            'deskripsi' => 'Nilai pengalaman selama menjalankan kegiatan PDLK.',
            'pertanyaan' => [
                'lingkungan_nyaman' =>
                    'Saya merasa nyaman di lingkungan pelaksanaan PDLK.',
                'rekan_kerja' =>
                    'Saya diterima dengan baik oleh rekan kerja.',
                'instruksi_atasan' =>
                    'Instruksi dari atasan jelas dan mudah dipahami.',
                'pembimbingan' =>
                    'Saya memperoleh pembimbingan dari pembimbing atau supervisor.',
                'waktu_bimbingan' =>
                    'Waktu bimbingan dari supervisor atau pembimbing sudah cukup.',
                'tugas_sesuai_prodi' =>
                    'Tugas yang diberikan sesuai dengan bidang studi saya.',
                'beban_tugas' =>
                    'Tugas yang diberikan dapat diselesaikan secara wajar dan tepat waktu.',
                'posisi_relevan' =>
                    'Peran saya dalam proyek relevan dengan pembelajaran dan program studi saya.',
            ],
        ],
        'manajemen' => [
            'judul' => 'B. Manajemen PDLK',
            'deskripsi' => 'Evaluasi pengelolaan dan koordinasi kegiatan PDLK.',
            'pertanyaan' => [
                'manajemen_koordinator' =>
                    'Manajemen koordinator PDLK berjalan dengan baik.',
                'koordinasi_informasi' =>
                    'Informasi dan koordinasi terkait kegiatan PDLK disampaikan dengan baik.',
            ],
        ],
        'layanan' => [
            'judul' => 'C. Layanan UPT PP-ESTEM',
            'deskripsi' => 'Evaluasi layanan yang mendukung pelaksanaan PDLK.',
            'pertanyaan' => [
                'sosialisasi_upt' =>
                    'Sosialisasi UPT PP-ESTEM membantu saya memahami kegiatan PDLK.',
                'publikasi_upt' =>
                    'Informasi dan publikasi PDLK mudah dipahami dan diakses.',
                'administrasi_upt' =>
                    'Layanan administrasi UPT PP-ESTEM mendukung kegiatan PDLK.',
            ],
        ],
    ];

    $daftarJenisPdlk = [
        'magang' => 'Magang',
        'kerja-praktik' => 'Kerja Praktik',
        'penelitian' => 'Penelitian',
        'pertukaran-pelajar' => 'Pertukaran Pelajar',
        'kkn' => 'Kuliah Kerja Nyata (KKN)',
        'studi-independen' => 'Studi Independen',
        'kewirausahaan' => 'Kewirausahaan',
    ];
@endphp

<style>
    .monev-panel {
        padding: 24px;
        background: #fff;
        border: 1px solid #e1e7ef;
        border-radius: 5px;
    }

    .monev-intro {
        margin: 0 0 22px;
        color: #718096;
        font-size: 13px;
        line-height: 1.8;
    }

    .monev-notice {
        margin-bottom: 24px;
        padding: 13px 16px;
        border-left: 4px solid #348db8;
        border-radius: 3px;
        background: #e8f3f9;
        color: #354052;
        font-size: 12px;
        line-height: 1.7;
    }

    .monev-section {
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1px solid #e5e9ef;
    }

    .monev-section-first {
        margin-top: 0;
        padding-top: 0;
        border-top: 0;
    }

    .monev-section h3 {
        margin: 0 0 8px;
        color: #26364d;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.6;
    }

    .monev-section-description {
        margin: 0 0 18px;
        color: #718096;
        font-size: 12px;
        line-height: 1.8;
    }

    .monev-field {
        display: block;
        width: 100%;
        min-width: 0;
        margin-bottom: 20px;
    }

    .monev-field > label,
    .monev-question-title {
        display: block;
        margin-bottom: 8px;
        color: #354052;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
    }

    .monev-required {
        color: #dc3545;
    }

    .monev-field input:not([type="radio"]),
    .monev-field select,
    .monev-field textarea {
        display: block;
        width: 100%;
        max-width: 100%;
        min-height: 44px;
        box-sizing: border-box;
        margin: 0;
        padding: 11px 13px;
        border: 1px solid #ccd6e3;
        border-radius: 4px;
        background: #fff;
        color: #354052;
        font-family: inherit;
        font-size: 13px;
        line-height: 1.5;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .monev-field input[readonly] {
        background: #f4f7fa;
        color: #596579;
        cursor: default;
    }

    .monev-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .monev-field input:not([type="radio"]):focus,
    .monev-field select:focus,
    .monev-field textarea:focus {
        outline: none;
        border-color: #348db8;
        box-shadow: 0 0 0 3px rgba(52, 141, 184, .12);
    }

    .monev-field input::placeholder,
    .monev-field textarea::placeholder {
        color: #9aa5b4;
    }

    .monev-question {
        margin-bottom: 16px;
        padding: 17px;
        border: 1px solid #e1e7ef;
        border-radius: 4px;
        background: #fff;
    }

    .monev-question-title {
        margin-bottom: 15px;
    }

    .monev-options {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 20px;
    }

    .monev-options label {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0;
        color: #5b6677;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.6;
        cursor: pointer;
    }

    .monev-options input[type="radio"] {
        width: 15px;
        height: 15px;
        margin: 0;
        accent-color: #348db8;
        cursor: pointer;
        flex-shrink: 0;
    }

    .monev-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #e5e9ef;
    }

    .monev-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        box-sizing: border-box;
        padding: 10px 17px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
    }

    .monev-button-primary {
        border-color: #348db8;
        background: #348db8;
        color: #fff;
    }

    .monev-button-primary:hover {
        border-color: #287ba4;
        background: #287ba4;
        color: #fff;
    }

    .monev-button-secondary {
        border-color: #edf1f6;
        background: #edf1f6;
        color: #354052;
    }

    .monev-button-secondary:hover {
        border-color: #e0e7ef;
        background: #e0e7ef;
        color: #26364d;
        text-decoration: none;
    }

    .monev-button:focus-visible {
        outline: 3px solid rgba(52, 141, 184, .25);
        outline-offset: 2px;
    }

    @media (max-width: 700px) {
        .monev-panel {
            padding: 16px;
        }

        .monev-options {
            flex-direction: column;
            gap: 12px;
        }

        .monev-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .monev-button {
            width: 100%;
        }
    }
</style>

<section class="panel monev-panel">

    <h2 class="monitoring-heading">
        Kuesioner Monitoring dan Evaluasi Akhir
    </h2>

    <p class="monev-intro">
        Lengkapi informasi kegiatan dan berikan penilaian berdasarkan
        pengalaman selama mengikuti PDLK.
    </p>

    <div class="monev-notice" role="note">
        Formulir ini masih dalam tahap pengembangan dan belum menyimpan
        jawaban secara permanen.
    </div>

    <form id="monevAkhirForm">

        {{-- INFORMASI MAHASISWA DAN KEGIATAN --}}
        <div class="monev-section monev-section-first">

            <h3>Informasi Mahasiswa dan Kegiatan</h3>

            <p class="monev-section-description">
                Periksa identitas dan informasi kegiatan yang akan dievaluasi.
            </p>

            <div class="monev-field">
                <label for="nim">
                    NIM Mahasiswa
                </label>

                <input
                    id="nim"
                    name="nim"
                    type="text"
                    value="{{ session('cis_user')['nim'] ?? '' }}"
                    placeholder="NIM mahasiswa"
                    readonly
                    required
                >
            </div>

            <div class="monev-field">
                <label for="nama">
                    Nama Mahasiswa
                </label>

                <input
                    id="nama"
                    name="nama"
                    type="text"
                    value="{{ session('cis_user')['nama'] ?? '' }}"
                    placeholder="Nama mahasiswa"
                    readonly
                    required
                >
            </div>

            <div class="monev-field">
                <label for="program_studi">
                    Program Studi
                </label>

                <input
                    id="program_studi"
                    name="program_studi"
                    type="text"
                    value="{{ session('cis_user')['prodi'] ?? '' }}"
                    placeholder="Program studi"
                    readonly
                >
            </div>

            <div class="monev-field">
                <label for="jenis_pdlk">
                    Bentuk Kegiatan PDLK
                    <span class="monev-required">*</span>
                </label>

                <select id="jenis_pdlk" name="jenis_pdlk" required>
                    <option
                        value=""
                        @selected(!$jenisPdlk)
                    >
                        Pilih jenis kegiatan
                    </option>

                    @foreach ($daftarJenisPdlk as $slug => $namaJenis)
                        <option
                            value="{{ $slug }}"
                            @selected($jenisPdlk === $slug)
                        >
                            {{ $namaJenis }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="monev-field">
                <label for="mitra">
                    Mitra atau Tempat Pelaksanaan
                    <span class="monev-required">*</span>
                </label>

                <input
                    id="mitra"
                    name="mitra"
                    type="text"
                    placeholder="Nama perusahaan atau institusi"
                    required
                >
            </div>

        </div>

        {{-- INFORMASI PELAKSANAAN --}}
        <div class="monev-section">

            <h3>D. Informasi Pelaksanaan PDLK</h3>

            <div class="monev-field">
                <label for="sumber_informasi">
                    Sumber Informasi Lowongan PDLK
                    <span class="monev-required">*</span>
                </label>

                <select
                    id="sumber_informasi"
                    name="sumber_informasi"
                    required
                >
                    <option value="">Pilih sumber informasi</option>
                    <option value="mitra">
                        Langsung dari mitra atau perusahaan
                    </option>
                    <option value="cis">
                        Campus Information System (CIS)
                    </option>
                    <option value="prodi">
                        Fakultas atau program studi
                    </option>
                    <option value="teman">Teman</option>
                    <option value="keluarga">Keluarga</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="monev-field">
                <label for="media_pendaftaran">
                    Media Pendaftaran PDLK
                    <span class="monev-required">*</span>
                </label>

                <select
                    id="media_pendaftaran"
                    name="media_pendaftaran"
                    required
                >
                    <option value="">Pilih media pendaftaran</option>
                    <option value="mitra">
                        Mendaftar langsung ke mitra
                    </option>
                    <option value="upt">
                        Melalui UPT PP-ESTEM
                    </option>
                    <option value="prodi">
                        Melalui program studi
                    </option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="monev-field">
                <label for="onboarding">
                    Pelaksanaan Onboarding
                    <span class="monev-required">*</span>
                </label>

                <select
                    id="onboarding"
                    name="onboarding"
                    required
                >
                    <option value="">Pilih pelaksanaan onboarding</option>
                    <option value="mitra">
                        Di mitra atau perusahaan
                    </option>
                    <option value="institut">
                        Di lingkungan institusi/perguruan tinggi
                    </option>
                    <option value="keduanya">
                        Di mitra dan institusi
                    </option>
                    <option value="tidak_ada">
                        Tidak ada onboarding
                    </option>
                </select>
            </div>

            <div class="monev-field">
                <label for="bantuan">
                    Apakah Anda menerima bantuan atau pendanaan PDLK?
                    <span class="monev-required">*</span>
                </label>

                <select id="bantuan" name="bantuan" required>
                    <option value="">Pilih jawaban</option>
                    <option value="ya">Ya</option>
                    <option value="tidak">Tidak</option>
                </select>
            </div>

            <div class="monev-field">
                <label for="kendala">
                    Kendala Selama Pelaksanaan PDLK
                    <span class="monev-required">*</span>
                </label>

                <textarea
                    id="kendala"
                    name="kendala"
                    rows="4"
                    placeholder="Jelaskan kendala yang dialami. Jika tidak ada, tuliskan Tidak ada kendala."
                    required
                ></textarea>
            </div>

        </div>

        {{-- EVALUASI DENGAN SKALA PERSETUJUAN --}}
        @foreach ($bagianEvaluasi as $bagianKey => $bagian)
            <div class="monev-section">

                <h3>{{ $bagian['judul'] }}</h3>

                <p class="monev-section-description">
                    {{ $bagian['deskripsi'] }}
                </p>

                @foreach ($bagian['pertanyaan'] as $pertanyaanKey => $pertanyaan)
                    <div class="monev-question">

                        <span class="monev-question-title">
                            {{ $loop->iteration }}. {{ $pertanyaan }}
                        </span>

                        <div class="monev-options">
                            @foreach ($skala as $nilai => $label)
                                <label>
                                    <input
                                        type="radio"
                                        name="{{ $pertanyaanKey }}"
                                        value="{{ $nilai }}"
                                        required
                                    >

                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                    </div>
                @endforeach

            </div>
        @endforeach

        {{-- SARAN DAN MASUKAN --}}
        <div class="monev-section">

            <h3>E. Saran dan Masukan</h3>

            <div class="monev-field">
                <label for="saran">
                    Saran untuk peningkatan pelaksanaan PDLK
                </label>

                <textarea
                    id="saran"
                    name="saran"
                    rows="4"
                    placeholder="Tuliskan saran atau masukan bagi mitra, program studi, dan UPT PP-ESTEM"
                ></textarea>
            </div>

        </div>

        {{-- TOMBOL FORMULIR --}}
        <div class="monev-actions">

            <a
                href="{{ route('dashboard') }}"
                class="monev-button monev-button-secondary"
            >
                Kembali ke Dashboard
            </a>

            <button
                type="submit"
                class="monev-button monev-button-primary"
            >
                Uji Formulir
            </button>

        </div>

    </form>

</section>

<script>
    document
        .getElementById('monevAkhirForm')
        .addEventListener('submit', function (event) {
            event.preventDefault();

            alert(
                'Formulir Monitoring Akhir berhasil diuji dari sisi tampilan. ' +
                'Jawaban belum disimpan karena integrasi database belum dibuat.'
            );
        });
</script>

@endsection
