@extends('layouts.app')
@section('title', 'Input Cepat')

@push('styles')
<style>
/* ── Type selector cards ── */
.type-card {
    border: 2px solid #e9ecef;
    border-radius: .75rem;
    padding: .875rem .5rem;
    cursor: pointer;
    transition: border-color .15s, background .15s, transform .1s;
    text-align: center;
    background: #fff;
    user-select: none;
}
.type-card:hover  { border-color: #6ea8fe; background: #f0f6ff; transform: translateY(-2px); }
.type-card.selected { border-color: #0d6efd; background: #e7f1ff; box-shadow: 0 0 0 3px rgba(13,110,253,.12); }
.type-card .tc-icon { font-size: 1.75rem; line-height: 1; margin-bottom: .35rem; }
.type-card .tc-label { font-size: .78rem; font-weight: 600; line-height: 1.2; }
[data-bs-theme="dark"] .type-card          { background: #2c3034; border-color: #495057; }
[data-bs-theme="dark"] .type-card.selected { background: #1a3a5c; border-color: #0d6efd; }

/* ── Student autocomplete ── */
.student-autocomplete { position: relative; }
.student-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; margin-top: 2px;
    background: #fff; border: 1px solid #dee2e6; border-radius: .6rem;
    box-shadow: 0 6px 20px rgba(0,0,0,.13); z-index: 1050;
    max-height: 280px; overflow-y: auto; display: none;
}
[data-bs-theme="dark"] .student-dropdown { background: #2c3034; border-color: #495057; }
.student-dropdown .sd-item {
    padding: .55rem 1rem; cursor: pointer;
    border-bottom: 1px solid #f2f2f2;
    display: flex; align-items: center; gap: .65rem;
}
[data-bs-theme="dark"] .sd-item { border-color: #3d4246; }
.sd-item:hover { background: #f0f6ff; }
[data-bs-theme="dark"] .sd-item:hover { background: #1a3a5c; }
.sd-item:last-child { border-bottom: none; }
.sd-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: #0d6efd; color: #fff; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; font-weight: 700;
}
.sd-name { font-weight: 600; font-size: .84rem; line-height: 1.2; }
.sd-meta { font-size: .72rem; color: #6c757d; }
[data-bs-theme="dark"] .sd-meta { color: #9fa8b3; }

/* ── Selected student badge ── */
.student-selected {
    display: none; align-items: center; gap: .6rem;
    background: #e7f1ff; border: 1px solid #b6d4fe; border-radius: .6rem;
    padding: .5rem .85rem; margin-top: .4rem;
}
[data-bs-theme="dark"] .student-selected { background: #1a3a5c; border-color: #0d6efd; }

/* ── Form section steps ── */
.form-section { display: none; }
.form-section.active { display: block; }

/* ── Step badge ── */
.step-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px; border-radius: 50%;
    font-size: .72rem; font-weight: 700; color: #fff;
    flex-shrink: 0; margin-right: .4rem;
}
</style>
@endpush

@section('content')

{{-- Flash success --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-3 rounded-3" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="page-header mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Input Cepat</h4>
        <small class="text-muted">Input semua jenis laporan dalam satu halaman</small>
    </div>
</div>

<form method="POST" action="{{ route('quick-entry.store') }}" enctype="multipart/form-data" id="quickForm">
@csrf
<input type="hidden" name="type" id="selectedType" value="{{ old('type','') }}">

{{-- ══════════ STEP 1 – Pilih Jenis Laporan ══════════ --}}
<div class="form-card mb-3">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#0d6efd">1</span>
        <span class="fw-bold">Pilih Jenis Laporan</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        @php
        $types = [
            ['late',           'bi-clock-history',      '#ffc107', 'Keterlambatan'],
            ['violation',      'bi-shield-exclamation', '#dc3545', 'Pelanggaran'],
            ['counseling',     'bi-chat-heart-fill',    '#198754', 'Konseling'],
            ['parent_meeting', 'bi-people-fill',        '#0dcaf0', 'Pemanggilan Ortu'],
            ['home_visit',     'bi-house-fill',         '#6f42c1', 'Home Visit'],
        ];
        @endphp
        @foreach($types as [$val, $icon, $color, $label])
        <div style="flex:1;min-width:90px;max-width:160px">
            <div class="type-card h-100 {{ old('type') == $val ? 'selected' : '' }}"
                 onclick="selectType('{{ $val }}', this)">
                <div class="tc-icon">
                    <i class="bi {{ $icon }}" style="color:{{ $color }}"></i>
                </div>
                <div class="tc-label">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @error('type')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>

{{-- ══════════ STEP 2 – Pilih Siswa ══════════ --}}
<div class="form-card mb-3" id="studentSection" style="{{ old('type') ? '' : 'display:none' }}">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#0d6efd">2</span>
        <span class="fw-bold">Pilih Siswa</span>
    </div>
    <div class="row g-2">
        <div class="col-12 col-md-7">
            <div class="student-autocomplete">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="studentSearch" class="form-control"
                        placeholder="Ketik nama atau NIS siswa..." autocomplete="off"
                        value="{{ old('_student_name','') }}">
                </div>
                <input type="hidden" name="student_id" id="studentId" value="{{ old('student_id','') }}">
                <div class="student-dropdown" id="studentDropdown"></div>
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="student-selected" id="studentBadge">
                <div class="sd-avatar" id="badgeAvatar">?</div>
                <div class="flex-grow-1 min-w-0">
                    <div class="sd-name" id="badgeName">–</div>
                    <div class="sd-meta" id="badgeMeta">–</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearStudent()" title="Ganti siswa">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
    @error('student_id')<div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
</div>

{{-- ══════════ STEP 3 – Form Detail ══════════ --}}
<div id="formSection" style="{{ old('type') ? '' : 'display:none' }}">

{{-- ─── KETERLAMBATAN ─── --}}
<div class="form-card mb-3 form-section {{ old('type')=='late' ? 'active' : '' }}" id="form-late">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#ffc107">3</span>
        <span class="fw-bold">Detail Keterlambatan</span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-3">
            <label class="form-label form-label-sm fw-semibold mb-1">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ old('date', date('Y-m-d')) }}">
            @error('date')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Jam Datang</label>
            <input type="time" name="arrive_time" id="lateArrive" class="form-control form-control-sm" value="{{ old('arrive_time') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Jam Masuk</label>
            <input type="time" name="entry_time" id="lateEntry" class="form-control form-control-sm" value="{{ old('entry_time') }}">
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Durasi (mnt)</label>
            <input type="number" name="duration_minutes" id="lateDuration" class="form-control form-control-sm"
                min="1" value="{{ old('duration_minutes') }}" placeholder="Auto">
            <div class="form-text" style="font-size:.68rem">Otomatis dari jam</div>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label form-label-sm fw-semibold mb-1">Dicatat Oleh</label>
            <select name="officer_id" id="officerSel" class="form-select form-select-sm"
                onchange="toggleManual(this,'officerManual')">
                <option value="">-- Pilih --</option>
                @foreach($officers as $u)
                <option value="{{ $u->id }}" {{ old('officer_id', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
                <option value="other" {{ old('officer_id')==='other' ? 'selected':'' }}>✏️ Lainnya (Ketik Manual)</option>
            </select>
            <input type="text" name="officer_name" id="officerManual"
                class="form-control form-control-sm mt-1 {{ old('officer_id')==='other' ? '' : 'd-none' }}"
                placeholder="Nama pencatat..." value="{{ old('officer_name') }}">
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label form-label-sm fw-semibold mb-1">Alasan</label>
            <input type="text" name="reason" class="form-control form-control-sm"
                value="{{ old('reason') }}" placeholder="Alasan keterlambatan...">
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label form-label-sm fw-semibold mb-1">Catatan Tambahan</label>
            <input type="text" name="notes" class="form-control form-control-sm"
                value="{{ old('notes') }}" placeholder="Catatan tambahan...">
        </div>
    </div>
</div>

{{-- ─── PELANGGARAN ─── --}}
<div class="form-card mb-3 form-section {{ old('type')=='violation' ? 'active' : '' }}" id="form-violation">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#dc3545">3</span>
        <span class="fw-bold">Detail Pelanggaran</span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ old('date', date('Y-m-d')) }}">
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label form-label-sm fw-semibold mb-1">Jenis Pelanggaran <span class="text-danger">*</span></label>
            <select name="violation_category_id" id="qViolationCat" class="form-select form-select-sm">
                <option value="">-- Pilih Jenis --</option>
                @foreach($violationCategories as $vc)
                <option value="{{ $vc->id }}" data-points="{{ $vc->points ?? 0 }}"
                    {{ old('violation_category_id') == $vc->id ? 'selected' : '' }}>
                    {{ $vc->name }} ({{ ucfirst($vc->category) }})
                </option>
                @endforeach
                <option value="other" data-points="0" {{ old('violation_category_id')==='other' ? 'selected':'' }}>
                    Pelanggaran Lainnya
                </option>
            </select>
            @error('violation_category_id')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Poin <span class="text-danger">*</span></label>
            <input type="number" name="points" id="qViolationPts" class="form-control form-control-sm"
                min="0" value="{{ old('points',0) }}">
            @error('points')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label form-label-sm fw-semibold mb-1">Pelapor</label>
            <select name="reporter_id" id="reporterSel" class="form-select form-select-sm"
                onchange="toggleManual(this,'reporterManual')">
                <option value="">-- Pilih --</option>
                @foreach($officers as $u)
                <option value="{{ $u->id }}" {{ old('reporter_id', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
                <option value="other" {{ old('reporter_id')==='other' ? 'selected':'' }}>✏️ Lainnya (Ketik Manual)</option>
            </select>
            <input type="text" name="reporter_name" id="reporterManual"
                class="form-control form-control-sm mt-1 {{ old('reporter_id')==='other' ? '' : 'd-none' }}"
                placeholder="Nama pelapor..." value="{{ old('reporter_name') }}">
        </div>

        {{-- Pelanggaran lainnya --}}
        <div class="col-12" id="qOtherViolation" style="{{ old('violation_category_id')==='other' ? '':'display:none' }}">
            <div class="border rounded-3 p-3 bg-light">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <label class="form-label form-label-sm fw-semibold mb-1">Nama Pelanggaran <span class="text-danger">*</span></label>
                        <input type="text" name="other_violation_name" id="qOtherName"
                            class="form-control form-control-sm" value="{{ old('other_violation_name') }}"
                            placeholder="Nama pelanggaran...">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label form-label-sm fw-semibold mb-1">Kategori <span class="text-danger">*</span></label>
                        <select name="other_violation_category" id="qOtherCat" class="form-select form-select-sm">
                            <option value="">Pilih...</option>
                            <option value="ringan" {{ old('other_violation_category')==='ringan'?'selected':'' }}>Ringan</option>
                            <option value="sedang" {{ old('other_violation_category')==='sedang'?'selected':'' }}>Sedang</option>
                            <option value="berat"  {{ old('other_violation_category')==='berat' ?'selected':'' }}>Berat</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label form-label-sm fw-semibold mb-1">Poin</label>
                        <input type="number" name="other_violation_points" id="qOtherPts"
                            class="form-control form-control-sm" value="{{ old('other_violation_points',0) }}" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <label class="form-label form-label-sm fw-semibold mb-1">Kronologi / Deskripsi</label>
            <textarea name="description" class="form-control form-control-sm" rows="2"
                placeholder="Kronologi kejadian...">{{ old('description') }}</textarea>
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label form-label-sm fw-semibold mb-1">Catatan</label>
            <input type="text" name="notes" class="form-control form-control-sm" value="{{ old('notes') }}">
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label form-label-sm fw-semibold mb-1">Foto Bukti</label>
            <input type="file" name="evidence_photo" class="form-control form-control-sm" accept="image/*">
        </div>
    </div>
</div>

{{-- ─── KONSELING ─── --}}
<div class="form-card mb-3 form-section {{ old('type')=='counseling' ? 'active' : '' }}" id="form-counseling">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#198754">3</span>
        <span class="fw-bold">Detail Konseling</span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ old('date', date('Y-m-d')) }}">
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">
                Guru BK
                <span class="badge bg-info text-dark ms-1" style="font-size:.6rem">Bisa lebih dari 1</span>
            </label>
            <select name="counselor_id[]" id="counselorSel" class="form-select form-select-sm" multiple size="4"
                onchange="toggleManualMulti(this,'counselorManual')">
                @foreach($counselors as $u)
                <option value="{{ $u->id }}" {{ collect(old('counselor_id',[])) ->contains($u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
                <option value="other" {{ in_array('other', old('counselor_id',[])) ? 'selected':'' }}>✏️ Lainnya (Ketik Manual)</option>
            </select>
            <div class="form-text" style="font-size:.68rem">Ctrl+klik untuk pilih lebih dari satu</div>
            <input type="text" name="counselor_name" id="counselorManual"
                class="form-control form-control-sm mt-1 {{ in_array('other', old('counselor_id',[])) ? '' : 'd-none' }}"
                placeholder="Nama guru lainnya..." value="{{ old('counselor_name') }}">
        </div>
        <div class="col-12">
            <label class="form-label form-label-sm fw-semibold mb-1">Masalah / Topik <span class="text-danger">*</span></label>
            <textarea name="problem" class="form-control form-control-sm" rows="2"
                placeholder="Uraikan masalah yang dikonseling...">{{ old('problem') }}</textarea>
            @error('problem')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Hasil Konseling</label>
            <textarea name="result" class="form-control form-control-sm" rows="2" placeholder="Hasil konseling...">{{ old('result') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Solusi</label>
            <textarea name="solution" class="form-control form-control-sm" rows="2" placeholder="Solusi yang diberikan...">{{ old('solution') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Tindak Lanjut</label>
            <textarea name="follow_up" class="form-control form-control-sm" rows="2" placeholder="Rencana tindak lanjut...">{{ old('follow_up') }}</textarea>
        </div>
    </div>
</div>

{{-- ─── PEMANGGILAN ORANG TUA ─── --}}
<div class="form-card mb-3 form-section {{ old('type')=='parent_meeting' ? 'active' : '' }}" id="form-parent_meeting">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#0dcaf0">3</span>
        <span class="fw-bold">Detail Pemanggilan Orang Tua</span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="meeting_date" class="form-control form-control-sm" value="{{ old('meeting_date', date('Y-m-d')) }}">
            @error('meeting_date')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">
                Penangani
                <span class="badge bg-info text-dark ms-1" style="font-size:.6rem">Bisa lebih dari 1</span>
            </label>
            <select name="handler_id[]" id="handlerSel" class="form-select form-select-sm" multiple size="4"
                onchange="toggleManualMulti(this,'handlerManual')">
                @foreach($counselors as $u)
                <option value="{{ $u->id }}" {{ collect(old('handler_id',[])) ->contains($u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
                <option value="other" {{ in_array('other', old('handler_id',[])) ? 'selected':'' }}>✏️ Lainnya (Ketik Manual)</option>
            </select>
            <div class="form-text" style="font-size:.68rem">Ctrl+klik untuk pilih lebih dari satu</div>
            <input type="text" name="handler_name" id="handlerManual"
                class="form-control form-control-sm mt-1 {{ in_array('other', old('handler_id',[])) ? '' : 'd-none' }}"
                placeholder="Nama penangani lainnya..." value="{{ old('handler_name') }}">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label form-label-sm fw-semibold mb-1">Orang Tua Hadir? <span class="text-danger">*</span></label>
            <select name="parent_attended" class="form-select form-select-sm">
                <option value="1" {{ old('parent_attended','1')=='1' ? 'selected' : '' }}>✅ Hadir</option>
                <option value="0" {{ old('parent_attended')=='0' ? 'selected' : '' }}>❌ Tidak Hadir</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label form-label-sm fw-semibold mb-1">Alasan Pemanggilan <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control form-control-sm" rows="2"
                placeholder="Alasan pemanggilan orang tua...">{{ old('reason') }}</textarea>
            @error('reason')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Hasil Pertemuan</label>
            <textarea name="meeting_result" class="form-control form-control-sm" rows="2" placeholder="Hasil pertemuan...">{{ old('meeting_result') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Kesepakatan</label>
            <textarea name="agreement" class="form-control form-control-sm" rows="2" placeholder="Kesepakatan yang dicapai...">{{ old('agreement') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Tindak Lanjut</label>
            <textarea name="follow_up" class="form-control form-control-sm" rows="2" placeholder="Rencana tindak lanjut...">{{ old('follow_up') }}</textarea>
        </div>
    </div>
</div>

{{-- ─── HOME VISIT ─── --}}
<div class="form-card mb-3 form-section {{ old('type')=='home_visit' ? 'active' : '' }}" id="form-home_visit">
    <div class="d-flex align-items-center mb-3">
        <span class="step-num" style="background:#6f42c1">3</span>
        <span class="fw-bold">Detail Home Visit</span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm fw-semibold mb-1">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="visit_date" class="form-control form-control-sm" value="{{ old('visit_date', date('Y-m-d')) }}">
            @error('visit_date')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">
                Petugas
                <span class="badge bg-info text-dark ms-1" style="font-size:.6rem">Bisa lebih dari 1</span>
            </label>
            <select name="visitor_id[]" id="visitorSel" class="form-select form-select-sm" multiple size="4"
                onchange="toggleManualMulti(this,'visitorManual')">
                @foreach($counselors as $u)
                <option value="{{ $u->id }}" {{ collect(old('visitor_id',[])) ->contains($u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
                <option value="other" {{ in_array('other', old('visitor_id',[])) ? 'selected':'' }}>✏️ Lainnya (Ketik Manual)</option>
            </select>
            <div class="form-text" style="font-size:.68rem">Ctrl+klik untuk pilih lebih dari satu</div>
            <input type="text" name="visitor_name" id="visitorManual"
                class="form-control form-control-sm mt-1 {{ in_array('other', old('visitor_id',[])) ? '' : 'd-none' }}"
                placeholder="Nama petugas lainnya..." value="{{ old('visitor_name') }}">
        </div>
        <div class="col-12">
            <label class="form-label form-label-sm fw-semibold mb-1">Alamat <span class="text-danger">*</span></label>
            <input type="text" name="address" class="form-control form-control-sm"
                value="{{ old('address') }}" placeholder="Alamat rumah yang dikunjungi...">
            @error('address')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label form-label-sm fw-semibold mb-1">Tujuan Kunjungan <span class="text-danger">*</span></label>
            <textarea name="purpose" class="form-control form-control-sm" rows="2"
                placeholder="Tujuan home visit...">{{ old('purpose') }}</textarea>
            @error('purpose')<div class="text-danger" style="font-size:.72rem">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Hasil Kunjungan</label>
            <textarea name="result" class="form-control form-control-sm" rows="2" placeholder="Hasil kunjungan...">{{ old('result') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Kesimpulan</label>
            <textarea name="conclusion" class="form-control form-control-sm" rows="2" placeholder="Kesimpulan...">{{ old('conclusion') }}</textarea>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm fw-semibold mb-1">Tindak Lanjut</label>
            <textarea name="follow_up" class="form-control form-control-sm" rows="2" placeholder="Rencana tindak lanjut...">{{ old('follow_up') }}</textarea>
        </div>
    </div>
</div>

{{-- ─── Submit ─── --}}
<div class="d-flex gap-2 pb-2">
    <button type="submit" class="btn btn-primary px-4" id="submitBtn">
        <i class="bi bi-save-fill me-2"></i>Simpan Data
    </button>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
</div>

</div>{{-- /formSection --}}
</form>
@endsection

@push('scripts')
<script>
const SEARCH_URL = '{{ route("api.students.search") }}';
let searchTimer = null;

/* ══ Toggle manual input — single select ══ */
function toggleManual(sel, manualId) {
    const manual = document.getElementById(manualId);
    if (!manual) return;
    if (sel.value === 'other') {
        manual.classList.remove('d-none');
        manual.focus();
    } else {
        manual.classList.add('d-none');
        manual.value = '';
    }
}

/* ══ Toggle manual input — multi select ══ */
function toggleManualMulti(sel, manualId) {
    const manual = document.getElementById(manualId);
    if (!manual) return;
    const vals = Array.from(sel.selectedOptions).map(o => o.value);
    if (vals.includes('other')) {
        manual.classList.remove('d-none');
        manual.focus();
    } else {
        manual.classList.add('d-none');
        manual.value = '';
    }
}

/* ══ Type selector ══ */
function selectType(type, el) {
    document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selectedType').value = type;
    document.getElementById('studentSection').style.display = '';
    document.getElementById('formSection').style.display   = '';
    document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
    const target = document.getElementById('form-' + type);
    if (target) target.classList.add('active');
    document.getElementById('studentSearch').focus();
}

/* ══ Autocomplete ══ */
const searchInput = document.getElementById('studentSearch');
const dropdown    = document.getElementById('studentDropdown');
const studentId   = document.getElementById('studentId');
const badge       = document.getElementById('studentBadge');
const badgeName   = document.getElementById('badgeName');
const badgeMeta   = document.getElementById('badgeMeta');
const badgeAvatar = document.getElementById('badgeAvatar');

searchInput.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 1) { dropdown.style.display = 'none'; return; }
    searchTimer = setTimeout(() => fetchStudents(q), 220);
});

searchInput.addEventListener('focus', function () {
    if (this.value.trim().length >= 1) fetchStudents(this.value.trim());
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.student-autocomplete')) dropdown.style.display = 'none';
});

function fetchStudents(q) {
    fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                dropdown.innerHTML = '<div class="sd-item text-muted small"><i class="bi bi-search me-2"></i>Tidak ditemukan</div>';
            } else {
                dropdown.innerHTML = data.map(s => {
                    const loc = s.location ? `<span class="badge ms-1" style="${s.location==='selatan'?'background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6':'background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5'};font-size:.6rem"><i class="bi bi-geo-alt-fill"></i> ${s.location==='selatan'?'S':'U'}</span>` : '';
                    return `<div class="sd-item" onclick="selectStudent(${s.id},'${esc(s.name)}','${esc(s.nis)}','${esc(s.class)}','${esc(s.location||'')}')">
                        <div class="sd-avatar">${s.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <div class="sd-name">${esc(s.name)}${loc}</div>
                            <div class="sd-meta">NIS: ${esc(s.nis)} &bull; ${esc(s.class)}</div>
                        </div>
                    </div>`;
                }).join('');
            }
            dropdown.style.display = 'block';
        });
}

function selectStudent(id, name, nis, kelas, location) {
    studentId.value      = id;
    searchInput.value    = name;
    dropdown.style.display = 'none';
    badgeName.textContent  = name;
    const locText = location === 'selatan' ? ' · Selatan' : location === 'utara' ? ' · Utara' : '';
    badgeMeta.textContent  = 'NIS: ' + nis + ' · ' + kelas + locText;
    badgeAvatar.textContent = name.charAt(0).toUpperCase();
    badge.style.display    = 'flex';
}

function clearStudent() {
    studentId.value     = '';
    searchInput.value   = '';
    badge.style.display = 'none';
    searchInput.focus();
}

function esc(str) {
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ══ Poin dari kategori pelanggaran ══ */
document.getElementById('qViolationCat')?.addEventListener('change', function () {
    const isOther = this.value === 'other';
    document.getElementById('qOtherViolation').style.display = isOther ? '' : 'none';
    document.getElementById('qOtherName').required = isOther;
    document.getElementById('qOtherCat').required  = isOther;
    if (!isOther) {
        const pts = this.options[this.selectedIndex].dataset.points;
        if (pts !== undefined) document.getElementById('qViolationPts').value = pts;
    }
});

document.getElementById('qOtherPts')?.addEventListener('input', function () {
    if (document.getElementById('qViolationCat').value === 'other') {
        document.getElementById('qViolationPts').value = this.value;
    }
});

/* ══ Auto-calc durasi keterlambatan ══ */
function calcDuration() {
    const a = document.getElementById('lateArrive')?.value;
    const e = document.getElementById('lateEntry')?.value;
    if (!a || !e) return;
    const [ah, am] = a.split(':').map(Number);
    const [eh, em] = e.split(':').map(Number);
    const diff = (eh * 60 + em) - (ah * 60 + am);
    if (diff > 0) document.getElementById('lateDuration').value = diff;
}
document.getElementById('lateArrive')?.addEventListener('change', calcDuration);
document.getElementById('lateEntry')?.addEventListener('change', calcDuration);

/* ══ Restore state on validation error ══ */
@if(old('type'))
(function () {
    const t = '{{ old("type") }}';
    document.querySelectorAll('.type-card').forEach(c => {
        if (c.getAttribute('onclick')?.includes("'" + t + "'")) c.classList.add('selected');
    });
    @if(old('student_id') && old('_student_name'))
    selectStudent({{ old('student_id') }}, '{{ addslashes(old("_student_name")) }}', '', '', '');
    @endif
})();
@endif
</script>
@endpush
