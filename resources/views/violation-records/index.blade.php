@extends('layouts.app')
@section('title', 'Data Pelanggaran')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-shield-exclamation me-2 text-danger"></i>Data Pelanggaran</h4>
        <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
    </div>
    <a href="{{ route('violation-records.create') }}" class="btn btn-danger btn-sm fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Input Pelanggaran
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fff0f0">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#dc3545">
                <i class="bi bi-calendar-month text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Bulan Ini</div>
                <div class="fw-bold fs-3 lh-1 text-danger">{{ $stats['this_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fff8e1">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#e6a817">
                <i class="bi bi-award text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Total Poin Bulan Ini</div>
                <div class="fw-bold fs-3 lh-1" style="color:#e6a817">{{ $stats['points_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fce4ec">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#c62828">
                <i class="bi bi-exclamation-octagon text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Pelanggaran Berat</div>
                <div class="fw-bold fs-3 lh-1" style="color:#c62828">{{ $stats['berat'] }}</div>
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

<div class="row g-3 mb-3">
    {{-- Chart kategori --}}
    <div class="col-12 col-md-5">
        <div class="table-card p-3 h-100">
            <div class="fw-semibold small mb-2 text-muted">
                <i class="bi bi-pie-chart-fill me-1 text-danger"></i>Kategori Pelanggaran Bulan Ini
            </div>
            @if(array_sum($chartCatData) > 0)
            <canvas id="catChart" height="180"></canvas>
            @else
            <div class="text-center py-4 text-muted small">Belum ada data bulan ini</div>
            @endif
        </div>
    </div>
    {{-- Top poin --}}
    <div class="col-12 col-md-7">
        <div class="table-card h-100">
            <div class="card-header-custom">
                <span class="small fw-semibold text-muted"><i class="bi bi-person-exclamation me-1 text-danger"></i>Siswa Poin Terbanyak Bulan Ini</span>
            </div>
            @if($topStudents->count())
            <div class="p-3">
                @foreach($topStudents as $i => $s)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill {{ $i === 0 ? 'bg-danger' : ($i === 1 ? 'bg-warning text-dark' : 'bg-light text-secondary') }}" style="width:22px;height:22px;display:flex;align-items:center;justify-content:center">{{ $i+1 }}</span>
                    <div class="flex-grow-1">
                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none fw-semibold small">{{ $s->name }}</a>
                        <small class="text-muted ms-1">{{ $s->class?->name ?? '-' }}</small>
                    </div>
                    @php $poin = $s->points_this_month ?? 0; $barPct = min(100, $poin * 2); @endphp
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:80px;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden">
                            <div style="width:{{ $barPct }}%;height:100%;background:{{ $i===0?'#dc3545':($i===1?'#ffc107':'#6c757d') }};border-radius:3px"></div>
                        </div>
                        <span class="badge bg-danger small" style="min-width:42px">{{ $poin }} poin</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4 text-muted small">Belum ada data bulan ini</div>
            @endif
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="form-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label form-label-sm mb-1 text-muted">Cari Siswa</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Nama siswa..." value="{{ request('search') }}"/>
            </div>
        </div>
        <div class="col-4 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Kelas</label>
            <select name="class_id" class="form-select form-select-sm">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-4 col-md-1">
            <label class="form-label form-label-sm mb-1 text-muted">Kategori</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="ringan" {{ request('category') === 'ringan' ? 'selected' : '' }}>Ringan</option>
                <option value="sedang" {{ request('category') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                <option value="berat"  {{ request('category') === 'berat'  ? 'selected' : '' }}>Berat</option>
            </select>
        </div>
        <div class="col-4 col-md-1">
            <label class="form-label form-label-sm mb-1 text-muted">Lokasi</label>
            <select name="location" class="form-select form-select-sm">
                <option value="">Semua</option>
                <option value="selatan" {{ request('location') === 'selatan' ? 'selected' : '' }}>🔴 Sel</option>
                <option value="utara"   {{ request('location') === 'utara'   ? 'selected' : '' }}>🔵 Uta</option>
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
            <button type="submit" class="btn btn-danger btn-sm flex-fill">
                <i class="bi bi-search"></i>
            </button>
            <a href="{{ route('violation-records.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="card-header-custom">
        <span class="small fw-semibold"><i class="bi bi-table me-1"></i>Daftar Pelanggaran
            @if($records->total() > 0)<span class="badge bg-danger ms-1">{{ $records->total() }}</span>@endif
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
                    <th>Jenis Pelanggaran</th>
                    <th>Kategori</th>
                    <th>Poin</th>
                    <th>Pelapor</th>
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
                    <td class="small">{{ $r->violationCategory?->name ?? '-' }}</td>
                    <td>
                        @php $cat = $r->violationCategory?->category ?? '' @endphp
                        @if($cat === 'ringan')<span class="badge" style="background:#d1e7dd;color:#0f5132">Ringan</span>
                        @elseif($cat === 'sedang')<span class="badge" style="background:#fff3cd;color:#664d03">Sedang</span>
                        @elseif($cat === 'berat')<span class="badge" style="background:#f8d7da;color:#842029">Berat</span>
                        @else<span class="badge bg-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        @php $pc = $r->points >= 30 ? 'danger' : ($r->points >= 15 ? 'warning text-dark' : 'secondary'); @endphp
                        <span class="badge bg-{{ $pc }} fw-bold">+{{ $r->points }}</span>
                    </td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->reporter, 'manualName'=>$r->reporter_name, 'extras'=>null])</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('violation-records.edit', $r) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('violation-records.destroy', $r) }}', '{{ addslashes($r->student?->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada data pelanggaran
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
                <p class="mb-1">Yakin ingin menghapus data pelanggaran:</p>
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
@if(array_sum($chartCatData) > 0)
<script>
new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartCats) !!},
        datasets: [{
            data: {!! json_encode($chartCatData) !!},
            backgroundColor: ['#d1e7dd', '#fff3cd', '#f8d7da'],
            borderColor: ['#0f5132', '#664d03', '#842029'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} kasus` } }
        }
    }
});
</script>
@endif

<script>
function confirmDelete(url, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteConfirmBtn').onclick = () => document.getElementById('deleteForm').submit();
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
