@extends('layouts.app')
@section('title', 'Edit Pemanggilan Orang Tua')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-info"></i>Edit Pemanggilan Orang Tua</h4></div>
    <a href="{{ route('parent-meetings.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-9">
<form action="{{ route('parent-meetings.update', $parentMeeting) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id', $parentMeeting->student_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal Pemanggilan <span class="text-danger">*</span></label>
                <input type="date" name="meeting_date" class="form-control @error('meeting_date') is-invalid @enderror"
                    value="{{ old('meeting_date', $parentMeeting->meeting_date?->format('Y-m-d')) }}" required/>
                @error('meeting_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Alasan Pemanggilan <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                    rows="3" required>{{ old('reason', $parentMeeting->reason) }}</textarea>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Kehadiran Orang Tua <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="parent_attended" id="attendedYes" value="1"
                            {{ old('parent_attended', $parentMeeting->parent_attended ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="attendedYes">Ya, Hadir</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="parent_attended" id="attendedNo" value="0"
                            {{ old('parent_attended', $parentMeeting->parent_attended ? '1' : '0') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="attendedNo">Tidak Hadir</label>
                    </div>
                </div>
                @error('parent_attended')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Hasil Pertemuan</label>
                <textarea name="meeting_result" class="form-control @error('meeting_result') is-invalid @enderror"
                    rows="3">{{ old('meeting_result', $parentMeeting->meeting_result) }}</textarea>
                @error('meeting_result')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Unggah Dokumen / Foto Pendukung</label>
                @if($parentMeeting->attachment)
                <div class="mb-2">
                    <a href="{{ asset('storage/'.$parentMeeting->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-paperclip me-1"></i> Lihat File Saat Ini
                    </a>
                </div>
                @endif
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                    accept="image/*,.pdf,.doc,.docx"/>
                <small class="text-muted">Format: JPG, PNG, PDF, DOC. Maks. 5MB. Kosongkan jika tidak diubah.</small>
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Tindak Lanjut</label>
                <textarea name="follow_up" class="form-control @error('follow_up') is-invalid @enderror"
                    rows="2">{{ old('follow_up', $parentMeeting->follow_up) }}</textarea>
                @error('follow_up')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                @include('partials.staff-select', [
                    'fieldName'    => 'handler_id',
                    'manualField'  => 'handler_name',
                    'label'        => 'Ditangani Oleh',
                    'users'        => $handlers,
                    'currentId'    => $parentMeeting->handler_id,
                    'currentName'  => $parentMeeting->handler_name,
                    'currentExtras'=> $parentMeeting->extra_handlers ?? [],
                    'multi'        => true,
                ])
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('parent-meetings.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-info text-white">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
