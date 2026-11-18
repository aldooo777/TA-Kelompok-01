@extends('layouts.pdlk')

@php
    $baru = $galeri === null;
@endphp

@section('title', ($baru ? 'Buat' : 'Ubah') . ' Galeri | Monitoring PDLK')
@section('breadcrumb', 'Informasi / Galeri')
@section('page-title', $baru ? 'Buat Galeri' : 'Ubah Galeri')

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-stack"
          action="{{ $baru ? route('kelola.galeri.store') : route('kelola.galeri.update', $galeri->id) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-card">
            <div class="p-form-grid">
                <div class="p-field p-span-2">
                    <label for="title">Judul kegiatan <span class="p-required">*</span></label>
                    <input id="title" name="title" class="p-input" required maxlength="200" value="{{ old('title', $galeri->title ?? '') }}">
                </div>
                <div class="p-field">
                    <label for="event_date">Tanggal kegiatan</label>
                    <input type="date" id="event_date" name="event_date" class="p-input" value="{{ old('event_date', $galeri->event_date ?? '') }}">
                </div>
                <div class="p-field">
                    <label for="status">Status <span class="p-required">*</span></label>
                    <select id="status" name="status" class="p-select">
                        @foreach (\App\Support\Label::GALLERY_STATUS as $v => $l)
                            <option value="{{ $v }}" @selected(old('status', $galeri->status ?? 'published') === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-field p-span-2">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="p-textarea" rows="4">{{ old('description', $galeri->description ?? '') }}</textarea>
                </div>
                <div class="p-field">
                    <label for="cover">Foto sampul</label>
                    <input type="file" id="cover" name="cover" accept="image/*" class="p-input">
                    @if (!$baru && $galeri->cover_image)
                        <div class="p-upload-preview"><img src="{{ file_url($galeri->cover_image) }}" alt=""></div>
                    @endif
                </div>
                <div class="p-field">
                    <label for="items">Tambah foto / video <span class="p-optional">(bisa pilih banyak, maks. 20 MB per file)</span></label>
                    <input type="file" id="items" name="items[]" accept="image/*,video/mp4" class="p-input" multiple>
                </div>
            </div>
        </div>

        @if ($items->isNotEmpty())
            <div class="p-card">
                <h2 class="p-card-title">Isi Galeri ({{ $items->count() }})</h2>
                <div class="p-grid-cards" style="margin-top:16px">
                    @foreach ($items as $item)
                        <div class="p-post" style="cursor:default">
                            <div class="p-post-media">
                                @if ($item->media_type === 'video')
                                    <video src="{{ file_url($item->file_path) }}" controls style="width:100%;height:100%;object-fit:cover"></video>
                                @else
                                    <img src="{{ file_url($item->file_path) }}" alt="{{ $item->caption }}">
                                @endif
                            </div>
                            <div class="p-post-body">
                                <input name="caption[{{ $item->id }}]" class="p-input" value="{{ $item->caption }}" placeholder="Keterangan (opsional)">
                                <button type="submit" form="hapus-{{ $item->id }}" class="p-btn p-btn-danger p-btn-sm">Hapus item</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-form-actions" style="border:0;margin:0;padding:0">
            <a href="{{ route('kelola.galeri.index') }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan Galeri' : 'Simpan Perubahan' }}</button>
        </div>
    </form>

    @foreach ($items as $item)
        <form id="hapus-{{ $item->id }}" method="POST" action="{{ route('kelola.galeri.item.destroy', [$galeri->id, $item->id]) }}"
              onsubmit="return confirm('Hapus item ini?')">@csrf @method('DELETE')</form>
    @endforeach
</div>
@endsection
