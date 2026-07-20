<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Laporan Pelanggaran</title>
@include('reports.pdf._base', ['accentColor'=>'#7b1a1a','accentBorder'=>'#5a1010','accentBg'=>'#fdf5f5'])
</head>
<body>

<div class="pdf-header">
    <div class="school">{{ config('app.school_name','SEKOLAH MENENGAH ATAS') }}</div>
    <div class="title">LAPORAN PELANGGARAN SISWA</div>
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
    <span class="m-item">Total Poin: <strong>{{ $data->sum('points') }}</strong></span>
    @php
        $beratCount  = $data->filter(fn($r) => $r->violationCategory?->category === 'berat')->count();
        $sedangCount = $data->filter(fn($r) => $r->violationCategory?->category === 'sedang')->count();
        $ringanCount = $data->filter(fn($r) => $r->violationCategory?->category === 'ringan')->count();
    @endphp
    <span class="m-item">Berat: <strong>{{ $beratCount }}</strong></span>
    <span class="m-item">Sedang: <strong>{{ $sedangCount }}</strong></span>
    <span class="m-item">Ringan: <strong>{{ $ringanCount }}</strong></span>
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
            <th>Jenis Pelanggaran</th>
            <th style="width:52px">Kategori</th>
            <th style="width:32px">Poin</th>
            <th style="width:90px">Pelapor</th>
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
                @else - @endif
            </td>
            <td>{{ $r->violationCategory?->name ?? '-' }}</td>
            <td class="tc">
                @php $cat = $r->violationCategory?->category ?? '' @endphp
                @if($cat=='ringan')<span class="b-ringan">Ringan</span>
                @elseif($cat=='sedang')<span class="b-sedang">Sedang</span>
                @elseif($cat=='berat')<span class="b-berat">Berat</span>
                @else - @endif
            </td>
            <td class="tc"><strong>{{ $r->points }}</strong></td>
            <td>@php
                $names = [];
                if ($r->reporter?->name) $names[] = $r->reporter->name;
                elseif ($r->reporter_name) $names[] = $r->reporter_name;
                echo $names ? implode(', ', $names) : '-';
            @endphp</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Tidak ada data pelanggaran untuk filter yang dipilih.</div>
@endif

<div class="pdf-footer">
    <span class="left">BK Digital — Sistem Informasi Bimbingan Konseling</span>
    <span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span>
</div>
</body>
</html>
