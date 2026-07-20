@extends('layouts.app')
@section('title', $student->name)
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-person-circle me-2 text-primary"></i>Profil Siswa</h4>
        <small class="text-muted">Detail lengkap dan riwayat</small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if(auth()->user()->canEdit())
        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endif
        <a href="{{ route('late-records.create', ['student_id' => $student->id]) }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-clock-history me-1"></i> Input Terlambat
        </a>
        <a href="{{ route('violation-records.create', ['student_id' => $student->id]) }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-shield-exclamation me-1"></i> Input Pelanggaran
        </a>
        <a href="{{ route('counselings.create', ['student_id' => $student->id]) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-chat-heart me-1"></i> Input Konseling
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Identitas -->
    <div class="col-12 col-lg-4">
        <div class="form-card mb-3 text-center">
            <div class="mb-3">
                @if($student->photo)
                    <img src="{{ Storage::url($student->photo) }}" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;"/>
                @else
                    <div style="width:100px;height:100px;background:#e9ecef;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#6c757d;margin:0 auto">
                        <i class="bi bi-person-fill"></i>
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-0">{{ $student->name }}</h5>
            <p class="text-muted small mb-2">NIS: {{ $student->nis }} @if($student->nisn) | NISN: {{ $student->nisn }}@endif</p>
            @php $statusColor = match($student->status) { 'aktif'=>'success', 'lulus'=>'info', 'pindah'=>'warning', 'keluar'=>'danger', default=>'secondary' }; @endphp
            <span class="badge bg-{{ $statusColor }} px-3 py-1">{{ ucfirst($student->status) }}</span>
            @if($student->location)
            <span class="badge ms-1 px-3 py-1"
                style="{{ $student->location === 'selatan' ? 'background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6' : 'background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5' }}">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ ucfirst($student->location) }}
                — {{ $student->location === 'selatan' ? 'Jl. Cendrawasih' : 'Jl. Sarbini' }}
            </span>
            @endif
        </div>

        <div class="form-card mb-3">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Identitas</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted small" style="width:40%">Kelas</td><td class="fw-semibold small">{{ $student->class?->name ?? '-' }}</td></tr>
                <tr><td class="text-muted small">Lokasi</td><td class="small">
                    @if($student->location === 'selatan')
                        <span class="badge" style="background:#fff0f0;color:#c0392b;border:1px solid #f5c6c6"><i class="bi bi-geo-alt-fill me-1"></i>Selatan — Jl. Cendrawasih</span>
                    @elseif($student->location === 'utara')
                        <span class="badge" style="background:#e8f4fd;color:#1565c0;border:1px solid #b8d9f5"><i class="bi bi-geo-alt-fill me-1"></i>Utara — Jl. Sarbini</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td></tr>
                <tr><td class="text-muted small">Wali Kelas</td><td class="small">{{ $student->class?->homeroomTeacher?->name ?? '-' }}</td></tr>
                <tr><td class="text-muted small">JK</td><td class="small">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><td class="text-muted small">TTL</td><td class="small">{{ $student->birth_place ? $student->birth_place.', ' : '' }}{{ $student->birth_date?->translatedFormat('d M Y') ?? '-' }}</td></tr>
                <tr><td class="text-muted small">Agama</td><td class="small">{{ $student->religion ?? '-' }}</td></tr>
                <tr><td class="text-muted small">Alamat</td><td class="small">{{ $student->address ?? '-' }}</td></tr>
                <tr><td class="text-muted small">No. HP</td><td class="small">{{ $student->phone ?? '-' }}</td></tr>
                <tr><td class="text-muted small">Orang Tua</td><td class="small">{{ $student->parent_name ?? '-' }}</td></tr>
                <tr><td class="text-muted small">HP Ortu</td><td class="small">{{ $student->parent_phone ?? '-' }}</td></tr>
            </table>
        </div>

        <!-- Statistik -->
        <div class="form-card">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-bar-chart me-2"></i>Statistik</h6>
            <div class="row g-2 text-center">
                <div class="col-4">
                    <div class="bg-warning bg-opacity-10 rounded p-2">
                        <div class="fw-bold fs-4 text-warning">{{ $stats['late_count'] }}</div>
                        <small class="text-muted">Terlambat</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-danger bg-opacity-10 rounded p-2">
                        <div class="fw-bold fs-4 text-danger">{{ $stats['total_points'] }}</div>
                        <small class="text-muted">Poin</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-success bg-opacity-10 rounded p-2">
                        <div class="fw-bold fs-4 text-success">{{ $stats['counseling_count'] }}</div>
                        <small class="text-muted">Konseling</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-purple bg-opacity-10 rounded p-2" style="background:rgba(111,66,193,.1)">
                        <div class="fw-bold fs-4" style="color:#6f42c1">{{ $stats['parent_meeting_count'] }}</div>
                        <small class="text-muted">Pemanggilan</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-info bg-opacity-10 rounded p-2">
                        <div class="fw-bold fs-4 text-info">{{ $stats['home_visit_count'] }}</div>
                        <small class="text-muted">Home Visit</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="col-12 col-lg-8">
        <div class="form-card">
            <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</h6>

            @if($timeline->count())
            <div class="timeline">
                @foreach($timeline as $item)
                @php
                    $typeConfig = match($item['type']) {
                        'late'           => ['color'=>'#ffc107', 'icon'=>'bi-clock-history',         'label'=>'Keterlambatan',     'bg'=>'bg-warning'],
                        'violation'      => ['color'=>'#dc3545', 'icon'=>'bi-shield-exclamation',    'label'=>'Pelanggaran',       'bg'=>'bg-danger'],
                        'counseling'     => ['color'=>'#198754', 'icon'=>'bi-chat-heart-fill',       'label'=>'Konseling',         'bg'=>'bg-success'],
                        'parent_meeting' => ['color'=>'#6f42c1', 'icon'=>'bi-people-fill',           'label'=>'Pemanggilan Ortu',  'bg'=>'bg-purple'],
                        'home_visit'     => ['color'=>'#0dcaf0', 'icon'=>'bi-house-fill',            'label'=>'Home Visit',        'bg'=>'bg-info'],
                        default          => ['color'=>'#6c757d', 'icon'=>'bi-circle',                'label'=>'Aktivitas',         'bg'=>'bg-secondary'],
                    };
                @endphp
                <div class="timeline-item">
                    <div class="timeline-dot" style="color:{{ $typeConfig['color'] }};background:{{ $typeConfig['color'] }}"></div>
                    <div class="timeline-content">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                            <span class="badge {{ $typeConfig['bg'] }} {{ in_array($item['type'],['late','violation']) ? '' : 'text-white' }}">
                                <i class="bi {{ $typeConfig['icon'] }} me-1"></i>{{ $typeConfig['label'] }}
                            </span>
                            <small class="text-muted">{{ $item['date']?->translatedFormat('d M Y') }}</small>
                        </div>

                        @if($item['type'] === 'late')
                            <p class="mb-1 small"><strong>Alasan:</strong> {{ $item['data']->reason ?? '-' }}</p>
                            @if($item['data']->duration_minutes)
                            <p class="mb-0 small"><strong>Durasi:</strong> {{ $item['data']->duration_minutes }} menit | <strong>Petugas:</strong> @include('partials.staff-name', ['primary'=>$item['data']->officer, 'manualName'=>$item['data']->officer_name, 'extras'=>null])</p>
                            @endif

                        @elseif($item['type'] === 'violation')
                            <p class="mb-1 small"><strong>Pelanggaran:</strong> {{ $item['data']->violationCategory?->name }}</p>
                            <p class="mb-1 small"><strong>Kronologi:</strong> {{ $item['data']->description ?? '-' }}</p>
                            <p class="mb-0 small"><strong>Poin:</strong> <span class="badge bg-danger">+{{ $item['data']->points }}</span> | <strong>Pelapor:</strong> @include('partials.staff-name', ['primary'=>$item['data']->reporter, 'manualName'=>$item['data']->reporter_name, 'extras'=>null])</p>

                        @elseif($item['type'] === 'counseling')
                            <p class="mb-1 small"><strong>Masalah:</strong> {{ $item['data']->problem }}</p>
                            @if($item['data']->solution)
                            <p class="mb-1 small"><strong>Solusi:</strong> {{ $item['data']->solution }}</p>
                            @endif
                            <p class="mb-0 small"><strong>Guru BK:</strong> @include('partials.staff-name', ['primary'=>$item['data']->counselor, 'manualName'=>$item['data']->counselor_name, 'extras'=>$item['data']->extra_counselors])</p>

                        @elseif($item['type'] === 'parent_meeting')
                            <p class="mb-1 small"><strong>Alasan:</strong> {{ $item['data']->reason }}</p>
                            <p class="mb-1 small">
                                <strong>Kehadiran:</strong>
                                <span class="badge {{ $item['data']->parent_attended ? 'bg-success' : 'bg-danger' }}">
                                    {{ $item['data']->parent_attended ? 'Hadir' : 'Tidak Hadir' }}
                                </span>
                            </p>
                            @if($item['data']->agreement)
                            <p class="mb-0 small"><strong>Kesepakatan:</strong> {{ $item['data']->agreement }}</p>
                            @endif

                        @elseif($item['type'] === 'home_visit')
                            <p class="mb-1 small"><strong>Tujuan:</strong> {{ $item['data']->purpose }}</p>
                            @if($item['data']->conclusion)
                            <p class="mb-0 small"><strong>Kesimpulan:</strong> {{ $item['data']->conclusion }}</p>
                            @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                Belum ada riwayat untuk siswa ini.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
