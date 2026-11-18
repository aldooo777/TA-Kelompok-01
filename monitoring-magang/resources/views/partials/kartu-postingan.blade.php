{{-- Kartu berita ala feed. $item: baris announcements (type news) --}}
<a href="{{ route('berita.show', $item->slug) }}" class="p-post">
    <div class="p-post-media">
        @if ($item->cover_image)
            <img src="{{ file_url($item->cover_image) }}" alt="{{ $item->title }}" loading="lazy">
        @else
            <span aria-hidden="true">📰</span>
        @endif
    </div>
    <div class="p-post-body">
        <div class="p-post-meta">
            <span class="p-badge p-badge-completed">Berita</span>
            <span>{{ tgl($item->published_at) }}</span>
        </div>
        <h3 class="p-post-title">{{ $item->title }}</h3>
        <p class="p-post-text">{{ $item->summary ?: ringkas($item->content) }}</p>
    </div>
</a>
