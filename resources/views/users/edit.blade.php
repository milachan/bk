@extends('layouts.app')
@section('title', 'Edit User')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-person-gear me-2 text-primary"></i>Edit User</h4></div>
    <a href="{{ route('users.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-7">
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required/>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required/>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jabatan</label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror"
                    value="{{ old('jabatan', $user->jabatan) }}"/>
                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <hr class="my-1"/>
                <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i>Kosongkan kolom password jika tidak ingin mengubah password</p>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Kosongkan jika tidak diubah"/>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Ulangi password baru"/>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">Pilih Role...</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>
                        {{ $r->label ?? $r->name }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1"
                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">Akun Aktif</label>
                </div>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
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
