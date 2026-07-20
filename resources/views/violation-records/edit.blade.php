@extends('layouts.app')
@section('title', 'Edit Pelanggaran')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-pencil-square me-2 text-danger"></i>Edit Pelanggaran</h4></div>
    <a href="{{ route('violation-records.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-8">
<form action="{{ route('violation-records.update', $violationRecord) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ old('student_id', $violationRecord->student_id) == $s->id ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Jenis Pelanggaran <span class="text-danger">*</span></label>
                <select name="violation_category_id" id="violationCategory" class="form-select @error('violation_category_id') is-invalid @enderror" required>
                    <option value="">Pilih Jenis Pelanggaran...</option>
                    @foreach($violationCategories as $vc)
                    <option value="{{ $vc->id }}"
                        data-points="{{ $vc->points }}"
                        {{ old('violation_category_id', $violationRecord->violation_category_id) == $vc->id ? 'selected' : '' }}>
                        {{ $vc->name }} ({{ ucfirst($vc->category) }})
                    </option>
                    @endforeach
                    <option value="other" data-points="0" {{ old('violation_category_id') === 'other' ? 'selected' : '' }}>
                        Pelanggaran Lainnya
                    </option>
                </select>
                @error('violation_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Form tambahan jika "Pelanggaran Lainnya" dipilih --}}
            <div class="col-12" id="otherViolationForm" style="{{ old('violation_category_id') === 'other' ? '' : 'display:none' }}">
                <div class="border rounded-3 p-3 bg-light">
                    <label class="form-label fw-semibold">Nama Pelanggaran Lainnya <span class="text-danger">*</span></label>
                    <input type="text" name="other_violation_name" id="otherViolationName"
                        class="form-control @error('other_violation_name') is-invalid @enderror"
                        value="{{ old('other_violation_name') }}"
                        placeholder="Tuliskan jenis pelanggaran..."/>
                    @error('other_violation_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="row mt-2 g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori Pelanggaran <span class="text-danger">*</span></label>
                            <select name="other_violation_category" id="otherViolationCategory"
                                class="form-select @error('other_violation_category') is-invalid @enderror">
                                <option value="">Pilih Kategori...</option>
                                <option value="ringan" {{ old('other_violation_category') === 'ringan' ? 'selected' : '' }}>Ringan</option>
                                <option value="sedang" {{ old('other_violation_category') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="berat"  {{ old('other_violation_category') === 'berat'  ? 'selected' : '' }}>Berat</option>
                            </select>
                            @error('other_violation_category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Poin Pelanggaran <span class="text-danger">*</span></label>
                            <input type="number" name="other_violation_points" id="otherViolationPoints"
                                class="form-control @error('other_violation_points') is-invalid @enderror"
                                value="{{ old('other_violation_points', 0) }}" min="0" max="100"/>
                            @error('other_violation_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label fw-semibold">Deskripsi Pelanggaran</label>
                        <textarea name="other_violation_description" class="form-control" rows="2"
                            placeholder="Deskripsi singkat pelanggaran...">{{ old('other_violation_description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date', $violationRecord->date?->format('Y-m-d')) }}" required/>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">Poin <span class="text-danger">*</span></label>
                <input type="number" name="points" id="points" class="form-control @error('points') is-invalid @enderror"
                    value="{{ old('points', $violationRecord->points) }}" min="0" max="100" required/>
                @error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Kronologi / Deskripsi</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    rows="3">{{ old('description', $violationRecord->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Pelapor</label>
                <select name="reporter_id" class="form-select">
                    <option value="">Pilih Pelapor...</option>
                    @foreach($reporters as $rp)
                    <option value="{{ $rp->id }}" {{ old('reporter_id', $violationRecord->reporter_id) == $rp->id ? 'selected' : '' }}>
                        {{ $rp->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Foto Bukti</label>
                @if($violationRecord->evidence_photo)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$violationRecord->evidence_photo) }}" alt="Bukti" class="img-thumbnail" style="max-height:80px"/>
                    <small class="text-muted d-block">Foto saat ini</small>
                </div>
                @endif
                <input type="file" name="evidence_photo" class="form-control @error('evidence_photo') is-invalid @enderror" accept="image/*"/>
                @error('evidence_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Catatan Tambahan</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $violationRecord->notes) }}</textarea>
            </div>

            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('violation-records.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
const violationSelect = document.getElementById('violationCategory');
const otherForm = document.getElementById('otherViolationForm');
const otherNameInput = document.getElementById('otherViolationName');
const otherCatSelect = document.getElementById('otherViolationCategory');
const otherPointsInput = document.getElementById('otherViolationPoints');

violationSelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const isOther = this.value === 'other';

    if (isOther) {
        otherForm.style.display = '';
        document.getElementById('points').value = otherPointsInput.value || 0;
        otherNameInput.required = true;
        otherCatSelect.required = true;
    } else {
        otherForm.style.display = 'none';
        otherNameInput.required = false;
        otherCatSelect.required = false;
        const points = selected.getAttribute('data-points');
        if (points !== null) {
            document.getElementById('points').value = points;
        }
    }
});

// Sync poin dari field "other" ke field poin utama
otherPointsInput.addEventListener('input', function () {
    if (violationSelect.value === 'other') {
        document.getElementById('points').value = this.value;
    }
});

// Init state jika ada old value
if (violationSelect.value === 'other') {
    otherForm.style.display = '';
    otherNameInput.required = true;
    otherCatSelect.required = true;
}
</script>
@endpush
