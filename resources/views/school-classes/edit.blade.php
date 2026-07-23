@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Kelas</h4></div>
    <a href="{{ route('school-classes.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-7">
<form action="{{ route('school-classes.update', $schoolClass) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $schoolClass->name) }}" required/>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Tingkat <span class="text-danger">*</span></label>
                <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                    <option value="">Pilih...</option>
                    <option value="VII"  {{ old('level', $schoolClass->level) == 'VII'  ? 'selected' : '' }}>VII</option>
                    <option value="VIII" {{ old('level', $schoolClass->level) == 'VIII' ? 'selected' : '' }}>VIII</option>
                    <option value="IX"   {{ old('level', $schoolClass->level) == 'IX'   ? 'selected' : '' }}>IX</option>
                </select>
                @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Wali Kelas</label>
                <input type="text" name="homeroom_teacher" class="form-control @error('homeroom_teacher') is-invalid @enderror"
                    value="{{ old('homeroom_teacher', $schoolClass->homeroom_teacher) }}" placeholder="Ketik nama wali kelas..."/>
                @error('homeroom_teacher')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Tahun Ajaran <span class="text-danger">*</span></label>
                <select name="school_year_id" class="form-select @error('school_year_id') is-invalid @enderror" required>
                    <option value="">Pilih Tahun Ajaran...</option>
                    @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}" {{ old('school_year_id', $schoolClass->school_year_id) == $sy->id ? 'selected' : '' }}>
                        {{ $sy->name }}
                    </option>
                    @endforeach
                </select>
                @error('school_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('school-classes.index') }}" class="btn btn-light">Batal</a>
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
