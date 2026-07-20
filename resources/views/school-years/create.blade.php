@extends('layouts.app')
@section('title', 'Tambah Tahun Ajaran')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-calendar3 me-2 text-primary"></i>Tambah Tahun Ajaran</h4></div>
    <a href="{{ route('school-years.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-6">
<form action="{{ route('school-years.store') }}" method="POST">
    @csrf
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Tahun Ajaran <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" placeholder="Contoh: 2024/2025" required/>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                    value="{{ old('start_date') }}" required/>
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                    value="{{ old('end_date') }}" required/>
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1"
                        {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">Jadikan Tahun Ajaran Aktif</label>
                </div>
                <small class="text-muted">Hanya satu tahun ajaran yang bisa aktif pada satu waktu</small>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('school-years.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection
