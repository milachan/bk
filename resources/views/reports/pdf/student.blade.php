<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Rekap Siswa</title>
@include('reports.pdf._base', ['accentColor'=>'#2c3e50','accentBorder'=>'#1a252f','accentBg'=>'#f8f9fa'])
</head>
<body>

<div class="pdf-header">
    <div class="school">{{ config('app.school_name','SEKOLAH MENENGAH ATAS') }}</div>
    <div class="title">REKAP DATA SISWA — BIMBINGAN KONSELING</div>
    <div class="period">
        @if(isset($year) && $year) Tahun {{ $year }} @else Semua Periode @endif
        @if(isset($class) && $class) &nbsp;|&nbsp; Kelas: {{ $class->name }} @endif
        @if(isset($location) && $location) &nbsp;|&nbsp; Lokasi: {{ ucfirst($location) }} @endif
    </div>
</div>

<div class="pdf-meta">
    <span class="m-item">Total Siswa: <strong>{{ count($data) }}</strong></span>
    <span class="m-item">Total Poin Pelanggaran: <strong>{{ $data->sum('violation_records_sum_points') }}</strong></span>
    <span class="m-item">Total Keterlambatan: <strong>{{ $data->sum('late_records_count') }}x</strong></span>
    <span class="m-item">Dicetak: <strong>{{ now()->translatedFormat('d F Y, H:i') }}</strong></span>
    <span class="m-item">Oleh: <strong>{{ auth()->user()->name ?? '-' }}</strong></span>
</div>

@if($data->count() > 0)
<table class="dt">
    <thead>
        <tr>
            <th style="width:24px">No</th>
            <th style="width:60px">NIS</th>
            <th style="width:150px">Nama Siswa</th>
            <th style="width:65px">Kelas</th>
            <th style="width:55px">Lokasi</th>
            <th style="width:70px" class="tc">Terlambat</th>
            <th style="width:70px" class="tc">Total Poin</th>
            <th style="width:65px" class="tc">Konseling</th>
            <th style="width:65px" class="tc">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td class="tc">{{ $i+1 }}</td>
            <td>{{ $r->nis }}</td>
            <td>{{ $r->name }}</td>
            <td class="tc">{{ $r->class?->name ?? '-' }}</td>
            <td class="tc">
                @if($r->location === 'selatan')<span class="b-loc-s">Selatan</span>
                @elseif($r->location === 'utara')<span class="b-loc-u">Utara</span>
                @else - @endif
            </td>
            <td class="tc">{{ $r->late_records_count ?? 0 }}x</td>
            <td class="tc"><strong>{{ $r->violation_records_sum_points ?? 0 }}</strong></td>
            <td class="tc">{{ $r->counselings_count ?? 0 }}x</td>
            <td class="tc">{{ ucfirst($r->status) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Tidak ada data siswa untuk filter yang dipilih.</div>
@endif

<div class="pdf-footer">
    <span class="left">BK Digital — Sistem Informasi Bimbingan Konseling</span>
    <span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span>
</div>
</body>
</html>
