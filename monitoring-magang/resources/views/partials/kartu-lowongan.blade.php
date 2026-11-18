{{-- Kartu ringkas lowongan. $item: baris dari Query::LOWONGAN; $portal: dibuka dari dashboard --}}
<a href="{{ route(($portal ?? false) ? 'portal.lowongan.show' : 'lowongan.show', $item->slug) }}" class="p-post">
    <div class="p-post-media">
        @if ($item->cover_image)
            <img src="{{ file_url($item->cover_image) }}" alt="Poster {{ $item->title }}" loading="lazy">
        @else
            <span aria-hidden="true">💼</span>
        @endif
    </div>
    <div class="p-post-body">
        <div class="p-post-meta">
            <span>{{ $item->activity_name }}</span>
            @if (lowongan_dibuka($item))
                <span class="p-badge p-badge-dibuka">Dibuka</span>
            @else
                <span class="p-badge p-badge-ditutup">Ditutup</span>
            @endif
            @if ($item->registration_type === 'external')
                <span class="p-badge p-badge-draft">Daftar di situs mitra</span>
            @endif
        </div>
        <h3 class="p-post-title">{{ $item->title }}</h3>
        <p class="p-post-text">
            <strong>{{ $item->partner_name }}</strong>
            @if ($item->location) · {{ $item->location }} @endif
        </p>
        @if ($item->registration_deadline)
            <p class="p-muted" style="margin:auto 0 0">Batas daftar: {{ tgl($item->registration_deadline) }}</p>
        @endif
    </div>
</a>
