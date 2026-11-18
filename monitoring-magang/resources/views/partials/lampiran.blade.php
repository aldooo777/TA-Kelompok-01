{{-- $lampiran: koleksi announcement_attachments --}}
@if ($lampiran->isNotEmpty())
    <div class="p-card">
        <h2 class="p-card-title">Lampiran</h2>
        <ul style="margin:12px 0 0;padding-left:20px;line-height:2">
            @foreach ($lampiran as $l)
                <li>
                    <a href="{{ file_url($l->file_path) }}" target="_blank" rel="noopener">{{ $l->file_name }}</a>
                    @if ($l->file_size)<span class="p-muted">({{ number_format($l->file_size / 1024, 0, ',', '.') }} KB)</span>@endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
