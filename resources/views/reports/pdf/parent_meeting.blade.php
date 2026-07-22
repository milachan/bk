<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<title>Laporan Pemanggilan Orang Tua</title>
@include('reports.pdf._base', ['accentColor'=>'#154360','accentBorder'=>'#0e2d42','accentBg'=>'#eaf4fb'])
</head>
<body>

<div class="pdf-header">
    <div class="school">{{ config('app.school_name','SEKOLAH MENENGAH ATAS') }}</div>
    <div class="title">LAPORAN PEMANGGILAN ORANG TUA SISWA</div>
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
    @php
        $hadir    = $data->where('parent_attended', true)->count();
        $tidakHadir = $data->where('parent_attended', false)->count();
    @endphp
    <span class="m-item">Ortu Hadir: <strong>{{ $hadir }}</strong></span>
    <span class="m-item">Tidak Hadir: <strong>{{ $tidakHadir }}</strong></span>
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
            <th>Alasan Pemanggilan</th>
            <th style="width:60px">Ortu Hadir</th>
            <th style="width:90px">Penangani</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $i => $r)
        <tr>
            <td class="tc">{{ $i+1 }}</td>
            <td class="tc">{{ $r->meeting_date?->format('d/m/Y') }}</td>
            <td>{{ $r->student?->nis }}</td>
            <td>{{ $r->student?->name }}</td>
            <td class="tc">{{ $r->student?->class?->name ?? '-' }}</td>
            <td class="tc">
                @if($r->student?->location === 'selatan')<span class="b-loc-s">Selatan</span>
                @elseif($r->student?->location === 'utara')<span class="b-loc-u">Utara</span>
                @else - @endif
            </td>
            <td>{{ $r->reason }}</td>
            <td class="tc">
                @if($r->parent_attended)
                    <span class="b-hadir">Hadir</span>
                @else
                    <span class="b-tidak">Tidak</span>
                @endif
            </td>
            <td>@php
                $names = [];
                if ($r->handler?->name) $names[] = $r->handler->name;
                elseif ($r->handler_name) $names[] = $r->handler_name;
                if (!empty($r->extra_handlers)) foreach ($r->extra_handlers as $e) $names[] = $e;
                echo $names ? implode(', ', $names) : '-';
            @endphp</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="no-data">Tidak ada data pemanggilan orang tua untuk filter yang dipilih.</div>
@endif

<div class="pdf-footer">
    <span class="left">BK Digital — Sistem Informasi Bimbingan Konseling</span>
    <span class="right">Dicetak {{ now()->format('d/m/Y H:i') }}</span>
</div>
</body>
</html>
