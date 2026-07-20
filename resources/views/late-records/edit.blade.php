@extends('layouts.app')
@section('title', 'Edit Keterlambatan')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Keterlambatan</h4></div>
    <a href="{{ route('late-records.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-8">
<form action="{{ route('late-records.update', $lateRecord) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id', $lateRecord->student_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $lateRecord->date?->format('Y-m-d')) }}" required/>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jam Datang</label>
                <input type="time" name="arrive_time" class="form-control" value="{{ old('arrive_time', $lateRecord->arrive_time) }}"/>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jam Masuk</label>
                <input type="time" name="entry_time" class="form-control" value="{{ old('entry_time', $lateRecord->entry_time) }}"/>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Durasi (menit)</label>
                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $lateRecord->duration_minutes) }}" min="1"/>
            </div>
            <div class="col-md-6">
                @include('partials.staff-select', [
                    'fieldName'   => 'officer_id',
                    'manualField' => 'officer_name',
                    'label'       => 'Dicatat Oleh',
                    'users'       => $officers,
                    'currentId'   => $lateRecord->officer_id,
                    'currentName' => $lateRecord->officer_name,
                    'currentExtras' => [],
                    'multi'       => false,
                ])
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alasan</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason', $lateRecord->reason) }}"/>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $lateRecord->notes) }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('late-records.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
