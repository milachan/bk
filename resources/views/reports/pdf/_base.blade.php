{{--
    Shared PDF base styles — include via @include('reports.pdf._base', ['accentColor' => '#...', 'accentBg' => '#...'])
--}}
<style>
@page { margin: 14mm 14mm 14mm 14mm; size: A4 landscape; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10.5px;
    color: #222;
    line-height: 1.4;
}

/* Header */
.pdf-header { text-align: center; border-bottom: 2.5px solid #222; padding-bottom: 8px; margin-bottom: 12px; }
.pdf-header .school { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; }
.pdf-header .title  { font-size: 12px; font-weight: bold; margin: 2px 0; }
.pdf-header .period { font-size: 10px; color: #555; }

/* Meta bar */
.pdf-meta {
    display: table; width: 100%; margin-bottom: 10px;
    background: #f4f4f4; border: 1px solid #e0e0e0;
    border-radius: 3px; padding: 5px 10px;
}
.pdf-meta .m-item { display: inline-block; margin-right: 18px; font-size: 10px; }
.pdf-meta .m-item strong { color: #222; }

/* Table */
table.dt { width: 100%; border-collapse: collapse; font-size: 10px; }
table.dt thead tr th {
    background: {{ $accentColor ?? '#2c3e50' }};
    color: #fff;
    padding: 5px 7px;
    text-align: left;
    border: 1px solid {{ $accentBorder ?? '#1a252f' }};
    white-space: nowrap;
}
table.dt tbody tr td {
    padding: 4px 7px;
    border: 1px solid #d5d5d5;
    vertical-align: top;
}
table.dt tbody tr:nth-child(even) td { background: {{ $accentBg ?? '#f8f9fa' }}; }
table.dt tbody tr:last-child td { border-bottom: 1px solid #bbb; }
.tc { text-align: center; }
.tr { text-align: right; }

/* Badges */
.b-ringan { background: #d1e7dd; color: #0f5132; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-sedang { background: #fff3cd; color: #664d03; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-berat  { background: #f8d7da; color: #842029; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-hadir  { background: #d1e7dd; color: #0f5132; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-tidak  { background: #f8d7da; color: #842029; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-loc-s  { background: #fff0f0; color: #c0392b; padding: 1px 5px; border-radius: 3px; font-size: 9px; }
.b-loc-u  { background: #e8f4fd; color: #1565c0; padding: 1px 5px; border-radius: 3px; font-size: 9px; }

/* Footer */
.pdf-footer {
    position: fixed; bottom: 8mm; left: 14mm; right: 14mm;
    font-size: 9px; color: #888;
    border-top: 1px solid #ccc; padding-top: 4px;
    display: table; width: 100%;
}
.pdf-footer .left  { display: table-cell; text-align: left; }
.pdf-footer .right { display: table-cell; text-align: right; }

/* No data */
.no-data { text-align: center; padding: 24px; color: #888; font-size: 11px; }

/* Page break */
tr { page-break-inside: avoid; }
thead { display: table-header-group; }
</style>
