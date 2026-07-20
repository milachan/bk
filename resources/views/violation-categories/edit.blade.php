@extends('layouts.app')
@section('title', 'Edit Jenis Pelanggaran')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Jenis Pelanggaran</h4></div>
    <a href="{{ route('violation-categories.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-7">
<form action="{{ route('violation-categories.update', $violationCategory) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Pelanggaran <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $violationCategory->name) }}" required/>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="">Pilih Kategori...</option>
                    <option value="ringan" {{ old('category', $violationCategory->category) == 'ringan' ? 'selected' : '' }}>Ringan</option>
                    <option value="sedang" {{ old('category', $violationCategory->category) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="berat" {{ old('category', $violationCategory->category) == 'berat' ? 'selected' : '' }}>Berat</option>
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Poin Pelanggaran <span class="text-danger">*</span></label>
                <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                    value="{{ old('points', $violationCategory->points) }}" min="0" max="100" required/>
                <small class="text-muted">Nilai 0 - 100</small>
                @error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    rows="3">{{ old('description', $violationCategory->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('violation-categories.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
