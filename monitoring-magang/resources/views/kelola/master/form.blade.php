@extends('layouts.pdlk')

@php
    $baru = $row === null;
@endphp

@section('title', ($baru ? 'Tambah ' : 'Ubah ') . $def['title'] . ' | Monitoring PDLK')
@section('breadcrumb', 'Data Master / ' . $def['title'])
@section('page-title', ($baru ? 'Tambah ' : 'Ubah ') . $def['title'])

@section('content')
<div class="p-stack">
    @include('partials.flash')

    <form method="POST" enctype="multipart/form-data" class="p-card"
          action="{{ $baru ? route('kelola.master.store', $def['key']) : route('kelola.master.update', [$def['key'], $row->id]) }}">
        @csrf
        @unless ($baru) @method('PUT') @endunless

        <div class="p-form-grid">
            @foreach ($def['fields'] as $kolom => $f)
                @php
                    $nilai = old($kolom, $row->$kolom ?? null);
                    $lebar = in_array($f['type'], ['textarea', 'image'], true) ? 'p-span-2' : '';
                @endphp
                <div class="p-field {{ $lebar }}">
                    <label for="{{ $kolom }}">
                        {{ $f['label'] }}
                        @if ($f['required'] ?? false) <span class="p-required">*</span> @endif
                    </label>

                    @switch($f['type'])
                        @case('textarea')
                            <textarea id="{{ $kolom }}" name="{{ $kolom }}" class="p-textarea" rows="5">{{ $nilai }}</textarea>
                            @break
                        @case('enum')
                            <select id="{{ $kolom }}" name="{{ $kolom }}" class="p-select">
                                <option value="">Pilih</option>
                                @foreach ($f['options'] as $v => $lbl)
                                    <option value="{{ $v }}" @selected((string) $nilai === (string) $v)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                            @break
                        @case('image')
                            <input type="file" id="{{ $kolom }}" name="{{ $kolom }}" accept="image/*" class="p-input">
                            @if (!$baru && $row->$kolom)
                                <div class="p-upload-preview"><img src="{{ file_url($row->$kolom) }}" alt=""></div>
                                <label class="p-check" style="margin-top:8px"><input type="checkbox" name="hapus_{{ $kolom }}" value="1"> Hapus gambar</label>
                            @endif
                            @break
                        @default
                            <input id="{{ $kolom }}" name="{{ $kolom }}" class="p-input"
                                   type="{{ ['number' => 'number', 'date' => 'date', 'url' => 'url'][$f['type']] ?? 'text' }}"
                                   value="{{ $f['type'] === 'date' && $nilai ? substr($nilai, 0, 10) : $nilai }}"
                                   @if (isset($f['max']) && $f['type'] === 'text') maxlength="{{ $f['max'] }}" @endif
                                   @if ($f['readonly'] ?? false) disabled @endif>
                    @endswitch

                    @if (isset($f['help']))<p class="p-help">{{ $f['help'] }}</p>@endif
                    @error($kolom)<p class="p-error">{{ $message }}</p>@enderror
                </div>
            @endforeach
        </div>

        <div class="p-form-actions">
            <a href="{{ route('kelola.master.index', $def['key']) }}" class="p-btn p-btn-light">← Kembali</a>
            <button class="p-btn p-btn-primary">{{ $baru ? 'Simpan' : 'Simpan Perubahan' }}</button>
        </div>
    </form>
</div>
@endsection
