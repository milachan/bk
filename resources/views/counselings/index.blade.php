@extends('layouts.app')
@section('title', 'Data Konseling')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-chat-heart-fill me-2 text-success"></i>Data Konseling</h4>
        <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
    </div>
    <a href="{{ route('counselings.create') }}" class="btn btn-success btn-sm fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Input Konseling
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#f0fdf4">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#198754">
                <i class="bi bi-calendar-day text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Hari Ini</div>
                <div class="fw-bold fs-3 lh-1 text-success">{{ $stats['today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#ecfdf5">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#20c997">
                <i class="bi bi-calendar-week text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Minggu Ini</div>
                <div class="fw-bold fs-3 lh-1" style="color:#20c997">{{ $stats['this_week'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#f0fff4">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#157347">
                <i class="bi bi-calendar-month text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Bulan Ini</div>
                <div class="fw-bold fs-3 lh-1" style="color:#157347">{{ $stats['this_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#f3f4ff">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#6366f1">
                <i class="bi bi-archive text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Total Keseluruhan</div>
                <div class="fw-bold fs-3 lh-1" style="color:#6366f1">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Recent konseling --}}
@if($recentStudents->count())
<div class="table-card mb-3 p-3">
    <div class="fw-semibold small mb-2 text-muted"><i class="bi bi-clock-history me-1 text-success"></i>Konseling Terbaru Bulan Ini</div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($recentStudents as $c)
        <a href="{{ route('students.show', $c->student) }}" class="text-decoration-none">
            <div class="d-flex align-items-center gap-2 border rounded-2 px-2 py-1 bg-light">
                <div style="width:28px;height:28px;background:#198754;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;color:#fff;font-weight:700">
                    {{ strtoupper(substr($c->student?->name ?? '?', 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold" style="font-size:.8rem">{{ $c->student?->name }}</div>
                    <div class="text-muted" style="font-size:.7rem">{{ $c->date?->translatedFormat('d M') }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Filter --}}
<div class="form-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <label class="form-label form-label-sm mb-1 text-muted">Cari Siswa</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Nama siswa..." value="{{ request('search') }}"/>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Lokasi</label>
            <select name="location" class="form-select form-select-sm">
                <option value="">Semua Lokasi</option>
                <option value="selatan" {{ request('location') === 'selatan' ? 'selected' : '' }}>🔴 Selatan</option>
                <option value="utara"   {{ request('location') === 'utara'   ? 'selected' : '' }}>🔵 Utara</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"/>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"/>
        </div>
        <div class="col-12 col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-success btn-sm flex-fill">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            <a href="{{ route('counselings.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="card-header-custom">
        <span class="small fw-semibold"><i class="bi bi-table me-1"></i>Daftar Konseling
            @if($records->total() > 0)<span class="badge bg-success ms-1">{{ $records->total() }}</span>@endif
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Permasalahan</th>
                    <th>Guru BK</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                <tr>
                    <td class="ps-3 text-muted small">{{ $records->firstItem() + $i }}</td>
                    <td class="small fw-semibold text-nowrap">{{ $r->date?->translatedFormat('d M Y') }}</td>
                    <td><a href="{{ route('students.show', $r->student) }}" class="text-decoration-none fw-semibold">{{ $r->student?->name }}
                        @if($r->student?->location === 'selatan')
                            <span class="badge ms-1" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> S</span>
                        @elseif($r->student?->location === 'utara')
                            <span class="badge ms-1" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> U</span>
                        @endif
                    </a></td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td class="small" style="max-width:220px">
                        <span class="text-truncate d-block" title="{{ $r->problem }}">{{ $r->problem }}</span>
                    </td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->counselor, 'manualName'=>$r->counselor_name, 'extras'=>$r->extra_counselors])</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('counselings.edit', $r) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('counselings.destroy', $r) }}', '{{ addslashes($r->student?->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-heart fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada data konseling
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="px-3 py-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
        <small class="text-muted">{{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }}</small>
        {{ $records->links() }}
    </div>
    @endif
</div>
@endsection

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Yakin ingin menghapus data konseling:</p>
                <p class="fw-bold" id="deleteTargetName"></p>
                <p class="text-muted small mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<form id="deleteForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete(url, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteConfirmBtn').onclick = () => document.getElementById('deleteForm').submit();
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush