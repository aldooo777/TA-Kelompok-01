{{-- Kartu postingan ala feed. Variabel: $item (Postingan) --}}
<a href="{{ route('berita.show', $item->slug) }}" class="p-post">
    <div class="p-post-media">
        @if ($item->foto_url)
            <img src="{{ $item->foto_url }}" alt="{{ $item->judul }}" loading="lazy">
        @else
            <span aria-hidden="true">📰</span>
        @endif
    </div>
    <div class="p-post-body">
        <div class="p-post-meta">
            <span class="p-badge p-badge-selesai">{{ $item->jenis_label }}</span>
            <span>{{ $item->published_at?->translatedFormat('d F Y') }}</span>
        </div>
        <h3 class="p-post-title">{{ $item->judul }}</h3>
        <p class="p-post-text">{{ $item->ringkasan }}</p>
    </div>
</a>
