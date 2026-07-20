@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-person-plus-fill me-2 text-primary"></i>Tambah Siswa Baru</h4>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
        </button>
        <a href="{{ route('students.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="form-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Data Identitas</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" required/>
                        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NISN</label>
                        <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}"/>
                        @error('nisn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required/>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">Pilih...</option>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tempat Lahir</label>
                        <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Agama</label>
                        <select name="religion" class="form-select">
                            <option value="">Pilih...</option>
                            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                            <option value="{{ $r }}" {{ old('religion') === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. HP Siswa</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"/>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="lulus"  {{ old('status') === 'lulus'  ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ old('status') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                            <option value="keluar" {{ old('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>

                <hr class="my-3"/>
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-people me-2"></i>Data Orang Tua</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Orang Tua / Wali</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}"/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">No. HP Orang Tua</label>
                        <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="form-card mb-3">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-door-open me-2"></i>Kelas & Lokasi</h6>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lokasi Sekolah <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <div class="form-check form-check-inline flex-fill">
                            <input class="form-check-input" type="radio" name="location" id="locSelatan" value="selatan"
                                {{ old('location') === 'selatan' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 border rounded-2 p-2 cursor-pointer" for="locSelatan" style="cursor:pointer">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <strong>Selatan</strong><br>
                                <small class="text-muted">Jl. Cendrawasih</small>
                            </label>
                        </div>
                        <div class="form-check form-check-inline flex-fill">
                            <input class="form-check-input" type="radio" name="location" id="locUtara" value="utara"
                                {{ old('location') === 'utara' ? 'checked' : '' }}>
                            <label class="form-check-label w-100 border rounded-2 p-2 cursor-pointer" for="locUtara" style="cursor:pointer">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                <strong>Utara</strong><br>
                                <small class="text-muted">Jl. Sarbini</small>
                            </label>
                        </div>
                    </div>
                    @error('location')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <label class="form-label fw-semibold">Kelas</label>
                <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
                    <option value="">Pilih Kelas...</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-card">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-image me-2"></i>Foto Siswa</h6>
                <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)"/>
                <img id="photoPreview" src="#" class="img-fluid rounded mt-2 d-none" style="max-height:200px"/>
                <small class="text-muted d-block mt-1">Maks. 2MB. Format: JPG, PNG</small>
            </div>
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="{{ route('students.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Data Siswa
            </button>
        </div>
    </div>
</form>
@endsection

{{-- Modal Import Excel (sama seperti di index) --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-arrow-up me-2 text-success"></i>Import Data Siswa dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 mb-3" style="background:#e8f4fd">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="bi bi-info-circle-fill fs-5 text-info flex-shrink-0 mt-1"></i>
                        <div>
                            <strong>Cara Import:</strong>
                            <ol class="mb-2 mt-1 small ps-3">
                                <li>Download template Excel di bawah ini.</li>
                                <li>Isi data siswa sesuai format kolom yang tersedia.</li>
                                <li>Baris contoh bisa dihapus atau diganti.</li>
                                <li>Upload file yang sudah diisi.</li>
                            </ol>
                            <a href="{{ route('students.template') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-download me-1"></i> Download Template Excel
                            </a>
                            <span class="text-muted small ms-2">Format: .xlsx / .xls / .csv · Maks. 5MB</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="small fw-semibold mb-2">Kolom yang tersedia di template:</p>
                    <div class="row g-1">
                        @foreach([['nis','NIS (wajib, unik)','danger'],['nisn','NISN (opsional)','secondary'],['nama','Nama Lengkap (wajib)','danger'],['jenis_kelamin','L / P (wajib)','danger'],['tempat_lahir','Tempat Lahir','secondary'],['tanggal_lahir','Tanggal Lahir (YYYY-MM-DD)','secondary'],['agama','Agama','secondary'],['alamat','Alamat','secondary'],['nomor_hp','No. HP Siswa','secondary'],['nama_orang_tua','Nama Orang Tua','secondary'],['nomor_hp_orang_tua','No. HP Orang Tua','secondary'],['kelas','Nama Kelas (harus sama persis)','warning'],['lokasi','selatan / utara','success']] as $c)
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-{{ $c[2] }} bg-opacity-15 text-{{ $c[2] }} border border-{{ $c[2] }} border-opacity-25" style="font-size:.7rem">{{ $c[0] }}</span>
                                <small class="text-muted" style="font-size:.72rem">{{ $c[1] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <label class="form-label fw-semibold">Pilih File Excel</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required id="importFileInput"/>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="importForm" class="btn btn-success">
                    <i class="bi bi-upload me-1"></i> Mulai Import
                </button>
            </div>
        </div>
    </div>
</div>

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
