@extends('layouts.app')
@section('title', 'Input Keterlambatan')
@section('content')

<div class="page-header">
    <div><h4><i class="bi bi-clock-history me-2 text-warning"></i>Input Keterlambatan</h4></div>
    <a href="{{ route('late-records.index') }}" class="btn btn-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
<div class="col-12 col-lg-8">
<form action="{{ route('late-records.store') }}" method="POST">
    @csrf
    <div class="form-card">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Siswa <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">Pilih Siswa...</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ (old('student_id', $selectedStudent?->id) == $s->id) ? 'selected' : '' }}>
                        {{ $s->name }} - {{ $s->class?->name ?? '-' }} ({{ $s->nis }})
                    </option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required/>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jam Datang</label>
                <input type="time" name="arrive_time" class="form-control" value="{{ old('arrive_time') }}" id="arriveTime"/>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jam Masuk</label>
                <input type="time" name="entry_time" class="form-control" value="{{ old('entry_time') }}" id="entryTime"/>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Durasi Keterlambatan (menit)</label>
                <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes') }}" min="1" id="durationMinutes"/>
                <small class="text-muted">Otomatis dihitung dari jam datang & masuk</small>
            </div>
            <div class="col-md-6">
                @include('partials.staff-select', [
                    'fieldName'   => 'officer_id',
                    'manualField' => 'officer_name',
                    'label'       => 'Dicatat Oleh',
                    'users'       => $officers,
                    'currentId'   => auth()->id(),
                    'currentName' => null,
                    'currentExtras' => [],
                    'multi'       => false,
                ])
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alasan</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="Contoh: Macet, bangun kesiangan..."/>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-end">
                <a href="{{ route('late-records.index') }}" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save me-1"></i> Simpan
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
// Auto-calc duration
function calcDuration() {
    const arrive = document.getElementById('arriveTime').value;
    const entry  = document.getElementById('entryTime').value;
    if (arrive && entry) {
        const [ah, am] = arrive.split(':').map(Number);
        const [eh, em] = entry.split(':').map(Number);
        const diff = (eh * 60 + em) - (ah * 60 + am);
        if (diff > 0) document.getElementById('durationMinutes').value = diff;
    }
}
document.getElementById('arriveTime').addEventListener('change', calcDuration);
document.getElementById('entryTime').addEventListener('change', calcDuration);
</script>
@endpush
