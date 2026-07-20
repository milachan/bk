@extends('layouts.app')
@section('title', 'Laporan')

@push('styles')
<style>
.rpt-type-btn { border:2px solid #e9ecef; border-radius:.6rem; padding:.5rem .75rem; cursor:pointer; transition:all .15s; background:#fff; text-align:center; }
.rpt-type-btn:hover  { border-color:#6ea8fe; background:#f0f6ff; }
.rpt-type-btn.active { border-color:#0d6efd; background:#e7f1ff; }
[data-bs-theme="dark"] .rpt-type-btn          { background:#2c3034; border-color:#495057; }
[data-bs-theme="dark"] .rpt-type-btn.active   { background:#1a3a5c; border-color:#0d6efd; }
.student-autocomplete { position:relative; }
.student-dropdown {
    position:absolute; top:100%; left:0; right:0; margin-top:2px;
    background:#fff; border:1px solid #dee2e6; border-radius:.5rem;
    box-shadow:0 4px 16px rgba(0,0,0,.12); z-index:1000;
    max-height:220px; overflow-y:auto; display:none;
}
[data-bs-theme="dark"] .student-dropdown { background:#2c3034; border-color:#495057; }
.student-dropdown .sd-item { padding:.45rem .9rem; cursor:pointer; border-bottom:1px solid #f2f2f2; font-size:.84rem; }
[data-bs-theme="dark"] .sd-item { border-color:#3d4246; }
.sd-item:hover { background:#f0f6ff; }
[data-bs-theme="dark"] .sd-item:hover { background:#1a3a5c; }
.sd-item:last-child { border-bottom:none; }
</style>
@endpush

@section('content')
<div class="page-header mb-3">
    <div>
        <h4 class="mb-0"><i class="bi bi-file-earmark-bar-graph-fill me-2 text-primary"></i>Laporan BK</h4>
        <small class="text-muted">Filter, visualisasi, dan ekspor data bimbingan konseling</small>
    </div>
</div>

{{-- ═══ FILTER FORM ═══ --}}
<div class="form-card mb-3">
<form method="GET" action="{{ route('reports.index') }}" id="reportForm">

    {{-- Jenis Laporan --}}
    <div class="mb-3">
        <label class="form-label form-label-sm fw-semibold text-muted mb-2">Jenis Laporan</label>
        @php
        $types = [
            'late'           => ['bi-clock-history',      '#ffc107', 'Keterlambatan'],
            'violation'      => ['bi-shield-exclamation', '#dc3545', 'Pelanggaran'],
            'counseling'     => ['bi-chat-heart-fill',    '#198754', 'Konseling'],
            'parent_meeting' => ['bi-people-fill',        '#0dcaf0', 'Pemanggilan Ortu'],
            'home_visit'     => ['bi-house-fill',         '#6f42c1', 'Home Visit'],
            'student'        => ['bi-person-lines-fill',  '#6c757d', 'Rekap Siswa'],
        ];
        @endphp
        <div class="d-flex flex-wrap gap-2">
            @foreach($types as $val => [$icon, $color, $label])
            <label class="rpt-type-btn {{ $type == $val ? 'active' : '' }}" style="min-width:90px">
                <input type="radio" name="type" value="{{ $val }}" class="d-none" {{ $type == $val ? 'checked' : '' }}
                    onchange="this.closest('form').submit()">
                <i class="bi {{ $icon }} d-block mb-1" style="color:{{ $color }};font-size:1.25rem"></i>
                <span style="font-size:.75rem;font-weight:600">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Filter Row --}}
    <div class="row g-2 align-items-end">
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Kelas</label>
            <select name="class_id" class="form-select form-select-sm">
                <option value="">Semua Kelas</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Lokasi</label>
            <select name="location" class="form-select form-select-sm">
                <option value="">Semua Lokasi</option>
                <option value="selatan" {{ ($location ?? '') === 'selatan' ? 'selected' : '' }}>🔴 Selatan</option>
                <option value="utara"   {{ ($location ?? '') === 'utara'   ? 'selected' : '' }}>🔵 Utara</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label form-label-sm mb-1 text-muted">Siswa</label>
            <div class="student-autocomplete">
                <input type="text" id="reportStudentSearch" class="form-control form-control-sm"
                    placeholder="Ketik nama siswa..." autocomplete="off"
                    value="{{ $studentId ? ($students->find($studentId)?->name ?? '') : '' }}">
                <input type="hidden" name="student_id" id="reportStudentId" value="{{ $studentId }}">
                <div class="student-dropdown" id="reportStudentDropdown"></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label form-label-sm mb-1 text-muted">Bulan</label>
            <select name="month" class="form-select form-select-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label form-label-sm mb-1 text-muted">Tahun</label>
            <select name="year" class="form-select form-select-sm">
                @foreach(range(date('Y'), date('Y')-5) as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-search me-1"></i>Tampilkan
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>

    {{-- Export Buttons --}}
    @if(isset($data) && $data->count() > 0)
    <div class="mt-3 pt-3 border-top d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('reports.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-pdf-fill me-1"></i>Export PDF
        </a>
        <a href="{{ route('reports.excel', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel
        </a>
        <small class="text-muted ms-1"><i class="bi bi-info-circle me-1"></i>PDF & Excel mengikuti filter aktif</small>
    </div>
    @endif

</form>
</div>

{{-- ═══ STATS + CHART ═══ --}}
@if(isset($data))
<div class="row g-3 mb-3">
    {{-- Stat cards --}}
    <div class="col-12 col-md-4">
        <div class="row g-2 h-100">
            <div class="col-4">
                <div class="p-3 rounded-3 border text-center h-100 d-flex flex-column justify-content-center" style="background:#f0f6ff">
                    <div class="fw-bold fs-3 text-primary lh-1">{{ $stats['total'] }}</div>
                    <div class="text-muted mt-1" style="font-size:.72rem">
                        @if($month) Hasil Filter @else Total @endif
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 rounded-3 border text-center h-100 d-flex flex-column justify-content-center" style="background:#fff8e1">
                    <div class="fw-bold fs-3 lh-1" style="color:#e6a817">{{ $stats['this_month'] }}</div>
                    <div class="text-muted mt-1" style="font-size:.72rem">Bulan Ini</div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-3 rounded-3 border text-center h-100 d-flex flex-column justify-content-center" style="background:#f0fdf4">
                    <div class="fw-bold fs-3 text-success lh-1">{{ $stats['this_year'] }}</div>
                    <div class="text-muted mt-1" style="font-size:.72rem">Tahun {{ now()->year }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Chart --}}
    <div class="col-12 col-md-8">
        <div class="table-card p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-semibold text-muted">
                    <i class="bi bi-bar-chart-fill me-1 text-primary"></i>Tren 6 Bulan Terakhir
                    @php
                    $typeLabel = ['late'=>'Keterlambatan','violation'=>'Pelanggaran','counseling'=>'Konseling','parent_meeting'=>'Pemanggilan Ortu','home_visit'=>'Home Visit','student'=>'Rekap Siswa'][$type] ?? $type;
                    $typeColor = ['late'=>'#ffc107','violation'=>'#dc3545','counseling'=>'#198754','parent_meeting'=>'#0dcaf0','home_visit'=>'#6f42c1','student'=>'#6c757d'][$type] ?? '#0d6efd';
                    @endphp
                    — {{ $typeLabel }}
                </span>
            </div>
            <canvas id="reportTrendChart" height="55"></canvas>
        </div>
    </div>
</div>

{{-- ═══ TABEL DATA ═══ --}}
<div class="table-card">
    <div class="card-header-custom">
        <span class="small fw-semibold">
            <i class="bi bi-table me-1"></i>
            Hasil Laporan {{ $typeLabel }}
            @if($month) — {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }} @endif
        </span>
        <span class="badge bg-primary">{{ $data->count() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                @if($type == 'late')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:85px">Tanggal</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th style="width:80px">Durasi</th>
                    <th>Alasan</th>
                    <th>Dicatat Oleh</th>
                </tr>
                @elseif($type == 'violation')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:85px">Tanggal</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th>Jenis Pelanggaran</th>
                    <th style="width:75px">Kategori</th>
                    <th style="width:55px">Poin</th>
                </tr>
                @elseif($type == 'counseling')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:85px">Tanggal</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th>Masalah</th>
                    <th>Guru BK</th>
                </tr>
                @elseif($type == 'parent_meeting')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:85px">Tanggal</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th>Alasan</th>
                    <th style="width:85px">Ortu Hadir</th>
                    <th>Penangani</th>
                </tr>
                @elseif($type == 'home_visit')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:85px">Tanggal</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th>Tujuan Kunjungan</th>
                    <th>Petugas</th>
                </tr>
                @elseif($type == 'student')
                <tr>
                    <th class="ps-3" style="width:35px">#</th>
                    <th style="width:75px">NIS</th>
                    <th>Nama Siswa</th>
                    <th style="width:80px">Kelas</th>
                    <th style="width:70px">Lokasi</th>
                    <th style="width:80px" class="text-center">Terlambat</th>
                    <th style="width:75px" class="text-center">Total Poin</th>
                    <th style="width:75px" class="text-center">Konseling</th>
                </tr>
                @endif
            </thead>
            <tbody>
            @php
            $locBadge = function($loc) {
                if ($loc === 'selatan') return '<span class="badge" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> S</span>';
                if ($loc === 'utara')   return '<span class="badge" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5;font-size:.65rem"><i class="bi bi-geo-alt-fill"></i> U</span>';
                return '<span class="text-muted small">-</span>';
            };
            @endphp
            @forelse($data as $i => $r)
                @if($type == 'late')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small text-nowrap">{{ $r->date?->format('d/m/Y') }}</td>
                    <td class="small fw-semibold">{{ $r->student?->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->student?->location) !!}</td>
                    <td><span class="badge bg-warning text-dark">{{ $r->duration_minutes ?? '-' }} mnt</span></td>
                    <td class="small">{{ $r->reason ?? '-' }}</td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->officer, 'manualName'=>$r->officer_name, 'extras'=>null])</td>
                </tr>
                @elseif($type == 'violation')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small text-nowrap">{{ $r->date?->format('d/m/Y') }}</td>
                    <td class="small fw-semibold">{{ $r->student?->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->student?->location) !!}</td>
                    <td class="small">{{ $r->violationCategory?->name ?? '-' }}</td>
                    <td>
                        @php $cat = $r->violationCategory?->category ?? '' @endphp
                        @if($cat=='ringan')<span class="badge" style="background:#d1e7dd;color:#0f5132">Ringan</span>
                        @elseif($cat=='sedang')<span class="badge" style="background:#fff3cd;color:#664d03">Sedang</span>
                        @elseif($cat=='berat')<span class="badge" style="background:#f8d7da;color:#842029">Berat</span>
                        @else<span class="badge bg-secondary">-</span>@endif
                    </td>
                    <td><span class="badge bg-danger fw-bold">{{ $r->points }}</span></td>
                </tr>
                @elseif($type == 'counseling')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small text-nowrap">{{ $r->date?->format('d/m/Y') }}</td>
                    <td class="small fw-semibold">{{ $r->student?->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->student?->location) !!}</td>
                    <td class="small" style="max-width:220px"><span class="text-truncate d-block" title="{{ $r->problem }}">{{ $r->problem }}</span></td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->counselor, 'manualName'=>$r->counselor_name, 'extras'=>$r->extra_counselors])</td>
                </tr>
                @elseif($type == 'parent_meeting')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small text-nowrap">{{ $r->meeting_date?->format('d/m/Y') }}</td>
                    <td class="small fw-semibold">{{ $r->student?->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->student?->location) !!}</td>
                    <td class="small" style="max-width:180px"><span class="text-truncate d-block">{{ $r->reason }}</span></td>
                    <td>@if($r->parent_attended)<span class="badge bg-success">Hadir</span>@else<span class="badge bg-danger">Tdk Hadir</span>@endif</td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->handler, 'manualName'=>$r->handler_name, 'extras'=>$r->extra_handlers])</td>
                </tr>
                @elseif($type == 'home_visit')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small text-nowrap">{{ $r->visit_date?->format('d/m/Y') }}</td>
                    <td class="small fw-semibold">{{ $r->student?->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->student?->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->student?->location) !!}</td>
                    <td class="small" style="max-width:220px"><span class="text-truncate d-block">{{ $r->purpose }}</span></td>
                    <td class="small text-muted">@include('partials.staff-name', ['primary'=>$r->visitor, 'manualName'=>$r->visitor_name, 'extras'=>$r->extra_visitors])</td>
                </tr>
                @elseif($type == 'student')
                <tr>
                    <td class="ps-3 text-muted small">{{ $i+1 }}</td>
                    <td class="small">{{ $r->nis }}</td>
                    <td class="small fw-semibold">{{ $r->name }}</td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $r->class?->name ?? '-' }}</span></td>
                    <td>{!! $locBadge($r->location) !!}</td>
                    <td class="text-center"><span class="badge bg-warning text-dark">{{ $r->late_records_count ?? 0 }}x</span></td>
                    <td class="text-center"><span class="badge bg-danger">{{ $r->violation_records_sum_points ?? 0 }}</span></td>
                    <td class="text-center"><span class="badge bg-success">{{ $r->counselings_count ?? 0 }}x</span></td>
                </tr>
                @endif
            @empty
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                    Tidak ada data untuk filter yang dipilih
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="form-card text-center py-5 text-muted">
    <i class="bi bi-file-earmark-bar-graph fs-1 d-block mb-3 opacity-20"></i>
    <p class="mb-0">Pilih filter dan klik <strong>Tampilkan</strong> untuk melihat laporan</p>
</div>
@endif

@endsection

@push('scripts')
<script>
// ── Mini trend chart ──
@if(isset($chartLabels))
new Chart(document.getElementById('reportTrendChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: '{{ $typeLabel }}',
            data: {!! json_encode($chartValues) !!},
            backgroundColor: '{{ $typeColor }}' + '99',
            borderColor: '{{ $typeColor }}',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { color: 'rgba(0,0,0,.05)' } },
            x: { ticks: { font: { size: 10 } }, grid: { display: false } }
        }
    }
});
@endif

// ── Autocomplete siswa ──
const SEARCH_URL = '{{ route("api.students.search") }}';
let timer = null;
const inp  = document.getElementById('reportStudentSearch');
const hid  = document.getElementById('reportStudentId');
const drop = document.getElementById('reportStudentDropdown');

inp.addEventListener('input', function () {
    clearTimeout(timer);
    const q = this.value.trim();
    hid.value = '';
    if (q.length < 1) { drop.style.display = 'none'; return; }
    timer = setTimeout(() => {
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                drop.innerHTML = data.length
                    ? data.map(s => `<div class="sd-item" onclick="pickStudent(${s.id},'${esc(s.name)}')">
                        <strong>${esc(s.name)}</strong>
                        <span class="text-muted ms-1">(${esc(s.nis)}) ${esc(s.class)}</span>
                      </div>`).join('')
                    : '<div class="sd-item text-muted">Tidak ditemukan</div>';
                drop.style.display = 'block';
            });
    }, 250);
});

document.addEventListener('click', e => { if (!e.target.closest('.student-autocomplete')) drop.style.display = 'none'; });

function pickStudent(id, name) { hid.value = id; inp.value = name; drop.style.display = 'none'; }
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endpush
