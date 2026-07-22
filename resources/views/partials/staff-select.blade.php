{{--
    Staff selector: tombol dropdown + checkbox/radio di dalam panel collapsible
    Props:
        $fieldName    — e.g. 'counselor_id'
        $manualField  — e.g. 'counselor_name'
        $label        — teks label
        $users        — collection User
        $currentId    — id user terpilih saat ini (edit), atau array jika multi
        $currentName  — nama manual saat ini (edit)
        $currentExtras— array extra names (edit)
        $multi        — bool, true = checkbox (lebih dari 1), false = radio (satu)
--}}
@php
    $multi         = $multi ?? false;
    $currentId     = $currentId ?? null;
    $currentName   = $currentName ?? null;
    $currentExtras = !empty($currentExtras) ? (array)$currentExtras : [];

    // Old input
    $oldIds = old($fieldName, $currentId ? (array)$currentId : []);
    if (!is_array($oldIds)) $oldIds = array_filter([$oldIds]);
    $oldManual  = old($manualField, $currentName ?? '');
    $showManual = in_array('other', $oldIds) || (!empty($currentName) && !$currentId);

    // Build label preview (siapa yang sudah dipilih)
    $previewNames = [];
    foreach ($users as $u) {
        if (in_array((string)$u->id, array_map('strval', $oldIds))) {
            $previewNames[] = $u->name;
        }
    }
    if ($showManual && $oldManual) $previewNames[] = $oldManual . ' (manual)';
    if (!empty($currentExtras)) {
        foreach ($currentExtras as $e) $previewNames[] = $e;
    }
    $previewText = !empty($previewNames) ? implode(', ', $previewNames) : 'Belum dipilih';

    $uid = 'stfsel_' . preg_replace('/[^a-z0-9]/i', '_', $fieldName) . '_' . substr(md5($fieldName.$label), 0, 4);
@endphp

@if($label)
<label class="form-label fw-semibold mb-1">
    {{ $label }}
    @if($multi)
    <span class="text-muted fw-normal" style="font-size:.7rem">(boleh lebih dari satu)</span>
    @endif
</label>
@endif

{{-- Tombol toggle — tampilkan siapa yang dipilih --}}
<div class="stf-wrap" style="position:relative">
<button type="button"
    class="btn btn-light border w-100 text-start d-flex align-items-center justify-content-between gap-2"
    style="min-height:38px;font-size:.875rem"
    id="{{ $uid }}_btn"
    onclick="staffTogglePanel('{{ $uid }}')">
    <span id="{{ $uid }}_preview" class="text-truncate" style="flex:1">
        {{ $previewText }}
    </span>
    <i class="bi bi-chevron-down flex-shrink-0" id="{{ $uid }}_chevron" style="font-size:.7rem;transition:transform .2s"></i>
</button>

{{-- Panel pilihan — pakai position:fixed agar tidak terpotong parent --}}
<div id="{{ $uid }}_panel"
     class="stf-panel"
     style="display:none; position:fixed; min-width:320px; max-width:520px;
            background:#fff; border:1px solid #dee2e6; border-radius:.5rem;
            max-height:300px; overflow-y:auto;
            box-shadow:0 8px 24px rgba(0,0,0,.15); z-index:9999;">

    <div class="p-2">
        {{-- Search filter jika user > 3 --}}
        @if($users->count() > 3)
        <div class="mb-2">
            <input type="text" class="form-control form-control-sm"
                placeholder="Cari nama..."
                oninput="staffFilter('{{ $uid }}', this.value)">
        </div>
        @endif

        {{-- Daftar user --}}
        @foreach($users as $u)
        <div class="d-flex align-items-center gap-2 px-2 py-1 rounded stf-item-{{ $uid }}"
             data-name="{{ strtolower($u->name) }}">
            <input class="form-check-input mt-0 flex-shrink-0" type="{{ $multi ? 'checkbox' : 'radio' }}"
                name="{{ $fieldName }}{{ $multi ? '[]' : '' }}"
                id="{{ $uid }}_{{ $u->id }}"
                value="{{ $u->id }}"
                {{ in_array((string)$u->id, array_map('strval', $oldIds)) ? 'checked' : '' }}
                onchange="staffUpdatePreview('{{ $uid }}')">
            <label class="form-check-label d-flex align-items-center gap-1 flex-wrap" for="{{ $uid }}_{{ $u->id }}" style="cursor:pointer;font-size:.875rem;line-height:1.3">
                <span>{{ $u->name }}</span>
                @if(!empty($u->jabatan))
                <span class="text-muted" style="font-size:.72rem;white-space:nowrap">{{ $u->jabatan }}</span>
                @endif
            </label>
        </div>
        @endforeach

        <hr class="my-1">

        {{-- Opsi Lainnya --}}
        <div class="d-flex align-items-center gap-2 px-2 py-1 rounded">
            <input class="form-check-input mt-0 flex-shrink-0" type="{{ $multi ? 'checkbox' : 'radio' }}"
                name="{{ $fieldName }}{{ $multi ? '[]' : '' }}"
                id="{{ $uid }}_other"
                value="other"
                {{ $showManual ? 'checked' : '' }}
                onchange="staffUpdatePreview('{{ $uid }}'); staffOtherToggle('{{ $uid }}')">
            <label class="form-check-label text-primary fw-semibold" for="{{ $uid }}_other" style="cursor:pointer;font-size:.875rem">
                <i class="bi bi-pencil-fill me-1" style="font-size:.7rem"></i>Lainnya (Ketik Manual)
            </label>
        </div>

        {{-- Input manual --}}
        <div id="{{ $uid }}_manual" class="px-2 pb-1 {{ $showManual ? '' : 'd-none' }}">
            <input type="text" name="{{ $manualField }}"
                class="form-control form-control-sm"
                placeholder="Ketik nama lengkap..."
                value="{{ $oldManual }}"
                oninput="staffUpdatePreview('{{ $uid }}')">
        </div>

        {{-- Extra names chip (saat edit) --}}
        @if(!empty($currentExtras))
        <div class="px-2 pb-2 pt-1">
            <small class="text-muted d-block mb-1" style="font-size:.7rem">Tersimpan sebelumnya:</small>
            <div class="d-flex flex-wrap gap-1">
                @foreach($currentExtras as $e)
                <span class="badge bg-light text-dark border" style="font-size:.72rem">{{ $e }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
</div>{{-- /position:relative wrapper --}}
