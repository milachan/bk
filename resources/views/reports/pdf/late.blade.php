<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Laporan Keterlambatan</title>
@include('reports.pdf._base', ['accentColor'=>'#1a5276','accentBorder'=>'#154360','accentBg'=>'#eaf4fb'])
</head>
<body>

<div class="pdf-header">
    <div class="school">{{ config('app.school_name','SEKOLAH MENENGAH ATAS') }}</div>
    <div class="title">LAPORAN KETERLAMBATAN SISWA</div>
    <div class="period">
        Periode:
        @if(isset($month) && $month)
            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
        @elseif(isset($year) && $year)
            Tahun {{ $year }}
        @else
            Semua Periode
        @endif
        @if(isset($class) && $class) &nbsp;|&nbsp; Kelas: {{ $class->name }} @endif
        @if(isset($location) && $location) &nbsp;|&nbsp; Lokasi: {{ ucfirst($location) }} @endif
    </div>
</div>

<div class="pdf-meta">
    <span class="m-item">Total Data: <strong>{{ count($data) }}</strong></span>
    @php $avgDur = $data->avg('duration_minutes') @endphp
    @if($avgDur)
    <span class="m-item">Rata-rata Durasi: <strong>{{ round($avgDur) }} mnt</strong></span>
    @endif
    <span class="m-item">Dicetak: <strong>{{ now()->translatedFormat('d F Y, H:i') }}</strong></span>
    <span class="m-item">Oleh: <strong>{{ auth()->user()->name ?? '-' }}</strong></span>
</div>

@if($data->count() > 0)
<table class="dt">
    <thead>
        <tr>
            <th style="width:24px">No</th>
            <th style="width:65px">Tanggal</th>
            <th style="width:45px">NIS</th>
            <th style="width:130px">Nama Siswa</th>
            <th style="width:55px">Kelas</th>
            <th style="width:50px">Lokasi</th>
            <th style="width:52px">Jam Datang</th>
            <th style="width:52px">Durasi (mnt)</th>
            <th>Alasan</th>
            <th style="width:90px">Dicatat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td class="tc">{{ $i+1 }}</td>
            <td class="tc">{{ $r->date?->format('d/m/Y') }}</td>
            <td>{{ $r->student?->nis }}</td>
            <td>{{ $r->student?->name }}</td>
            <td class="tc">{{ $r->student?->class?->name ?? '-' }}</td>
            <td class="tc">
                @if($r->student?->location === 'selatan')<span class="b-loc-s">Selatan</span>
                @elseif($r->student?->location === 'utara')<span class="b-loc-u">Utara</span>
                @else -
                @endif
            </td>
            <td class="tc">{{ $r->arrive_time ?? '-' }}</td>
            <td class="tc">{{ $r->duration_minutes ?? '-' }}</td>
            <td>{{ $r->reason ?? '-' }}</td>
            <td>@php
                $names = [];
                if ($r->officer?->name) $names[] = $r->officer->name;
                elseif ($r->officer_name) $names[] = $r->officer_name;
                echo $names ? implode(', ', $names) : '-';
            @endphp</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Tidak ada data keterlambatan untuk filter yang dipilih.</div>
@endif

<div class="pdf-footer">
    <span class="left">BK Digital — Sistem Informasi Bimbingan Konseling</span>
    <span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span>
</div>
</body>
</html>
