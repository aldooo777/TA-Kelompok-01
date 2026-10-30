
@extends('layouts.pdlk')

@section('title', 'Monitoring Pertengahan | Monitoring PDLK')
@section('breadcrumb', 'Monitoring / Pertengahan PDLK')
@section('page-title', 'Monitoring Pertengahan PDLK')

@section('content')

<style>
    .monitoring-panel {
        padding: 24px;
        background: #fff;
        border: 1px solid #e1e7ef;
        border-radius: 5px;
    }

    .monitoring-heading {
        margin: 0 0 8px;
        color: #26364d;
        font-size: 21px;
        font-weight: 600;
        line-height: 1.5;
    }

    .monitoring-description {
        margin: 0 0 22px;
        color: #718096;
        font-size: 13px;
        line-height: 1.8;
    }

    .monitoring-notice {
        margin-bottom: 24px;
        padding: 13px 16px;
        border-left: 4px solid #348db8;
        background: #e8f3f9;
        color: #354052;
        border-radius: 3px;
        font-size: 12px;
        line-height: 1.7;
    }

    .monitoring-form {
        width: 100%;
    }

    .monitoring-form-field {
        display: block;
        width: 100%;
        min-width: 0;
        margin-bottom: 20px;
    }

    .monitoring-form-field label {
        display: block;
        margin-bottom: 8px;
        color: #354052;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.5;
    }

    .monitoring-form-field input,
    .monitoring-form-field select,
    .monitoring-form-field textarea {
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

    .monitoring-form-field input[readonly] {
        background: #f4f7fa;
        color: #596579;
        cursor: default;
    }

    .monitoring-form-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .monitoring-form-field input:focus,
    .monitoring-form-field select:focus,
    .monitoring-form-field textarea:focus {
        outline: none;
        border-color: #348db8;
        box-shadow: 0 0 0 3px rgba(52, 141, 184, .12);
    }

    .monitoring-form-field input::placeholder,
    .monitoring-form-field textarea::placeholder {
        color: #9aa5b4;
    }

    .monitoring-form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid #e5e9ef;
    }

    .monitoring-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 17px;
        border: 1px solid transparent;
        border-radius: 4px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
    }

    .monitoring-button-back {
        background: #edf1f6;
        border-color: #edf1f6;
        color: #354052;
    }

    .monitoring-button-back:hover {
        background: #e0e7ef;
        border-color: #e0e7ef;
        color: #26364d;
        text-decoration: none;
    }

    .monitoring-button-submit {
        background: #348db8;
        border-color: #348db8;
        color: #fff;
    }

    .monitoring-button-submit:hover {
        background: #287ba4;
        border-color: #287ba4;
    }

    .monitoring-button:focus-visible {
        outline: 3px solid rgba(52, 141, 184, .25);
        outline-offset: 2px;
    }

    @media (max-width: 600px) {
        .monitoring-panel {
            padding: 16px;
        }

        .monitoring-heading {
            font-size: 19px;
        }

        .monitoring-form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .monitoring-button {
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>

<section class="panel monitoring-panel">

    <h2 class="monitoring-heading">
        Kuesioner Progres Kegiatan
    </h2>

    <p class="monitoring-description">
        Isi informasi mengenai pelaksanaan, perkembangan, dan kendala
        kegiatan PDLK yang sedang Anda jalani.
    </p>

    <div class="monitoring-notice">
        Formulir ini masih dalam tahap pengembangan dan belum menyimpan
        jawaban secara permanen.
    </div>

    <form
        class="monitoring-form"
        onsubmit="event.preventDefault(); alert('Formulir ini masih dalam tahap pengembangan dan belum menyimpan data.');"
    >

        {{-- Identitas Mahasiswa --}}

        <div class="monitoring-form-field">
            <label for="nim">NIM Mahasiswa</label>

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

        <div class="monitoring-form-field">
            <label for="nama">Nama Mahasiswa</label>

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

        <div class="monitoring-form-field">
            <label for="program_studi">Program Studi</label>

            <input
                id="program_studi"
                name="program_studi"
                type="text"
                value="{{ session('cis_user')['prodi'] ?? '' }}"
                placeholder="Program studi"
                readonly
            >
        </div>

        {{-- Jenis Kegiatan PDLK --}}

        <div class="monitoring-form-field">
            <label for="jenis_pdlk">Bentuk Kegiatan PDLK</label>

            <select id="jenis_pdlk" name="jenis_pdlk" required>
                <option
                    value=""
                    @selected(!request()->query('jenis'))
                >
                    Pilih jenis kegiatan
                </option>

                <option
                    value="magang"
                    @selected(request()->query('jenis') === 'magang')
                >
                    Magang
                </option>

                <option
                    value="kerja-praktik"
                    @selected(request()->query('jenis') === 'kerja-praktik')
                >
                    Kerja Praktik
                </option>

                <option
                    value="penelitian"
                    @selected(request()->query('jenis') === 'penelitian')
                >
                    Penelitian
                </option>

                <option
                    value="pertukaran-pelajar"
                    @selected(request()->query('jenis') === 'pertukaran-pelajar')
                >
                    Pertukaran Pelajar
                </option>

                <option
                    value="kkn"
                    @selected(request()->query('jenis') === 'kkn')
                >
                    Kuliah Kerja Nyata (KKN)
                </option>

                <option
                    value="studi-independen"
                    @selected(request()->query('jenis') === 'studi-independen')
                >
                    Studi Independen
                </option>

                <option
                    value="kewirausahaan"
                    @selected(request()->query('jenis') === 'kewirausahaan')
                >
                    Kewirausahaan
                </option>
            </select>
        </div>

        {{-- Informasi Pelaksanaan --}}

        <div class="monitoring-form-field">
            <label for="mitra">Mitra atau Tempat Pelaksanaan</label>

            <input
                id="mitra"
                name="mitra"
                type="text"
                placeholder="Nama perusahaan atau institusi"
                required
            >
        </div>

        <div class="monitoring-form-field">
            <label for="peran">Peran Mahasiswa Selama PDLK</label>

            <textarea
                id="peran"
                name="peran"
                rows="3"
                placeholder="Jelaskan peran Anda selama kegiatan"
                required
            ></textarea>
        </div>

        <div class="monitoring-form-field">
            <label for="mentor">Nama Mentor dari Mitra</label>

            <input
                id="mentor"
                name="mentor"
                type="text"
                placeholder="Nama mentor atau supervisor"
            >
        </div>

        <div class="monitoring-form-field">
            <label for="dosen_pembimbing">
                Dosen Pembimbing Institusi
            </label>

            <input
                id="dosen_pembimbing"
                name="dosen_pembimbing"
                type="text"
                placeholder="Nama dosen pembimbing"
            >
        </div>

        <div class="monitoring-form-field">
            <label for="proyek">
                Proyek atau Pekerjaan yang Dikerjakan
            </label>

            <textarea
                id="proyek"
                name="proyek"
                rows="4"
                placeholder="Jelaskan proyek atau pekerjaan yang dilakukan"
                required
            ></textarea>
        </div>

        <div class="monitoring-form-field">
            <label for="kendala">Kendala Selama Pelaksanaan PDLK</label>

            <textarea
                id="kendala"
                name="kendala"
                rows="4"
                placeholder="Jelaskan kendala yang dialami, atau tuliskan Tidak ada kendala"
                required
            ></textarea>
        </div>

        {{-- Tombol Formulir --}}

        <div class="monitoring-form-actions">

            <a
                href="{{ route('dashboard') }}"
                class="monitoring-button monitoring-button-back"
            >
                Kembali
            </a>

            <button
                type="submit"
                class="monitoring-button monitoring-button-submit"
            >
                Uji Tampilan Formulir
            </button>

        </div>

    </form>

</section>

@endsection
