<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Data Siswa';
    }

    public function array(): array
    {
        // Contoh data dummy untuk panduan
        return [
            ['2024001', '1234567801', 'Ahmad Fauzi',  'L', 'Jakarta', '2008-03-15', 'Islam', 'Jl. Merdeka No.1 Jakarta',    '08111111111', 'Bapak Fauzi',  '08122222222', 'X IPA 1', 'selatan'],
            ['2024002', '1234567802', 'Siti Rahayu',  'P', 'Bandung',  '2008-07-22', 'Islam', 'Jl. Sudirman No.5 Bandung', '08211111111', 'Ibu Rahayu', '08222222222',   'X IPA 2', 'utara'],
        ];
    }

    public function headings(): array
    {
        return [
            'nis',
            'nisn',
            'nama',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat',
            'nomor_hp',
            'nama_orang_tua',
            'nomor_hp_orang_tua',
            'kelas',
            'lokasi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Highlight kolom lokasi
        $sheet->getStyle('M1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
        ]);

        // Border
        $sheet->getStyle('A1:M3')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // Style data rows
        $sheet->getStyle('A2:M3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // nis
            'B' => 14,  // nisn
            'C' => 25,  // nama
            'D' => 15,  // jenis_kelamin
            'E' => 15,  // tempat_lahir
            'F' => 15,  // tanggal_lahir
            'G' => 12,  // agama
            'H' => 35,  // alamat
            'I' => 15,  // nomor_hp
            'J' => 25,  // nama_orang_tua
            'K' => 18,  // nomor_hp_orang_tua
            'L' => 15,  // kelas
            'M' => 14,  // lokasi
        ];
    }
}
