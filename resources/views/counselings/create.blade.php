@extends('layouts.app')
@section('title', 'Input Konseling')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-chat-heart-fill me-2 text-success"></i>Input Konseling</h4></div>
    <a href="{{ route('counselings.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-9">
<form action="{{ route('counselings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date', date('Y-m-d')) }}" required/>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Masalah / Permasalahan <span class="text-danger">*</span></label>
                <textarea name="problem" class="form-control @error('problem') is-invalid @enderror"
                    rows="3" placeholder="Uraikan permasalahan siswa..." required>{{ old('problem') }}</textarea>
                @error('problem')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Hasil Konseling</label>
                <textarea name="result" class="form-control @error('result') is-invalid @enderror"
                    rows="3" placeholder="Hasil dari sesi konseling...">{{ old('result') }}</textarea>
                @error('result')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Solusi / Rencana Tindak Lanjut</label>
                <textarea name="solution" class="form-control @error('solution') is-invalid @enderror"
                    rows="3" placeholder="Solusi yang disepakati...">{{ old('solution') }}</textarea>
                @error('solution')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Unggah Foto / Dokumen Pendukung</label>
                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
                    accept="image/*,.pdf,.doc,.docx"/>
                <small class="text-muted">Format: JPG, PNG, PDF, DOC. Maks. 5MB</small>
                @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                @include('partials.staff-select', [
                    'fieldName'   => 'counselor_id',
                    'manualField' => 'counselor_name',
                    'label'       => 'Guru BK / Konselor',
                    'users'       => $counselors,
                    'currentId'   => old('counselor_id') ? null : auth()->id(),
                    'currentName' => null,
                    'currentExtras' => [],
                    'multi'       => true,
                ])
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('counselings.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
