{{--
    Partial untuk menampilkan nama staf dengan fallback ke manual name + extra staff
    Usage: @include('partials.staff-name', ['primary' => $r->counselor, 'manualName' => $r->counselor_name, 'extras' => $r->extra_counselors])
--}}
@php
    $names = [];
    if (!empty($primary?->name)) $names[] = $primary->name;
    elseif (!empty($manualName))  $names[] = $manualName;
    if (!empty($extras) && is_array($extras)) {
        foreach ($extras as $e) { if (!empty($e)) $names[] = $e; }
    }
    $display = !empty($names) ? implode(', ', $names) : '-';
@endphp
<span title="{{ $display }}">{{ $display }}</span>
