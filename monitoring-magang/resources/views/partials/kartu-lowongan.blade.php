{{-- Kartu ringkas lowongan. Variabel: $item (Lowongan) --}}
<a href="{{ route('lowongan.show', $item->slug) }}" class="p-post">
    <div class="p-post-media">
        @if ($item->poster_url)
            <img src="{{ $item->poster_url }}" alt="Poster {{ $item->judul }}" loading="lazy">
        @else
            <span aria-hidden="true">💼</span>
        @endif
    </div>
    <div class="p-post-body">
        <div class="p-post-meta">
            <span>{{ $item->jenisKegiatan->nama ?? '-' }}</span>
            @if ($item->dibuka)
                <span class="p-badge p-badge-dibuka">Dibuka</span>
            @else
                <span class="p-badge p-badge-ditutup">Ditutup</span>
            @endif
        </div>
        <h3 class="p-post-title">{{ $item->judul }}</h3>
        <p class="p-post-text">
            <strong>{{ $item->mitra->nama ?? '-' }}</strong>
            @if ($item->lokasi) · {{ $item->lokasi }} @endif
        </p>
        @if ($item->tanggal_tutup)
            <p class="p-muted" style="margin:auto 0 0">Batas daftar: {{ $item->tanggal_tutup->translatedFormat('d F Y') }}</p>
        @endif
    </div>
</a>
