{{--
    Staff selector: checkbox list + opsi "Lainnya (Ketik Manual)"
    Props:
        $fieldName    — e.g. 'counselor_id'   (tanpa [])
        $manualField  — e.g. 'counselor_name'
        $label        — teks label
        $users        — collection User
        $currentId    — id user terpilih saat ini (edit)
        $currentName  — nama manual saat ini (edit)
        $currentExtras— array extra names (edit)
        $multi        — bool, true = checkbox multi, false = radio single
--}}
@php
    $multi       = $multi ?? false;
    $currentId   = $currentId ?? null;
    $currentName = $currentName ?? null;
    $currentExtras = !empty($currentExtras) ? (array)$currentExtras : [];

    // Old input
    $oldIds   = old($fieldName, $currentId ? [$currentId] : []);
    if (!is_array($oldIds)) $oldIds = [$oldIds];
    $oldManual = old($manualField, $currentName ?? '');
    $showManual = in_array('other', $oldIds) || (!empty($currentName) && !$currentId);
    if ($showManual && !in_array('other', $oldIds)) $oldIds[] = 'other';

    $uid = 'staff_' . str_replace([' ','/'], '_', $fieldName);
@endphp

<label class="form-label fw-semibold mb-2">
    {{ $label }}
    @if($multi)
        <span class="text-muted fw-normal ms-1" style="font-size:.72rem">(pilih satu atau lebih)</span>
    @endif
</label>

<div class="border rounded-3 p-3" style="background:#fafbfc" id="{{ $uid }}_wrap">

    {{-- Daftar user sebagai checkbox/radio --}}
    @foreach($users as $u)
    <div class="form-check mb-1">
        <input class="form-check-input" type="{{ $multi ? 'checkbox' : 'radio' }}"
            name="{{ $fieldName }}{{ $multi ? '[]' : '' }}"
            id="{{ $uid }}_{{ $u->id }}"
            value="{{ $u->id }}"
            {{ in_array($u->id, array_map('strval', $oldIds)) ? 'checked' : '' }}
            onchange="{{ $multi ? "staffCheckOther('{$uid}')" : "staffRadioOther('{$uid}')" }}">
        <label class="form-check-label" for="{{ $uid }}_{{ $u->id }}">
            {{ $u->name }}
            @if($u->jabatan)
                <small class="text-muted ms-1">— {{ $u->jabatan }}</small>
            @endif
        </label>
    </div>
    @endforeach

    {{-- Opsi Lainnya --}}
    <div class="form-check mb-0 mt-2 pt-2 border-top">
        <input class="form-check-input" type="{{ $multi ? 'checkbox' : 'radio' }}"
            name="{{ $fieldName }}{{ $multi ? '[]' : '' }}"
            id="{{ $uid }}_other"
            value="other"
            {{ $showManual ? 'checked' : '' }}
            onchange="{{ $multi ? "staffCheckOther('{$uid}')" : "staffRadioOther('{$uid}')" }}">
        <label class="form-check-label fw-semibold text-primary" for="{{ $uid }}_other">
            <i class="bi bi-pencil-fill me-1" style="font-size:.75rem"></i>Lainnya (Ketik Manual)
        </label>
    </div>

    {{-- Input manual — muncul saat "Lainnya" dipilih --}}
    <div id="{{ $uid }}_manual" class="{{ $showManual ? 'mt-2' : 'd-none mt-2' }}">
        <input type="text" name="{{ $manualField }}"
            class="form-control form-control-sm"
            placeholder="Ketik nama lengkap..."
            value="{{ $oldManual }}">
        <div class="form-text" style="font-size:.68rem">
            <i class="bi bi-info-circle me-1"></i>Nama ini akan dicatat jika tidak ada di daftar akun
        </div>
    </div>

    {{-- Tampilkan extra names yang sudah tersimpan (saat edit) --}}
    @if(!empty($currentExtras))
    <div class="mt-2 pt-2 border-top">
        <small class="text-muted d-block mb-1">Staf tambahan tersimpan:</small>
        <div class="d-flex flex-wrap gap-1">
            @foreach($currentExtras as $e)
            <span class="badge bg-light text-dark border" style="font-size:.75rem">
                <i class="bi bi-person-fill me-1 text-muted"></i>{{ $e }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

</div>
