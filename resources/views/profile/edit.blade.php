@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-person-gear me-2 text-primary"></i>Profil Saya</h4>
        <small class="text-muted">Kelola informasi akun Anda</small>
    </div>
</div>

<div class="row g-3">
    {{-- Kartu Info User --}}
    <div class="col-12 col-lg-4">
        <div class="form-card text-center">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                 style="width:80px;height:80px;background:#0d6efd;font-size:2rem;color:#fff;font-weight:700">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 mb-2">
                {{ $user->role?->label ?? 'User' }}
            </span>
            <div class="text-muted small">{{ $user->email }}</div>
            @if($user->jabatan)
            <div class="text-muted small mt-1"><i class="bi bi-briefcase me-1"></i>{{ $user->jabatan }}</div>
            @endif
            <hr/>
            <div class="d-flex justify-content-around text-center">
                <div>
                    <div class="fw-bold text-primary">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</div>
                    <small class="text-muted">Bergabung</small>
                </div>
                <div>
                    <div class="fw-bold {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </div>
                    <small class="text-muted">Status</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        {{-- Update Info --}}
        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-fill me-2"></i>Informasi Profil</h6>

            @if(session('status') === 'profile-updated')
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
                <i class="bi bi-check-circle-fill me-1"></i> Profil berhasil diperbarui.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required/>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required/>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Ganti Password --}}
        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-shield-lock-fill me-2"></i>Ganti Password</h6>

            @if(session('status') === 'password-updated')
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
                <i class="bi bi-check-circle-fill me-1"></i> Password berhasil diperbarui.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Password Saat Ini</label>
                        <input type="password" name="current_password"
                               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"/>
                        @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password"
                               class="form-control @error('password', 'updatePassword') is-invalid @enderror"/>
                        @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control"/>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-key-fill me-1"></i> Ganti Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Hapus Akun --}}
        @if(auth()->user()->isAdmin())
        <div class="form-card border-danger border-opacity-50">
            <h6 class="fw-bold mb-1 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Zona Berbahaya</h6>
            <p class="text-muted small mb-3">Setelah akun dihapus, semua data akan hilang permanen.</p>
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash-fill me-1"></i> Hapus Akun
            </button>
        </div>
        @endif
    </div>
</div>

{{-- Modal Hapus Akun --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Masukkan password Anda untuk mengkonfirmasi penghapusan akun.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm">
                    @csrf @method('DELETE')
                    <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Password" required/>
                    @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="deleteForm" class="btn btn-danger">
                    <i class="bi bi-trash-fill me-1"></i> Hapus Akun
                </button>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('scripts')
<script>new bootstrap.Modal(document.getElementById('deleteModal')).show();</script>
@endpush
@endif

@endsection
