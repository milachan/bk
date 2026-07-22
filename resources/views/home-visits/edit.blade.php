@extends('layouts.app')
@section('title', 'Edit Home Visit')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Home Visit</h4></div>
    <a href="{{ route('home-visits.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-9">
<form action="{{ route('home-visits.update', $homeVisit) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}"
                        data-address="{{ $s->address ?? '' }}"
                        {{ old('student_id', $homeVisit->student_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                <input type="date" name="visit_date" class="form-control @error('visit_date') is-invalid @enderror"
                    value="{{ old('visit_date', $homeVisit->visit_date?->format('Y-m-d')) }}" required/>
                @error('visit_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Alamat Kunjungan <span class="text-danger">*</span></label>
                <textarea name="address" id="addressField" class="form-control @error('address') is-invalid @enderror"
                    rows="2" required>{{ old('address', $homeVisit->address) }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Tujuan Kunjungan <span class="text-danger">*</span></label>
                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror"
                    rows="3" required>{{ old('purpose', $homeVisit->purpose) }}</textarea>
                @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Hasil Kunjungan</label>
                <textarea name="result" class="form-control @error('result') is-invalid @enderror"
                    rows="3">{{ old('result', $homeVisit->result) }}</textarea>
                @error('result')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Kesimpulan</label>
                <textarea name="conclusion" class="form-control @error('conclusion') is-invalid @enderror"
                    rows="3">{{ old('conclusion', $homeVisit->conclusion) }}</textarea>
                @error('conclusion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Tindak Lanjut</label>
                <textarea name="follow_up" class="form-control @error('follow_up') is-invalid @enderror"
                    rows="2">{{ old('follow_up', $homeVisit->follow_up) }}</textarea>
                @error('follow_up')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Unggah Foto / Dokumen Pendukung</label>
                @if($homeVisit->attachment)
                <div class="mb-2">
                    <a href="{{ Storage::url($homeVisit->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-paperclip me-1"></i> Lihat File Saat Ini
                    </a>
                </div>
                @endif
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                    accept="image/*,.pdf,.doc,.docx">
                <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX. Maks: 5MB. Biarkan kosong jika tidak ingin mengubah file.</small>
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                @include('partials.staff-select', [
                    'fieldName'    => 'visitor_id',
                    'manualField'  => 'visitor_name',
                    'label'        => 'Petugas Kunjungan',
                    'users'        => $visitors,
                    'currentId'    => $homeVisit->visitor_id,
                    'currentName'  => $homeVisit->visitor_name,
                    'currentExtras'=> $homeVisit->extra_visitors ?? [],
                    'multi'        => true,
                ])
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('home-visits.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
