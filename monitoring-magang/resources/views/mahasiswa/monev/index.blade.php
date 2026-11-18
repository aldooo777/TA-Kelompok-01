@extends('layouts.pdlk')

@php use App\Support\Label; @endphp

@section('title', 'Monev | Monitoring PDLK')
@section('breadcrumb', 'Monitoring / Monev')
@section('page-title', 'Monitoring & Evaluasi (Monev)')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <div class="p-card">
        <dl class="p-dl">
            <dt>Kegiatan</dt><dd>{{ $peserta->activity_name }} · {{ $peserta->partner_name }}</dd>
            <dt>Periode</dt><dd>{{ $peserta->period_name ?? 'Belum diatur' }}</dd>
            <dt>Status</dt><dd><span class="p-badge p-badge-{{ $peserta->status }}">{{ Label::of(Label::PARTICIPANT_STATUS, $peserta->status) }}</span></dd>
        </dl>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Monev Wajib</h2>
        <p class="p-card-subtitle">
            Monev tengah (minggu ke-8–9) dan monev akhir <strong>wajib</strong> diisi. Selama monev yang sudah dibuka belum dikirim,
            dashboard Anda terkunci. Setelah keduanya terkirim, status PDLK Anda otomatis menjadi <strong>Selesai</strong>.
        </p>

        @if (!$peserta->period_id)
            <div class="p-alert p-alert-warning" style="margin-top:14px">Periode PDLK Anda belum diatur. Hubungi UPT PP-ESTEM atau koordinator PDLK prodi.</div>
        @elseif ($jadwal->isEmpty())
            <div class="p-empty">Belum ada jadwal monev untuk periode Anda.</div>
        @else
            <div class="p-table-wrap" style="margin-top:14px">
                <table class="p-table">
                    <thead><tr><th>Monev</th><th>Dibuka</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($jadwal as $s)
                            @php
                                $belumBuka = $s->open_at > now()->toDateTimeString();
                                $terkirim = $s->response_status === 'submitted';
                                $terlambat = $terkirim && $s->submitted_at > $s->deadline_at;
                            @endphp
                            <tr>
                                <td><strong>{{ Label::of(Label::MONEV_TYPE, $s->type) }}</strong><span class="p-muted">{{ $s->title }}</span></td>
                                <td>{{ tgl($s->open_at, 'd/m/Y H:i') }}</td>
                                <td>{{ tgl($s->deadline_at, 'd/m/Y H:i') }}</td>
                                <td>
                                    @if ($terkirim)
                                        <span class="p-badge p-badge-passed">Terkirim</span>
                                        <span class="p-muted">{{ tgl($s->submitted_at, 'd/m/Y H:i') }}{{ $terlambat ? ' (terlambat)' : '' }}</span>
                                    @elseif ($belumBuka)
                                        <span class="p-badge p-badge-draft">Belum dibuka</span>
                                    @elseif ($s->is_closed)
                                        <span class="p-badge p-badge-ditutup">Ditutup</span>
                                    @elseif ($s->response_status === 'draft')
                                        <span class="p-badge p-badge-processing">Draf</span>
                                    @else
                                        <span class="p-badge p-badge-failed">Belum diisi</span>
                                    @endif
                                </td>
                                <td>
                                    @unless ($belumBuka)
                                        <a href="{{ route('monitoring.isi', $s->id) }}" class="p-btn p-btn-sm {{ $terkirim || $s->is_closed ? 'p-btn-light' : 'p-btn-primary' }}">
                                            {{ $terkirim || $s->is_closed ? 'Lihat' : 'Isi Monev' }}
                                        </a>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
