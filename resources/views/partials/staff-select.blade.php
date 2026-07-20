{{--
    Reusable staff dropdown dengan opsi "Lainnya (Ketik Manual)" dan multi-select.
    Props:
        $fieldName    — nama field, e.g. 'counselor_id'
        $manualField  — nama field manual, e.g. 'counselor_name'
        $label        — label teks, e.g. 'Guru BK / Konselor'
        $users        — collection User
        $currentId    — nilai id saat ini (untuk edit)
        $currentName  — nilai name manual saat ini (untuk edit)
        $currentExtras— array extra names saat ini (untuk edit, multi)
        $multi        — bool, true jika multi-select (default false)
        $manualId     — HTML id untuk input manual (default: fieldName + 'Manual')
--}}
@php
    $multi      = $multi ?? false;
    $manualId   = $manualId ?? str_replace(['[',']'], '', $fieldName) . 'Manual';
    $selectId   = str_replace(['[',']'], '', $fieldName) . 'Sel';
    $currentId  = $currentId ?? null;
    $currentName= $currentName ?? null;
    $currentExtras = $currentExtras ?? [];

    // Determine if "other" should be pre-selected
    $isOther = $currentName && !$currentId;

    if ($multi) {
        // Build selected values for multi
        $selectedIds = old(str_replace('[]','', $fieldName), []);
        if (empty($selectedIds) && $currentId) $selectedIds = [$currentId];
        if (empty($selectedIds) && $isOther)   $selectedIds = ['other'];
    } else {
        $selectedVal = old(str_replace('[]','', $fieldName), $isOther ? 'other' : ($currentId ?? ''));
    }
@endphp

<label class="form-label fw-semibold">
    {{ $label }}
    @if($multi)
    <span class="badge bg-info text-dark ms-1" style="font-size:.65rem;vertical-align:middle">Bisa lebih dari 1</span>
    @endif
</label>

@if($multi)
<select name="{{ $fieldName }}" id="{{ $selectId }}" class="form-select"
    multiple size="4"
    onchange="staffToggleManualMulti(this,'{{ $manualId }}')">
    @foreach($users as $u)
    <option value="{{ $u->id }}"
        {{ in_array($u->id, (array)$selectedIds) ? 'selected' : '' }}>
        {{ $u->name }}
    </option>
    @endforeach
    <option value="other" {{ in_array('other', (array)$selectedIds) ? 'selected' : '' }}>
        ✏️ Lainnya (Ketik Manual)
    </option>
</select>
<div class="form-text" style="font-size:.7rem">Ctrl+klik untuk pilih lebih dari satu</div>

{{-- Show extra names as chips if editing --}}
@if(!empty($currentExtras))
<div class="mt-1 d-flex flex-wrap gap-1">
    @foreach($currentExtras as $e)
    <span class="badge bg-light text-dark border" style="font-size:.75rem">{{ $e }}</span>
    @endforeach
</div>
@endif

@else
<select name="{{ $fieldName }}" id="{{ $selectId }}" class="form-select"
    onchange="staffToggleManual(this,'{{ $manualId }}')">
    <option value="">-- Pilih --</option>
    @foreach($users as $u)
    <option value="{{ $u->id }}"
        {{ $selectedVal == $u->id ? 'selected' : '' }}>
        {{ $u->name }}
    </option>
    @endforeach
    <option value="other" {{ $selectedVal === 'other' ? 'selected' : '' }}>
        ✏️ Lainnya (Ketik Manual)
    </option>
</select>
@endif

<input type="text" name="{{ $manualField }}" id="{{ $manualId }}"
    class="form-control mt-2 {{ ($isOther || in_array('other', old(str_replace('[]','', $fieldName), []))) ? '' : 'd-none' }}"
    placeholder="Ketik nama..."
    value="{{ old($manualField, $currentName) }}">
