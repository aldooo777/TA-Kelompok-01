@extends('layouts.pdlk')

@php
    use App\Support\Label;
    $judul = Label::of(Label::MONEV_TYPE, $jadwal->type);
@endphp

@section('title', $judul . ' | Monitoring PDLK')
@section('breadcrumb', 'Monitoring / ' . $judul)
@section('page-title', $judul)

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <h2 class="p-card-title">{{ $jadwal->title }}</h2>
        @if ($jadwal->description)<p class="p-card-subtitle p-prose">{{ $jadwal->description }}</p>@endif
        <dl class="p-dl" style="margin-top:12px">
            <dt>Mahasiswa</dt><dd>{{ $peserta->name }} ({{ $peserta->nim }})</dd>
            <dt>Kegiatan</dt><dd>{{ $peserta->activity_name }} · {{ $peserta->partner_name }}</dd>
            <dt>Tenggat</dt><dd>{{ tgl($jadwal->deadline_at, 'l, d F Y H:i') }}
                @if (!$respon || $respon->status !== 'submitted')
                    @if (now()->toDateTimeString() > $jadwal->deadline_at)<span class="p-badge p-badge-failed">Lewat tenggat</span>@endif
                @endif
            </dd>
            @if ($respon?->status === 'submitted')
                <dt>Dikirim</dt><dd>{{ tgl($respon->submitted_at, 'd F Y H:i') }}</dd>
            @endif
        </dl>
    </div>

    @if (!$bisaIsi && $respon?->status !== 'submitted')
        <div class="p-alert p-alert-warning">Monev ini sedang tidak dibuka untuk diisi.</div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ route('monitoring.simpan', $jadwal->id) }}" class="p-stack">
        @csrf
        @php $bagian = false; @endphp
        @forelse ($pertanyaan as $i => $x)
            @php
                $opsi = json_decode((string) $x->options, true);
                $j = $jawaban->get($x->id);
                $nilai = old("jawaban.{$x->id}", $j?->answer_text);
            @endphp
            @if ($x->section_title !== $bagian)
                @if ($bagian !== false)</div>@endif
                @php $bagian = $x->section_title; @endphp
                <div class="p-card">
                @if ($bagian)<h2 class="p-card-title" style="margin-bottom:6px">{{ $bagian }}</h2>@endif
            @endif

            <div class="p-field" style="margin-top:18px">
                <label for="j{{ $x->id }}">{{ $i + 1 }}. {{ $x->question_text }} @if ($x->is_required)<span class="p-required">*</span>@endif</label>

                @switch($x->question_type)
                    @case('text')
                        <input id="j{{ $x->id }}" name="jawaban[{{ $x->id }}]" class="p-input" value="{{ $nilai }}" @disabled(!$bisaIsi)>
                        @break
                    @case('scale')
                        <div style="display:flex;gap:14px;flex-wrap:wrap">
                            @for ($n = $opsi['min'] ?? 1; $n <= ($opsi['max'] ?? 5); $n++)
                                <label class="p-check"><input type="radio" name="jawaban[{{ $x->id }}]" value="{{ $n }}" @checked((string) $nilai === (string) $n) @disabled(!$bisaIsi)> {{ $n }}</label>
                            @endfor
                        </div>
                        <p class="p-help">{{ $opsi['min'] ?? 1 }} = sangat kurang, {{ $opsi['max'] ?? 5 }} = sangat baik</p>
                        @break
                    @case('choice')
                        @foreach ((array) $opsi as $o)
                            <label class="p-check" style="display:flex;margin-top:4px"><input type="radio" name="jawaban[{{ $x->id }}]" value="{{ $o }}" @checked($nilai === $o) @disabled(!$bisaIsi)> {{ $o }}</label>
                        @endforeach
                        @break
                    @case('file')
                        @if ($j?->file_path)
                            <p style="margin:0 0 6px"><a href="{{ file_url($j->file_path) }}" target="_blank">Lihat file yang sudah diunggah</a></p>
                        @endif
                        @if ($bisaIsi)
                            <input type="file" id="j{{ $x->id }}" name="jawaban[{{ $x->id }}]" class="p-input" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                            <p class="p-help">Bukti pelaksanaan, misalnya foto saat meeting (PDF/gambar/Word, maks. 10 MB).{{ $j?->file_path ? ' Unggah lagi untuk mengganti.' : '' }}</p>
                        @endif
                        @break
                    @default
                        <textarea id="j{{ $x->id }}" name="jawaban[{{ $x->id }}]" class="p-textarea" rows="4" @disabled(!$bisaIsi)>{{ $nilai }}</textarea>
                @endswitch
                @error("jawaban.{$x->id}")<p class="p-error">{{ $message }}</p>@enderror
            </div>
            @if ($loop->last)</div>@endif
        @empty
            <div class="p-card p-empty">Kuesioner ini belum memiliki pertanyaan.</div>
        @endforelse

        @if ($bisaIsi && $pertanyaan->isNotEmpty())
            <div class="p-form-actions" style="border:0;margin:0;padding:0">
                <button name="aksi" value="draf" class="p-btn p-btn-light">Simpan Draf</button>
                <button name="aksi" value="kirim" class="p-btn p-btn-primary"
                        onclick="return confirm('Kirim monev? Jawaban tidak dapat diubah setelah dikirim.')">Kirim Monev →</button>
            </div>
        @else
            <a href="{{ route('monitoring.index') }}" class="p-btn p-btn-light">← Kembali</a>
        @endif
    </form>
</div>
@endsection
