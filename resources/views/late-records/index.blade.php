@extends('layouts.app')
@section('title', 'Keterlambatan')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-clock-history me-2 text-warning"></i>Data Keterlambatan</h4>
        <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
    </div>
    <a href="{{ route('late-records.create') }}" class="btn btn-warning btn-sm fw-semibold">
        <i class="bi bi-plus-circle me-1"></i> Input Keterlambatan
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fff7e6">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#ffc107">
                <i class="bi bi-calendar-day text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Hari Ini</div>
                <div class="fw-bold fs-3 lh-1 text-warning">{{ $stats['today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fff3e0">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#fd7e14">
                <i class="bi bi-calendar-week text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Minggu Ini</div>
                <div class="fw-bold fs-3 lh-1" style="color:#fd7e14">{{ $stats['this_week'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#fff8e1">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#e6a817">
                <i class="bi bi-calendar-month text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Bulan Ini</div>
                <div class="fw-bold fs-3 lh-1" style="color:#e6a817">{{ $stats['this_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="p-3 rounded-3 border d-flex align-items-center gap-3" style="background:#f3f4ff">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:46px;height:46px;background:#6366f1">
                <i class="bi bi-stopwatch text-white fs-5"></i>
            </div>
            <div>
                <div class="text-muted" style="font-size:.75rem">Rata-rata Durasi</div>
                <div class="fw-bold fs-3 lh-1" style="color:#6366f1">{{ $stats['avg_duration'] }}<small class="fs-6"> mnt</small></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    {{-- Chart 7 hari --}}
    <div class="col-12 col-lg-7">
        <div class="table-card p-3 h-100">
            <div class="fw-semibold small mb-2 text-muted">
                <i class="bi bi-bar-chart-fill me-1 text-warning"></i>Keterlambatan 7 Hari Terakhir
            </div>
            <canvas id="lateChart" height="90"></canvas>
        </div>
    </div>
    {{-- Top siswa --}}
    <div class="col-12 col-lg-5">
        <div class="table-card h-100">
            <div class="card-header-custom">
                <span class="small fw-semibold text-muted"><i class="bi bi-trophy me-1 text-warning"></i>Paling Sering Terlambat Bulan Ini</span>
            </div>
            @if($topStudents->count())
            <ul class="list-group list-group-flush">
                @foreach($topStudents as $i => $s)
                <li class="list-group-item d-flex align-items-center gap-2 py-2 px-3">
                    <span class="badge rounded-pill {{ $i === 0 ? 'bg-warning text-dark' : 'bg-light text-secondary' }}" style="width:22px">{{ $i+1 }}</span>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none fw-semibold small text-truncate d-block">{{ $s->name }}</a>
                        <small class="text-muted">{{ $s->class?->name ?? '-' }}</small>
                    </div>
                    <span class="badge bg-warning text-dark">{{ $s->late_this_month }}x</span>
                </li>
                @endforeach
            </ul>
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
        <div class="col-6 col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-search me-1"></i>Filter
            </button>
            <a href="{{ route('late-records.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
    @if(request()->anyFilled(['search','class_id','date_from','date_to','location']))
    <div class="mt-2 d-flex flex-wrap gap-1">
        <small class="text-muted me-1">Filter aktif:</small>
        @if(request('search'))<span class="badge bg-light text-dark border">Nama: "{{ request('search') }}"</span>@endif
        @if(request('class_id'))<span class="badge bg-light text-dark border">Kelas: {{ $classes->find(request('class_id'))?->name }}</span>@endif
        @if(request('location'))<span class="badge bg-light text-dark border">Lokasi: {{ ucfirst(request('location')) }}</span>@endif
        @if(request('date_from'))<span class="badge bg-light text-dark border">Dari: {{ request('date_from') }}</span>@endif
        @if(request('date_to'))<span class="badge bg-light text-dark border">Sampai: {{ request('date_to') }}</span>@endif
    </div>
    @endif
</div>

<div class="table-card">
    <div class="card-header-custom">
        <span class="small fw-semibold">
            <i class="bi bi-table me-1"></i>
            Daftar Keterlambatan
            @if($records->total() > 0)<span class="badge bg-warning text-dark ms-1">{{ $records->total() }}</span>@endif
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width:40px">#</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Jam Datang</th>
                    <th>Durasi</th>
                    <th>Alasan</th>
                    <th>Dicatat Oleh</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                <tr>
                    <td class="ps-3 text-muted small">{{ $records->firstItem() + $i }}</td>
                    <td class="small fw-semibold text-nowrap">{{ $r->date?->translatedFormat('d M Y') }}</td>
                    <td>
                        <a href="{{ route('students.show', $r->student) }}" class="text-decoration-none fw-semibold">{{ $r->student?->name }}</a>
                        @if($r->student?->location === 'selatan')
                            <span class="badge ms-1" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> S</span>
                        @elseif($r->student?->location === 'utara')
                            <span class="badge ms-1" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> U</span>
                        @endif
                    </td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td class="small text-nowrap">
                        @if($r->arrive_time)
                        <i class="bi bi-alarm text-muted me-1"></i>{{ $r->arrive_time }}
                        @else -
                        @endif
                    </td>
                    <td>
                        @if($r->duration_minutes)
                        @php $color = $r->duration_minutes > 30 ? 'danger' : ($r->duration_minutes > 15 ? 'warning' : 'secondary'); @endphp
                        <span class="badge bg-{{ $color }} {{ $color === 'warning' ? 'text-dark' : '' }}">{{ $r->duration_minutes }} mnt</span>
                        @else<span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="small" style="max-width:180px">
                        <span class="text-truncate d-block" title="{{ $r->reason }}">{{ $r->reason ?? '-' }}</span>
                    </td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->officer, 'manualName'=>$r->officer_name, 'extras'=>null])</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('late-records.edit', $r) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('late-records.destroy', $r) }}', '{{ addslashes($r->student?->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-clock fs-1 d-block mb-2 opacity-25"></i>
                        Tidak ada data keterlambatan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="px-3 py-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
        <small class="text-muted">Menampilkan {{ $records->firstItem() }}–{{ $records->lastItem() }} dari {{ $records->total() }}</small>
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
                <p class="mb-1">Yakin ingin menghapus data keterlambatan:</p>
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
new Chart(document.getElementById('lateChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Jumlah Terlambat',
            data: {!! json_encode($chartData) !!},
            backgroundColor: 'rgba(255,193,7,0.7)',
            borderColor: '#ffc107',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

function confirmDelete(url, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteConfirmBtn').onclick = () => document.getElementById('deleteForm').submit();
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
