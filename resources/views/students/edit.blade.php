@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Data Siswa</h4>
        <small class="text-muted">{{ $student->name }}</small>
    </div>
    <a href="{{ route('students.show', $student) }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="form-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Data Identitas</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $student->nis) }}" required/>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NISN</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $student->nisn) }}"/>
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->name) }}" required/>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $student->birth_place) }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Agama</label>
                        <select name="religion" class="form-select">
                            <option value="">Pilih...</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                            <option value="{{ $r }}" {{ old('religion', $student->religion) === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['aktif','lulus','pindah','keluar'] as $st)
                            <option value="{{ $st }}" {{ old('status', $student->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                    </div>
                </div>
                <hr/>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Orang Tua</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name', $student->parent_name) }}"/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone', $student->parent_phone) }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="form-card mb-3">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-geo-alt me-2"></i>Lokasi & Kelas</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi Sekolah</label>
                    <div class="d-flex gap-2">
                        <div class="form-check flex-fill">
                            <input class="form-check-input" type="radio" name="location" id="locSelatan" value="selatan"
                                {{ old('location', $student->location) === 'selatan' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 border rounded-2 p-2" for="locSelatan" style="cursor:pointer">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <strong>Selatan</strong><br>
                                <small class="text-muted">Jl. Cendrawasih</small>
                            </label>
                        </div>
                        <div class="form-check flex-fill">
                            <input class="form-check-input" type="radio" name="location" id="locUtara" value="utara"
                                {{ old('location', $student->location) === 'utara' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 border rounded-2 p-2" for="locUtara" style="cursor:pointer">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                <strong>Utara</strong><br>
                                <small class="text-muted">Jl. Sarbini</small>
                            </label>
                        </div>
                    </div>
                </div>
                <label class="form-label fw-semibold">Kelas</label>
                <select name="class_id" class="form-select" size="6">
                    <option value="">Pilih Kelas...</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-card">
                <h6 class="fw-bold mb-2">Foto Siswa</h6>
                @if($student->photo)
                <img src="{{ Storage::url($student->photo) }}" class="img-fluid rounded mb-2" style="max-height:180px"/>
                @endif
                <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)"/>
                <img id="photoPreview" src="#" class="img-fluid rounded mt-2 d-none" style="max-height:180px"/>
            </div>
        </div>
        <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="{{ route('students.show', $student) }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-warning">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

