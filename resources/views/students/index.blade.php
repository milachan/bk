@extends('layouts.app')
@section('title', 'Data Siswa')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-people-fill me-2 text-primary"></i>Data Siswa</h4>
        <small class="text-muted">Total: {{ $students->total() }} siswa terdaftar</small>
    </div>
    @if(auth()->user()->canEdit())
    <div class="d-flex gap-2 flex-wrap">
        <!-- Tombol Import -->
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
        </button>
        <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </a>
    </div>
    @endif
</div>

{{-- Flash import errors --}}
@if(session('import_errors'))
<div class="alert alert-warning alert-dismissible fade show rounded-3 mb-3">
    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Beberapa baris dilewati:</strong>
    <ul class="mb-0 mt-1 small">
        @foreach(session('import_errors') as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filter -->
<div class="form-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label form-label-sm mb-1 text-muted">Cari Siswa</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Nama, NIS, atau NISN..." value="{{ request('search') }}"/>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Kelas</label>
            <select name="class_id" class="form-select form-select-sm">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="aktif"  {{ request('status') === 'aktif'  ? 'selected' : '' }}>Aktif</option>
                <option value="lulus"  {{ request('status') === 'lulus'  ? 'selected' : '' }}>Lulus</option>
                <option value="pindah" {{ request('status') === 'pindah' ? 'selected' : '' }}>Pindah</option>
                <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Keluar</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Lokasi</label>
            <select name="location" class="form-select form-select-sm">
                <option value="">Semua Lokasi</option>
                <option value="selatan" {{ request('location') === 'selatan' ? 'selected' : '' }}>🔴 Selatan (Cendrawasih)</option>
                <option value="utara"   {{ request('location') === 'utara'   ? 'selected' : '' }}>🔵 Utara (Sarbini)</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width:40px">#</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Lokasi</th>
                    <th>JK</th>
                    <th>No. HP</th>
                    <th>Orang Tua</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $s)
                <tr>
                    <td class="ps-3 text-muted small">{{ $students->firstItem() + $i }}</td>
                    <td class="fw-semibold small text-monospace">{{ $s->nis }}</td>
                    <td>
                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none fw-semibold">{{ $s->name }}</a>
                        @if($s->nisn)<br><small class="text-muted" style="font-size:.7rem">NISN: {{ $s->nisn }}</small>@endif
                    </td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $s->class?->name ?? '-' }}</span></td>
                    <td>
                        @if($s->location === 'selatan')
                            <span class="badge" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6"><i class="bi bi-geo-alt-fill me-1"></i>Selatan</span>
                        @elseif($s->location === 'utara')
                            <span class="badge" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5"><i class="bi bi-geo-alt-fill me-1"></i>Utara</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($s->gender === 'L')
                            <span class="badge bg-info text-white"><i class="bi bi-gender-male"></i> L</span>
                        @else
                            <span class="badge bg-pink" style="background:#e91e8c;color:#fff"><i class="bi bi-gender-female"></i> P</span>
                        @endif
                    </td>
                    <td class="small">{{ $s->phone ?? '-' }}</td>
                    <td class="small">
                        {{ $s->parent_name ?? '-' }}
                        @if($s->parent_phone)<br><small class="text-muted">{{ $s->parent_phone }}</small>@endif
                    </td>
                    <td>
                        @php $statusColor = match($s->status) { 'aktif'=>'success', 'lulus'=>'info', 'pindah'=>'warning', 'keluar'=>'danger', default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $statusColor }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('students.show', $s) }}" class="btn btn-light" title="Lihat Profil">
                                <i class="bi bi-eye-fill text-primary"></i>
                            </a>
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('students.edit', $s) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('students.destroy', $s) }}', '{{ addslashes($s->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada data siswa
                        @if(request()->anyFilled(['search','class_id','status','location']))
                        <br><a href="{{ route('students.index') }}" class="btn btn-sm btn-link mt-1">Hapus filter</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($students->hasPages())
    <div class="px-3 py-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $students->firstItem() }}–{{ $students->lastItem() }} dari {{ $students->total() }} siswa</small>
        {{ $students->links() }}
    </div>
    @endif
</div>

{{-- Modal Import Excel --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="importModalLabel">
                    <i class="bi bi-file-earmark-arrow-up me-2 text-success"></i>Import Data Siswa dari Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">

                {{-- Panduan --}}
                <div class="alert alert-info border-0 rounded-3 mb-3" style="background:#e8f4fd">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="bi bi-info-circle-fill fs-5 text-info flex-shrink-0 mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Cara Import:</strong>
                            <ol class="mb-3 small ps-3">
                                <li>Download template Excel dengan tombol di bawah.</li>
                                <li>Isi data siswa sesuai format kolom yang tersedia.</li>
                                <li>Baris contoh (baris 2 &amp; 3) bisa dihapus atau diganti.</li>
                                <li>Upload file yang sudah diisi.</li>
                            </ol>
                            <a href="{{ route('students.template') }}"
                               class="btn btn-sm btn-success"
                               onclick="event.stopPropagation()">
                                <i class="bi bi-download me-1"></i> Download Template Excel
                            </a>
                            <span class="text-muted small ms-2">Format: .xlsx / .xls / .csv &middot; Maks. 5MB</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom template --}}
                <div class="mb-3">
                    <p class="small fw-semibold mb-2 text-muted">Kolom yang tersedia di template:</p>
                    <div class="row g-1">
                        @php
                        $cols = [
                            ['nis',              'NIS (wajib, unik)',          'danger'],
                            ['nisn',             'NISN (opsional)',            'secondary'],
                            ['nama',             'Nama Lengkap (wajib)',       'danger'],
                            ['jenis_kelamin',    'L / P (wajib)',              'danger'],
                            ['tempat_lahir',     'Tempat Lahir',               'secondary'],
                            ['tanggal_lahir',    'Tgl Lahir (YYYY-MM-DD)',     'secondary'],
                            ['agama',            'Agama',                      'secondary'],
                            ['alamat',           'Alamat',                     'secondary'],
                            ['nomor_hp',         'No. HP Siswa',               'secondary'],
                            ['nama_orang_tua',   'Nama Orang Tua',             'secondary'],
                            ['nomor_hp_orang_tua','No. HP Orang Tua',          'secondary'],
                            ['kelas',            'Nama Kelas (sama persis)',   'warning'],
                            ['lokasi',           'selatan / utara',            'success'],
                        ];
                        @endphp
                        @foreach($cols as [$colKey, $colDesc, $colColor])
                        <div class="col-6 col-md-4">
                            <div class="d-flex align-items-center gap-1 py-1">
                                <span class="badge bg-{{ $colColor }} bg-opacity-15 text-{{ $colColor }} border border-{{ $colColor }} border-opacity-25"
                                      style="font-size:.68rem;font-weight:600;min-width:0">{{ $colKey }}</span>
                                <small class="text-muted text-truncate" style="font-size:.7rem">{{ $colDesc }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Form Upload --}}
                <form action="{{ route('students.import') }}" method="POST"
                      enctype="multipart/form-data" id="importForm">
                    @csrf
                    <label class="form-label fw-semibold mb-1">Pilih File Excel</label>
                    <input type="file" name="file" id="importFileInput"
                           class="form-control @error('file') is-invalid @enderror"
                           accept=".xlsx,.xls,.csv" required
                           onchange="updateFileName(this)"/>
                    @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text mt-1" id="selectedFileName">Belum ada file dipilih.</div>

                    {{-- Preview file terpilih --}}
                    <div id="filePreview" class="d-none mt-2 p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-spreadsheet text-success fs-4 flex-shrink-0"></i>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate" id="previewName"></div>
                            <div class="text-muted" style="font-size:.72rem" id="previewSize"></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light flex-shrink-0" onclick="clearFile()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="importForm" class="btn btn-success" id="importBtn">
                    <i class="bi bi-upload me-1"></i>Mulai Import
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Yakin ingin menghapus siswa:</p>
                <p class="fw-bold" id="deleteTargetName"></p>
                <p class="text-danger small fw-semibold mb-0"><i class="bi bi-exclamation-triangle-fill me-1"></i>Seluruh riwayat siswa ini akan ikut terhapus. Tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Ya, Hapus Permanen</button>
            </div>
        </div>
    </div>
</div>
<form id="deleteForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function confirmDelete(url, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteConfirmBtn').onclick = () => document.getElementById('deleteForm').submit();
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
function updateFileName(input) {
    const preview = document.getElementById('filePreview');
    const nameEl  = document.getElementById('previewName');
    const sizeEl  = document.getElementById('previewSize');
    const hint    = document.getElementById('selectedFileName');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const size = (file.size / 1024).toFixed(1);
        nameEl.textContent = file.name;
        sizeEl.textContent = size + ' KB';
        preview.classList.remove('d-none');
        hint.textContent = '';
    }
}
function clearFile() {
    document.getElementById('importFileInput').value = '';
    document.getElementById('filePreview').classList.add('d-none');
    document.getElementById('selectedFileName').textContent = 'Belum ada file dipilih.';
}

// Tampilkan spinner saat submit
document.getElementById('importForm').addEventListener('submit', function() {
    const btn = document.getElementById('importBtn');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengimpor...';
    btn.disabled = true;
});

// Auto-open modal jika ada error import
@if($errors->has('file'))
new bootstrap.Modal(document.getElementById('importModal')).show();
@endif
</script>
@endpush
