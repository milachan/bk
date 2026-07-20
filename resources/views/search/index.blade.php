@extends('layouts.app')
@section('title', 'Pencarian')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-search me-2 text-primary"></i>Pencarian Global</h4>
        @isset($q)
        <small class="text-muted">
            Hasil untuk: <strong>"{{ $q }}"</strong>
            @isset($totalResults)
             — {{ $totalResults }} total hasil
            @endisset
        </small>
        @endisset
    </div>
</div>

{{-- Search Form --}}
<div class="form-card mb-4">
    <form method="GET" action="{{ route('search') }}" class="d-flex gap-2">
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control" placeholder="Cari nama siswa, NIS, riwayat..."
                value="{{ $q ?? '' }}" autofocus/>
            <button type="submit" class="btn btn-primary px-4">Cari</button>
        </div>
    </form>
</div>

@if(!isset($q) || $q === '')
    {{-- Empty state: no search query --}}
    <div class="form-card text-center py-5 text-muted">
        <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-0">Masukkan kata kunci untuk mulai pencarian</p>
        <small>Anda bisa mencari berdasarkan nama siswa, NIS, atau riwayat kegiatan</small>
    </div>

@elseif(isset($totalResults) && $totalResults == 0)
    {{-- No results --}}
    <div class="form-card text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-0 fw-semibold">Tidak ada hasil untuk "{{ $q }}"</p>
        <small>Coba gunakan kata kunci yang berbeda</small>
    </div>

@else
    {{-- Section: Data Siswa --}}
    @if(isset($students) && $students->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-people-fill me-2 text-primary"></i>Data Siswa ({{ $students->count() }} hasil)</h6>
        <div class="row g-3">
            @foreach($students as $s)
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('students.show', $s) }}" class="text-decoration-none">
                    <div class="form-card h-100 d-flex align-items-center gap-3 p-3 transition-hover"
                        style="transition: box-shadow .2s; cursor:pointer;"
                        onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.1)'"
                        onmouseout="this.style.boxShadow=''">
                        {{-- Avatar / Foto --}}
                        <div style="width:50px;height:50px;flex-shrink:0;border-radius:.6rem;overflow:hidden;background:#e9ecef;display:flex;align-items:center;justify-content:center;">
                            @if($s->photo)
                                <img src="{{ asset('storage/'.$s->photo) }}" alt="{{ $s->name }}" style="width:100%;height:100%;object-fit:cover"/>
                            @else
                                <span style="font-size:1.3rem;font-weight:700;color:#6c757d">{{ strtoupper(substr($s->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-semibold text-dark text-truncate">{{ $s->name }}</div>
                            <small class="text-muted">NIS: {{ $s->nis }}</small>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge bg-light text-dark">{{ $s->class?->name ?? '-' }}</span>
                                @if($s->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section: Keterlambatan --}}
    @if(isset($lateRecords) && $lateRecords->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-warning"></i>Keterlambatan ({{ $lateRecords->count() }} terbaru)</h6>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lateRecords as $lr)
                        <tr>
                            <td class="ps-3 small">{{ $lr->date?->format('d/m/Y') }}</td>
                            <td class="small fw-semibold">
                                <a href="{{ route('students.show', $lr->student) }}" class="text-decoration-none">{{ $lr->student?->name }}</a>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $lr->student?->class?->name ?? '-' }}</span></td>
                            <td><span class="badge bg-warning text-dark">{{ $lr->duration_minutes ?? '-' }} mnt</span></td>
                            <td class="small text-muted">{{ $lr->reason ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Section: Pelanggaran --}}
    @if(isset($violationRecords) && $violationRecords->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Pelanggaran ({{ $violationRecords->count() }} terbaru)</h6>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($violationRecords as $vr)
                        <tr>
                            <td class="ps-3 small">{{ $vr->date?->format('d/m/Y') }}</td>
                            <td class="small fw-semibold">
                                <a href="{{ route('students.show', $vr->student) }}" class="text-decoration-none">{{ $vr->student?->name }}</a>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $vr->student?->class?->name ?? '-' }}</span></td>
                            <td class="small">{{ $vr->violationCategory?->name ?? '-' }}</td>
                            <td><span class="badge bg-danger">{{ $vr->points }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Section: Konseling --}}
    @if(isset($counselings) && $counselings->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-chat-heart-fill me-2 text-success"></i>Konseling ({{ $counselings->count() }} terbaru)</h6>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Siswa</th>
                            <th>Masalah</th>
                            <th>Guru BK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($counselings as $c)
                        <tr>
                            <td class="ps-3 small">{{ $c->date?->format('d/m/Y') }}</td>
                            <td class="small fw-semibold">
                                <a href="{{ route('students.show', $c->student) }}" class="text-decoration-none">{{ $c->student?->name }}</a>
                            </td>
                            <td class="small text-truncate" style="max-width:200px" title="{{ $c->problem }}">{{ $c->problem }}</td>
                            <td class="small">{{ $c->counselor?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Section: Pemanggilan Orang Tua --}}
    @if(isset($parentMeetings) && $parentMeetings->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-people-fill me-2 text-info"></i>Pemanggilan Orang Tua ({{ $parentMeetings->count() }} terbaru)</h6>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Siswa</th>
                            <th>Alasan</th>
                            <th>Kehadiran</th>
                            <th>Penanganan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parentMeetings as $pm)
                        <tr>
                            <td class="ps-3 small">{{ $pm->meeting_date?->format('d/m/Y') }}</td>
                            <td class="small fw-semibold">
                                <a href="{{ route('students.show', $pm->student) }}" class="text-decoration-none">{{ $pm->student?->name }}</a>
                            </td>
                            <td class="small text-truncate" style="max-width:180px" title="{{ $pm->reason }}">{{ $pm->reason }}</td>
                            <td>
                                @if($pm->parent_attended)
                                    <span class="badge bg-success">Hadir</span>
                                @else
                                    <span class="badge bg-danger">Tidak Hadir</span>
                                @endif
                            </td>
                            <td class="small">{{ $pm->handler?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endif
@endsection
