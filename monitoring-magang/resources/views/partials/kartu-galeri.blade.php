{{-- $item: baris galleries --}}
<a href="{{ route('galeri.show', $item->id) }}" class="p-post">
    <div class="p-post-media">
        @if ($item->cover_image)
            <img src="{{ file_url($item->cover_image) }}" alt="{{ $item->title }}" loading="lazy">
        @else
            <span aria-hidden="true">🖼️</span>
        @endif
    </div>
    <div class="p-post-body">
        <div class="p-post-meta"><span>{{ tgl($item->event_date) }}</span>
            @isset($item->jumlah_item)<span>{{ $item->jumlah_item }} foto/video</span>@endisset
        </div>
        <h3 class="p-post-title">{{ $item->title }}</h3>
        @if ($item->description)<p class="p-post-text">{{ ringkas($item->description, 120) }}</p>@endif
    </div>
</a>
