@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard
            @if($filterLocation === 'selatan')
                <span class="badge ms-2" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;font-size:.7rem;vertical-align:middle"><i class="bi bi-geo-alt-fill me-1"></i>Selatan</span>
            @elseif($filterLocation === 'utara')
                <span class="badge ms-2" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5;font-size:.7rem;vertical-align:middle"><i class="bi bi-geo-alt-fill me-1"></i>Utara</span>
            @endif
        </h4>
        <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('late-records.create') }}" class="btn btn-sm btn-primary no-print">
            <i class="bi bi-clock-history me-1"></i> Input Terlambat
        </a>
        <a href="{{ route('violation-records.create') }}" class="btn btn-sm btn-danger no-print">
            <i class="bi bi-shield-exclamation me-1"></i> Input Pelanggaran
        </a>
    </div>
</div>

{{-- Filter Tanggal / Bulan --}}
<div class="form-card mb-3 no-print">
    <form method="GET" id="dashFilterForm" class="row g-2 align-items-end">

        {{-- Filter Lokasi --}}
        <div class="col-12 col-md-auto">
            <label class="form-label form-label-sm mb-1 text-muted fw-semibold">Lokasi</label>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="filter_location" id="flAll" value=""
                    {{ !$filterLocation ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-secondary" for="flAll">Semua</label>

                <input type="radio" class="btn-check" name="filter_location" id="flSelatan" value="selatan"
                    {{ $filterLocation === 'selatan' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-danger" for="flSelatan">
                    <i class="bi bi-geo-alt-fill me-1"></i>Selatan
                </label>

                <input type="radio" class="btn-check" name="filter_location" id="flUtara" value="utara"
                    {{ $filterLocation === 'utara' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-primary" for="flUtara">
                    <i class="bi bi-geo-alt-fill me-1"></i>Utara
                </label>
            </div>
        </div>

        <div class="col-auto d-none d-md-flex align-items-end pb-1 text-muted" style="font-size:1.2rem">|</div>

        {{-- Filter Waktu --}}
        <div class="col-12 col-md-auto">
            <label class="form-label form-label-sm mb-1 text-muted fw-semibold">Tampilkan Data</label>
            <div class="btn-group btn-group-sm" role="group">
                <input type="radio" class="btn-check" name="filter_mode" id="fmAll" value=""
                    {{ !$filterMode ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-primary" for="fmAll">Semua</label>

                <input type="radio" class="btn-check" name="filter_mode" id="fmDay" value="day"
                    {{ $filterMode === 'day' ? 'checked' : '' }} onchange="toggleFilterFields()">
                <label class="btn btn-outline-primary" for="fmDay"><i class="bi bi-calendar-day me-1"></i>Per Hari</label>

                <input type="radio" class="btn-check" name="filter_mode" id="fmMonth" value="month"
                    {{ $filterMode === 'month' ? 'checked' : '' }} onchange="toggleFilterFields()">
                <label class="btn btn-outline-primary" for="fmMonth"><i class="bi bi-calendar-month me-1"></i>Per Bulan</label>
            </div>
        </div>

        <div class="col-auto" id="fieldDay" style="{{ $filterMode === 'day' ? '' : 'display:none' }}">
            <label class="form-label form-label-sm mb-1 text-muted">Pilih Tanggal</label>
            <input type="date" name="filter_date" class="form-control form-control-sm"
                value="{{ $filterDate ? $filterDate->format('Y-m-d') : now()->format('Y-m-d') }}"
                onchange="this.form.submit()"/>
        </div>

        <div class="col-auto" id="fieldMonth" style="{{ $filterMode === 'month' ? '' : 'display:none' }}">
            <label class="form-label form-label-sm mb-1 text-muted">Pilih Bulan & Tahun</label>
            <input type="month" name="filter_month" class="form-control form-control-sm"
                value="{{ ($filterMonth && $filterYear) ? $filterYear.'-'.str_pad($filterMonth,2,'0',STR_PAD_LEFT) : now()->format('Y-m') }}"
                onchange="this.form.submit()"/>
        </div>

        {{-- Label aktif + reset --}}
        @if($locationLabel || $filterLabel)
        <div class="col-12 col-md-auto d-flex flex-wrap gap-1 align-items-center">
            @if($locationLabel)
            <span class="badge px-3 py-2"
                style="{{ $filterLocation === 'selatan' ? 'background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6' : 'background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5' }}">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $locationLabel }}
            </span>
            @endif
            @if($filterLabel)
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                <i class="bi bi-calendar-fill me-1"></i>{{ $filterLabel }}
            </span>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm" title="Reset semua filter">
                <i class="bi bi-x-lg"></i> Reset
            </a>
        </div>
        @endif

    </form>
</div>

<!-- Stat Cards Row 1 -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="icon bg-white bg-opacity-25"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="label text-white-50">Total Siswa Aktif</div>
                <div class="value">{{ number_format($stats['total_students']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-warning text-dark">
            <div class="icon bg-dark bg-opacity-10"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="label">{{ $filterMode === 'day' ? 'Terlambat '.$filterDate?->translatedFormat('d M') : 'Terlambat Hari Ini' }}</div>
                <div class="value">{{ $stats['late_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="icon bg-white bg-opacity-25"><i class="bi bi-calendar-week"></i></div>
            <div>
                <div class="label text-white-50">
                    @if($filterMode === 'day') Terlambat {{ $filterDate?->translatedFormat('d M') }}
                    @elseif($filterMode === 'month') Terlambat {{ $filterLabel }}
                    @else Terlambat Bulan Ini
                    @endif
                </div>
                <div class="value">{{ $stats['late_this_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-danger text-white">
            <div class="icon bg-white bg-opacity-25"><i class="bi bi-shield-exclamation"></i></div>
            <div>
                <div class="label text-white-50">
                    @if($filterMode === 'day') Pelanggaran {{ $filterDate?->translatedFormat('d M') }}
                    @elseif($filterMode === 'month') Pelanggaran {{ $filterLabel }}
                    @else Pelanggaran Bulan Ini
                    @endif
                </div>
                <div class="value">{{ $stats['violations_this_month'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards Row 2 -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card bg-success text-white">
            <div class="icon bg-white bg-opacity-25"><i class="bi bi-chat-heart-fill"></i></div>
            <div>
                <div class="label text-white-50">
                    @if($filterMode === 'day') Konseling {{ $filterDate?->translatedFormat('d M') }}
                    @elseif($filterMode === 'month') Konseling {{ $filterLabel }}
                    @else Konseling Bulan Ini
                    @endif
                </div>
                <div class="value">{{ $stats['counselings_this_month'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card" style="background:#6f42c1;color:#fff">
            <div class="icon" style="background:rgba(255,255,255,.2)"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="label" style="color:rgba(255,255,255,.7)">
                    @if($filterMode === 'day') Pemanggilan Ortu {{ $filterDate?->translatedFormat('d M') }}
                    @elseif($filterMode === 'month') Pemanggilan Ortu {{ $filterLabel }}
                    @else Pemanggilan Ortu
                    @endif
                </div>
                <div class="value">{{ $stats['parent_meetings'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card" style="background:#fd7e14;color:#fff">
            <div class="icon" style="background:rgba(255,255,255,.2)"><i class="bi bi-house-fill"></i></div>
            <div>
                <div class="label" style="color:rgba(255,255,255,.7)">
                    @if($filterMode === 'day') Home Visit {{ $filterDate?->translatedFormat('d M') }}
                    @elseif($filterMode === 'month') Home Visit {{ $filterLabel }}
                    @else Home Visit
                    @endif
                </div>
                <div class="value">{{ $stats['home_visits'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-3">
    <div class="col-12 col-lg-8">
        <div class="table-card p-3">
            <div class="card-header-custom">
                <span><i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Tren 6 Bulan Terakhir</span>
            </div>
            <canvas id="trendChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="table-card p-3 h-100">
            <div class="card-header-custom mb-2">
                <span><i class="bi bi-pie-chart-fill me-2 text-danger"></i>Pelanggaran per Kelas</span>
            </div>
            <canvas id="classChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Widgets Row -->
<div class="row g-3">
    <!-- Terlambat Hari Ini -->
    <div class="col-12 col-lg-6">
        <div class="table-card">
            <div class="card-header-custom">
                <span>
                    <i class="bi bi-clock-history me-2 text-warning"></i>
                    @if($filterMode === 'day' && $filterDate)
                        Terlambat {{ $filterDate->translatedFormat('d F Y') }}
                    @elseif($filterMode === 'month' && $filterMonth)
                        Terlambat {{ $filterLabel }}
                    @else
                        Terlambat Hari Ini
                    @endif
                </span>
                <a href="{{ route('late-records.create') }}" class="btn btn-sm btn-warning no-print">
                    <i class="bi bi-plus"></i> Tambah
                </a>
            </div>
            @if($lateToday->count())
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light"><tr><th>Siswa</th><th>Kelas</th><th>Lokasi</th><th>Jam Datang</th><th>Durasi</th></tr></thead>
                    <tbody>
                        @foreach($lateToday as $r)
                        <tr>
                            <td><a href="{{ route('students.show', $r->student) }}" class="text-decoration-none fw-semibold">{{ $r->student?->name }}</a></td>
                            <td><span class="badge bg-light text-dark">{{ $r->student?->class?->name ?? '-' }}</span></td>
                            <td>
                                @if($r->student?->location === 'selatan')
                                    <span class="badge" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> S</span>
                                @elseif($r->student?->location === 'utara')
                                    <span class="badge" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> U</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $r->arrive_time }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $r->duration_minutes }} mnt</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-check-circle fs-2 text-success d-block mb-2"></i>
                Tidak ada keterlambatan hari ini
            </div>
            @endif
        </div>
    </div>

    <!-- Siswa Poin Tertinggi -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="table-card">
            <div class="card-header-custom">
                <span>
                    <i class="bi bi-trophy-fill me-2 text-danger"></i>
                    Poin Tertinggi
                    @if($filterLabel)<small class="text-muted fw-normal ms-1" style="font-size:.75rem">{{ $filterLabel }}</small>@endif
                </span>
            </div>
            @if($topViolators->count())
            <ul class="list-group list-group-flush">
                @foreach($topViolators as $i => $s)
                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                    <span class="badge bg-{{ $i == 0 ? 'danger' : ($i == 1 ? 'warning text-dark' : 'secondary') }} rounded-pill">{{ $i + 1 }}</span>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none text-truncate d-block fw-semibold small">{{ $s->name }}</a>
                        <small class="text-muted">{{ $s->class?->name ?? '-' }}</small>
                    </div>
                    <span class="badge bg-danger">{{ $s->violation_records_sum_points ?? 0 }} poin</span>
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center py-4 text-muted"><small>Belum ada data</small></div>
            @endif
        </div>
    </div>

    <!-- Paling Sering Terlambat -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="table-card">
            <div class="card-header-custom">
                <span>
                    <i class="bi bi-hourglass-split me-2 text-warning"></i>
                    Sering Terlambat
                    @if($filterLabel)<small class="text-muted fw-normal ms-1" style="font-size:.75rem">{{ $filterLabel }}</small>@endif
                </span>
            </div>
            @if($mostLate->count())
            <ul class="list-group list-group-flush">
                @foreach($mostLate as $i => $s)
                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                    <span class="badge bg-{{ $i == 0 ? 'warning text-dark' : 'secondary' }} rounded-pill">{{ $i + 1 }}</span>
                    <div class="flex-grow-1 min-w-0">
                        <a href="{{ route('students.show', $s) }}" class="text-decoration-none text-truncate d-block fw-semibold small">{{ $s->name }}</a>
                        <small class="text-muted">{{ $s->class?->name ?? '-' }}</small>
                    </div>
                    <span class="badge bg-warning text-dark">{{ $s->late_records_count }}x</span>
                </li>
                @endforeach
            </ul>
            @else
            <div class="text-center py-4 text-muted"><small>Belum ada data</small></div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle filter fields
function toggleFilterFields() {
    const dayRadio   = document.getElementById('fmDay');
    const monthRadio = document.getElementById('fmMonth');
    const fieldDay   = document.getElementById('fieldDay');
    const fieldMonth = document.getElementById('fieldMonth');

    fieldDay.style.display   = dayRadio.checked   ? '' : 'none';
    fieldMonth.style.display = monthRadio.checked ? '' : 'none';

    // auto-submit setelah field muncul agar user bisa pilih tanggal/bulan
    if (!dayRadio.checked && !monthRadio.checked) {
        document.getElementById('dashFilterForm').submit();
    }
}

// Init on load
(function () {
    const dayRadio   = document.getElementById('fmDay');
    const monthRadio = document.getElementById('fmMonth');
    if (dayRadio && dayRadio.checked)     document.getElementById('fieldDay').style.display   = '';
    if (monthRadio && monthRadio.checked) document.getElementById('fieldMonth').style.display = '';
})();

// Trend Chart
const ctx1 = document.getElementById('trendChart');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            {
                label: 'Keterlambatan',
                data: {!! json_encode($lateMonthly) !!},
                borderColor: '#ffc107', backgroundColor: 'rgba(255,193,7,.1)',
                fill: true, tension: .4
            },
            {
                label: 'Pelanggaran',
                data: {!! json_encode($violationMonthly) !!},
                borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,.1)',
                fill: true, tension: .4
            },
            {
                label: 'Konseling',
                data: {!! json_encode($counselingMonthly) !!},
                borderColor: '#198754', backgroundColor: 'rgba(25,135,84,.1)',
                fill: true, tension: .4
            },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Class Chart
@if($violationByClass->count())
const ctx2 = document.getElementById('classChart');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($violationByClass->pluck('class_name')) !!},
        datasets: [{
            data: {!! json_encode($violationByClass->pluck('total')) !!},
            backgroundColor: ['#dc3545','#ffc107','#0d6efd','#198754','#6f42c1','#fd7e14'],
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
@endif
</script>
@endpush
