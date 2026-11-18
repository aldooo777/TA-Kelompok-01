@extends('layouts.publik')

@section('title', 'Tentang PDLK')

@section('hero')
<section class="pub-page-head">
    <div class="pub-container">
        <h1>Tentang PDLK &amp; UPT PP-ESTEM</h1>
        <p>Visi, misi, program kerja, dan alur Pembelajaran di Luar Kampus di Institut Teknologi Del.</p>
    </div>
</section>
@endsection

@section('content')
<div class="p-stack">
    <div class="p-card">
        <h2 class="p-card-title">Visi UPT PP-ESTEM</h2>
        <p style="margin:12px 0 0;line-height:1.7">Menjadi pusat pengembangan pembelajaran yang unggul dan inovatif melalui penerapan pembelajaran yang berkualitas, efisien, efektif, produktif, dan akuntabel.</p>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Misi</h2>
        <ol style="margin:12px 0 0;padding-left:20px;line-height:1.8">
            <li>Melaksanakan penelitian terkait metode, media, dan sumber belajar yang inovatif.</li>
            <li>Mengembangkan pembelajaran yang berpusat pada mahasiswa (<em>Student-Centered Learning</em>).</li>
            <li>Mengembangkan pembelajaran elektronik (<em>e-learning</em>).</li>
            <li>Menyelenggarakan pelatihan bagi sivitas akademika maupun pihak eksternal.</li>
            <li>Menjalin kerja sama dengan pemerintah, institusi pendidikan, dan industri.</li>
        </ol>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Program Kerja</h2>
        <ul style="margin:12px 0 0;padding-left:20px;line-height:1.8">
            <li>Pelatihan pembelajaran inovatif berbasis STEM, SCL, dan <em>e-learning</em>.</li>
            <li>Penelitian dan pengembangan pembelajaran.</li>
            <li>Kerja sama dengan pemerintah, institusi pendidikan, dan industri.</li>
            <li>Pengelolaan Pembelajaran di Luar Kampus (PDLK).</li>
        </ul>
    </div>

    @if ($struktur->isNotEmpty())
    <div class="p-card">
        <h2 class="p-card-title">Struktur Organisasi</h2>
        <div class="p-grid-cards" style="margin-top:16px">
            @foreach ($struktur as $s)
                <div style="border:1px solid var(--border);border-radius:8px;padding:14px">
                    <div class="p-muted">{{ $s->position_name }}</div>
                    <strong>{{ $s->officer_name ?? '-' }}</strong>
                    @if ($s->description)<p class="p-muted" style="margin:6px 0 0;line-height:1.5">{{ $s->description }}</p>@endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="p-card">
        <h2 class="p-card-title">Bentuk Kegiatan PDLK</h2>
        <div class="p-stack" style="margin-top:16px">
            @foreach ($jenisKegiatan as $jenis)
                <details style="border:1px solid var(--border);border-radius:8px;padding:14px">
                    <summary style="cursor:pointer"><strong>{{ $jenis->icon }} {{ $jenis->name }}</strong>
                        @if ($jenis->summary)<span class="p-muted"> — {{ $jenis->summary }}</span>@endif
                    </summary>
                    <div style="margin-top:10px">
                        @if ($jenis->image)<div class="p-detail-media" style="max-width:360px"><img src="{{ file_url($jenis->image) }}" alt=""></div>@endif
                        @if ($jenis->description)<p class="p-prose">{{ $jenis->description }}</p>@endif
                        @if ($jenis->duration_text)<p><strong>Durasi:</strong> {{ $jenis->duration_text }}</p>@endif
                        @if ($jenis->objectives)<p><strong>Tujuan:</strong></p><p class="p-prose">{{ $jenis->objectives }}</p>@endif
                        <a href="{{ route('lowongan.index', ['jenis' => $jenis->slug]) }}">Lihat lowongan {{ $jenis->name }} →</a>
                    </div>
                </details>
            @endforeach
        </div>
        <p class="p-card-subtitle" style="margin-top:16px">
            Berdasarkan penyelenggaranya, PDLK dilaksanakan melalui skema <strong>Flagship</strong>
            (program Kemdiktisaintek / Kampus Berdampak) dan <strong>Mandiri</strong>
            (diinisiasi program studi dan perguruan tinggi bersama mitra).
        </p>
    </div>

    <div class="p-card">
        <h2 class="p-card-title">Alur PDLK</h2>
        <ol style="margin:12px 0 0;padding-left:20px;line-height:1.9">
            <li>Mahasiswa membaca lowongan beserta kriterianya, lalu mendaftar melalui akun CIS.</li>
            <li>UPT PP-ESTEM meneruskan data pendaftar ke mitra. Seleksi dilakukan oleh mitra.</li>
            <li>Status pendaftaran: <strong>Terdaftar → Proses → Lulus / Tidak Lulus</strong>.</li>
            <li>Mahasiswa yang lulus otomatis tercatat sebagai mahasiswa aktif PDLK. Mahasiswa PDLK mandiri
                atau melalui tautan mitra didata oleh koordinator PDLK prodi atau UPT PP-ESTEM.</li>
            <li>Selama kegiatan, mahasiswa memperbarui progres dan mengunggah bukti pelaksanaan.</li>
            <li>Monev tengah diisi pada minggu ke-8 sampai ke-9, dilanjutkan monev akhir. Keduanya wajib.</li>
            <li>Setelah monev tengah dan akhir lengkap, status kegiatan menjadi <strong>Selesai</strong>.</li>
        </ol>
    </div>
    @if ($kontak->isNotEmpty())
    <div class="p-card">
        <h2 class="p-card-title">Kontak</h2>
        <dl class="p-dl" style="margin-top:12px">
            @foreach ($kontak as $k)
                <dt>{{ $k->icon }} {{ $k->name }}</dt>
                <dd>
                    @switch($k->contact_type)
                        @case('email') <a href="mailto:{{ $k->contact_value }}">{{ $k->contact_value }}</a> @break
                        @case('whatsapp') <a href="https://wa.me/{{ preg_replace('/\D/', '', preg_replace('/^0/', '62', $k->contact_value)) }}" target="_blank">{{ $k->contact_value }}</a> @break
                        @case('website') <a href="{{ $k->contact_value }}" target="_blank">{{ $k->contact_value }}</a> @break
                        @default {{ $k->contact_value }}
                    @endswitch
                </dd>
            @endforeach
        </dl>
    </div>
    @endif
</div>
@endsection
