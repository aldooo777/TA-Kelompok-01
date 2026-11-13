@if ($paginator->hasPages())
    <nav class="p-pagination" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="p-muted">‹ Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a>
        @endif
        <span class="active">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a>
        @else
            <span class="p-muted">Berikutnya ›</span>
        @endif
    </nav>
@endif
