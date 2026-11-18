@extends('layouts.pdlk')

@section('title', 'Formulir Pendaftaran | Monitoring PDLK')
@section('breadcrumb', 'Formulir Pendaftaran')
@section('page-title', 'Formulir Pendaftaran PDLK')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-alert p-alert-info">
        Anda mendaftar pada <strong>{{ $lowongan->title }}</strong> — {{ $lowongan->partner_name }}
        ({{ $lowongan->activity_name }}).
        <a href="{{ route('portal.lowongan.show', $lowongan->slug) }}" target="_blank">Lihat kriteria</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="p-card"
          action="{{ route('mahasiswa.pendaftaran.store', $lowongan->slug) }}">
        @csrf

        <div class="p-card-header">
            <div>
                <h2 class="p-card-title">Data Pendaftar</h2>
                <p class="p-card-subtitle">NIM diverifikasi dengan data CIS akun Anda. Nama mengikuti data CIS.</p>
            </div>
        </div>

        <div class="p-form-grid">
            <div class="p-field">
                <label for="nama">Nama</label>
                <input id="nama" class="p-input" value="{{ $awal['nama'] }}" disabled>
            </div>

            <div class="p-field">
                <label for="nim">NIM <span class="p-required">*</span></label>
                <input id="nim" name="nim" class="p-input" required maxlength="30"
                       value="{{ old('nim', $awal['nim']) }}" @error('nim') aria-invalid="true" @enderror>
                @error('nim')<p class="p-error">{{ $message }}</p>@enderror
            </div>

            <div class="p-field">
                <label for="study_program_id">Program studi <span class="p-required">*</span></label>
                <select id="study_program_id" name="study_program_id" class="p-select" required>
                    <option value="">Pilih program studi</option>
                    @foreach ($prodiList as $prodi)
                        <option value="{{ $prodi->id }}" @selected((int) old('study_program_id', $awal['study_program_id']) === (int) $prodi->id)>{{ $prodi->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-field">
                <label for="semester">Semester <span class="p-required">*</span></label>
                <input type="number" id="semester" name="semester" class="p-input" min="1" max="14" required value="{{ old('semester') }}">
            </div>

            <div class="p-field">
                <label for="gpa">IPK <span class="p-required">*</span></label>
                <input type="number" id="gpa" name="gpa" class="p-input" step="0.01" min="0" max="4" required
                       value="{{ old('gpa') }}" placeholder="Contoh: 3.45">
            </div>

            <div class="p-field">
                <label for="phone">No. HP / WhatsApp <span class="p-required">*</span></label>
                <input id="phone" name="phone" class="p-input" required value="{{ old('phone') }}" placeholder="081234567890">
                <p class="p-help">Mitra dapat menghubungi Anda langsung melalui nomor ini.</p>
            </div>

            <div class="p-field p-span-2">
                <label for="email">Email <span class="p-required">*</span></label>
                <input type="email" id="email" name="email" class="p-input" required value="{{ old('email', $awal['email']) }}">
            </div>

            <div class="p-field p-span-2">
                <label for="dokumen">Berkas persyaratan (1 file PDF) <span class="p-required">*</span></label>
                <input type="file" id="dokumen" name="dokumen" accept="application/pdf" class="p-input" required>
                <p class="p-help">
                    Gabungkan berkas berikut menjadi <strong>satu file PDF</strong> (maks. 10 MB):
                    KTM, KTP, CV, portofolio, transkrip nilai, dan surat pengantar.
                </p>
            </div>

            @if ($fields->isNotEmpty())
                <div class="p-span-2" style="border-top:1px solid var(--border);padding-top:16px">
                    <strong>Isian tambahan dari mitra</strong> <span class="p-optional">(tidak wajib)</span>
                </div>
                @foreach ($fields as $field)
                    <div class="p-field {{ $field->field_type === 'textarea' ? 'p-span-2' : '' }}">
                        <label for="tambahan_{{ $field->id }}">{{ $field->label }} <span class="p-optional">(opsional)</span></label>
                        @if ($field->field_type === 'textarea')
                            <textarea id="tambahan_{{ $field->id }}" name="tambahan[{{ $field->id }}]" class="p-textarea" rows="4">{{ old('tambahan.' . $field->id) }}</textarea>
                        @else
                            <input id="tambahan_{{ $field->id }}" name="tambahan[{{ $field->id }}]" class="p-input"
                                   type="{{ $field->field_type === 'number' ? 'number' : ($field->field_type === 'url' ? 'url' : 'text') }}"
                                   value="{{ old('tambahan.' . $field->id) }}">
                        @endif
                    </div>
                @endforeach
            @endif

            <div class="p-field p-span-2">
                <label class="p-check">
                    <input type="checkbox" name="persetujuan" value="1" @checked(old('persetujuan'))>
                    Saya menyatakan data yang diisi benar dan bersedia mengikuti PDLK hingga selesai bila dinyatakan lulus.
                </label>
            </div>
        </div>

        <div class="p-form-actions">
            <a href="{{ route('portal.lowongan.show', $lowongan->slug) }}" class="p-btn p-btn-light">← Kembali ke lowongan</a>
            <button class="p-btn p-btn-primary">Kirim Pendaftaran →</button>
        </div>
    </form>
</div>
@endsection
