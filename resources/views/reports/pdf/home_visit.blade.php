<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Laporan Home Visit</title>
@include('reports.pdf._base', ['accentColor'=>'#4a235a','accentBorder'=>'#321640','accentBg'=>'#f9f0ff'])
</head>
<body>

<div class="pdf-header">
    <div class="school">{{ config('app.school_name','SEKOLAH MENENGAH ATAS') }}</div>
    <div class="title">LAPORAN HOME VISIT</div>
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
            <th style="width:120px">Alamat</th>
            <th>Tujuan Kunjungan</th>
            <th style="width:110px">Hasil / Kesimpulan</th>
            <th style="width:90px">Petugas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td class="tc">{{ $i+1 }}</td>
            <td class="tc">{{ $r->visit_date?->format('d/m/Y') }}</td>
            <td>{{ $r->student?->nis }}</td>
            <td>{{ $r->student?->name }}</td>
            <td class="tc">{{ $r->student?->class?->name ?? '-' }}</td>
            <td class="tc">
                @if($r->student?->location === 'selatan')<span class="b-loc-s">Selatan</span>
                @elseif($r->student?->location === 'utara')<span class="b-loc-u">Utara</span>
                @else - @endif
            </td>
            <td>{{ $r->address }}</td>
            <td>{{ $r->purpose }}</td>
            <td>{{ $r->conclusion ?? $r->result ?? '-' }}</td>
            <td>@php
                $names = [];
                if ($r->visitor?->name) $names[] = $r->visitor->name;
                elseif ($r->visitor_name) $names[] = $r->visitor_name;
                if (!empty($r->extra_visitors)) foreach ($r->extra_visitors as $e) $names[] = $e;
                echo $names ? implode(', ', $names) : '-';
            @endphp</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Tidak ada data home visit untuk filter yang dipilih.</div>
@endif

<div class="pdf-footer">
    <span class="left">BK Digital — Sistem Informasi Bimbingan Konseling</span>
    <span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span>
</div>
</body>
</html>
