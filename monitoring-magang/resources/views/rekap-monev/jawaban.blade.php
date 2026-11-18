@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Jawaban Monev | Monitoring PDLK')
@section('breadcrumb', 'Monev / Rekap / Jawaban')
@section('page-title', 'Jawaban ' . Label::of(Label::MONEV_TYPE, $respon->type))

@section('content')
<div class="p-stack">
    <div class="p-card">
        <dl class="p-dl">
            <dt>Mahasiswa</dt><dd>{{ $respon->name }} ({{ $respon->nim }})</dd>
            <dt>Kuesioner</dt><dd>{{ $respon->title }}</dd>
            <dt>Status</dt><dd>{{ Label::of(Label::RESPONSE_STATUS, $respon->status) }}
                @if ($respon->submitted_at) · {{ tgl($respon->submitted_at, 'd F Y H:i') }}{{ $respon->submitted_at > $respon->deadline_at ? ' (terlambat)' : '' }}@endif</dd>
        </dl>
    </div>
    <div class="p-card">
        @php $bagian = false; @endphp
        @foreach ($jawaban as $i => $j)
            @if ($j->section_title !== $bagian && $j->section_title)
                <h3 style="margin:18px 0 6px;color:var(--accent-dark);font-size:15px">{{ $j->section_title }}</h3>
            @endif
            @php $bagian = $j->section_title; @endphp
            <div style="padding:10px 0;border-bottom:1px solid var(--border)">
                <div class="p-muted">{{ $i + 1 }}. {{ $j->question_text }}</div>
                <div style="margin-top:4px">
                    @if ($j->question_type === 'file' && $j->file_path)
                        <a href="{{ file_url($j->file_path) }}" target="_blank">Lihat file</a>
                    @else
                        <span class="p-prose">{{ $j->answer_text ?? '—' }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <a href="{{ url()->previous() }}" class="p-btn p-btn-light" style="align-self:flex-start">← Kembali</a>
</div>
@endsection
