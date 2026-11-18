{{-- $x: baris monev_questions atau null; $opsi: options ter-decode; $aksi: URL form --}}
@php
    use App\Support\Label;
    $uid = $x?->id ?? 'baru';
    $tipe = old('question_type', $x?->question_type ?? 'textarea');
@endphp
<form method="POST" action="{{ $aksi }}" style="margin-top:12px" data-form-pertanyaan>
    @csrf
    @if ($x) @method('PUT') @endif
    <div class="p-form-grid">
        <div class="p-field">
            <label for="section_{{ $uid }}">Bagian <span class="p-optional">(opsional)</span></label>
            <input id="section_{{ $uid }}" name="section_title" class="p-input" value="{{ $x?->section_title }}" placeholder="Contoh: Kegiatan selama PDLK">
        </div>
        <div class="p-field">
            <label for="type_{{ $uid }}">Tipe jawaban</label>
            <select id="type_{{ $uid }}" name="question_type" class="p-select" data-tipe>
                @foreach (Label::QUESTION_TYPE as $v => $l)
                    <option value="{{ $v }}" @selected($tipe === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="p-field p-span-2">
            <label for="text_{{ $uid }}">Pertanyaan <span class="p-required">*</span></label>
            <textarea id="text_{{ $uid }}" name="question_text" class="p-textarea" rows="2" required style="min-height:70px">{{ $x?->question_text }}</textarea>
        </div>
        <div class="p-field p-span-2" data-box="choice" @if ($tipe !== 'choice') hidden @endif>
            <label for="pilihan_{{ $uid }}">Pilihan jawaban (satu per baris)</label>
            <textarea id="pilihan_{{ $uid }}" name="pilihan" class="p-textarea" rows="4" style="min-height:90px">{{ $x?->question_type === 'choice' ? implode("\n", (array) $opsi) : '' }}</textarea>
        </div>
        <div class="p-field" data-box="scale" @if ($tipe !== 'scale') hidden @endif>
            <label>Rentang skala</label>
            <div style="display:flex;gap:8px;align-items:center">
                <input type="number" name="skala_min" class="p-input" min="0" max="10" value="{{ $opsi['min'] ?? 1 }}" style="max-width:90px">
                <span>sampai</span>
                <input type="number" name="skala_max" class="p-input" min="1" max="10" value="{{ $opsi['max'] ?? 5 }}" style="max-width:90px">
            </div>
        </div>
        <div class="p-field">
            <label for="order_{{ $uid }}">Urutan</label>
            <input type="number" id="order_{{ $uid }}" name="display_order" class="p-input" min="0" value="{{ $x?->display_order }}" placeholder="Otomatis">
        </div>
        <div class="p-field" style="align-self:end">
            <input type="hidden" name="is_required" value="0">
            <label class="p-check"><input type="checkbox" name="is_required" value="1" @checked($x?->is_required ?? true)> Wajib diisi</label>
        </div>
    </div>
    <div style="margin-top:12px"><button class="p-btn p-btn-primary p-btn-sm">{{ $x ? 'Simpan Perubahan' : '+ Tambah Pertanyaan' }}</button></div>
</form>
@once
<script>
    document.addEventListener('change', function (e) {
        if (!e.target.matches('[data-tipe]')) return;
        const form = e.target.closest('[data-form-pertanyaan]');
        form.querySelectorAll('[data-box]').forEach(b => b.hidden = b.dataset.box !== e.target.value);
    });
</script>
@endonce
