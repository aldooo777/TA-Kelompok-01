@extends('layouts.pdlk')

@php
    use App\Support\Label;
    $baru = $kuesioner === null;
@endphp

@section('title', ($baru ? 'Buat' : 'Ubah') . ' Kuesioner | Monitoring PDLK')
@section('breadcrumb', 'Monev / Kuesioner')
@section('page-title', $baru ? 'Buat Kuesioner Monev' : 'Ubah Kuesioner Monev')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" class="p-card" action="{{ $baru ? route('kelola.kuesioner.store') : route('kelola.kuesioner.update', $kuesioner->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless
        <div class="p-form-grid">
            <div class="p-field">
                <label for="type">Jenis monev <span class="p-required">*</span></label>
                <select id="type" name="type" class="p-select" required>
                    @foreach (Label::MONEV_TYPE as $v => $l)
                        <option value="{{ $v }}" @selected(old('type', $kuesioner?->type) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="p-field">
                <label for="title">Judul <span class="p-required">*</span></label>
                <input id="title" name="title" class="p-input" required maxlength="200" value="{{ old('title', $kuesioner?->title) }}" placeholder="Contoh: Monev Tengah PDLK Genap 2025/2026">
            </div>
            <div class="p-field p-span-2">
                <label for="description">Petunjuk pengisian</label>
                <textarea id="description" name="description" class="p-textarea" rows="3">{{ old('description', $kuesioner?->description) }}</textarea>
            </div>
        </div>
        <div class="p-form-actions">
            <a href="{{ route('kelola.kuesioner.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan & Tambah Pertanyaan' : 'Simpan Kuesioner' }}</button>
        </div>
    </form>

    @unless ($baru)
        <div class="p-card">
            <h2 class="p-card-title">Pertanyaan ({{ $pertanyaan->count() }})</h2>
            <p class="p-card-subtitle">Pertanyaan yang sudah pernah dijawab hanya dinonaktifkan saat dihapus, agar jawaban lama tetap tersimpan.</p>

            @php $bagian = null; @endphp
            @forelse ($pertanyaan as $x)
                @if ($x->section_title !== $bagian)
                    @php $bagian = $x->section_title; @endphp
                    <h3 style="margin:20px 0 8px;font-size:15px;color:var(--accent-dark)">{{ $bagian ?: 'Tanpa bagian' }}</h3>
                @endif
                @php $opsi = json_decode((string) $x->options, true); @endphp
                <details style="border:1px solid var(--border);border-radius:8px;padding:12px;margin-top:8px">
                    <summary style="cursor:pointer">
                        <strong>{{ $x->display_order }}. {{ $x->question_text }}</strong>
                        <span class="p-muted"> · {{ Label::of(Label::QUESTION_TYPE, $x->question_type) }}{{ $x->is_required ? ' · wajib' : '' }}{{ $x->jumlah_jawaban ? ' · ' . $x->jumlah_jawaban . ' jawaban' : '' }}</span>
                    </summary>
                    @include('kelola.monev._form-pertanyaan', ['x' => $x, 'opsi' => $opsi, 'aksi' => route('kelola.kuesioner.pertanyaan.update', [$kuesioner->id, $x->id])])
                    <form method="POST" action="{{ route('kelola.kuesioner.pertanyaan.destroy', [$kuesioner->id, $x->id]) }}" onsubmit="return confirm('Hapus pertanyaan ini?')" style="margin-top:8px">
                        @csrf @method('DELETE')
                        <button class="p-btn p-btn-danger p-btn-sm">Hapus pertanyaan</button>
                    </form>
                </details>
            @empty
                <p class="p-muted" style="margin-top:12px">Belum ada pertanyaan.</p>
            @endforelse
        </div>

        <div class="p-card">
            <h2 class="p-card-title">Tambah Pertanyaan</h2>
            @include('kelola.monev._form-pertanyaan', ['x' => null, 'opsi' => null, 'aksi' => route('kelola.kuesioner.pertanyaan.store', $kuesioner->id)])
        </div>
    @endunless
</div>
@endsection
