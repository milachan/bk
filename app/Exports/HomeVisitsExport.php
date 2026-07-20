<?php

namespace App\Exports;

use App\Models\HomeVisit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HomeVisitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classId,
        private ?int $studentId,
        private ?int $month,
        private ?int $year
    ) {}

    public function collection()
    {
        $query = HomeVisit::with(['student.class', 'visitor'])->latest('visit_date');
        if ($this->studentId) $query->where('student_id', $this->studentId);
        if ($this->classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        if ($this->month)     $query->whereMonth('visit_date', $this->month)->whereYear('visit_date', $this->year);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Alamat', 'Tujuan', 'Hasil', 'Kesimpulan', 'Tindak Lanjut', 'Petugas'];
    }

    public function map($row): array
    {
        static $no = 0; $no++;
        return [
            $no,
            $row->visit_date?->format('d/m/Y'),
            $row->student?->nis,
            $row->student?->name,
            $row->student?->class?->name,
            $row->address,
            $row->purpose,
            $row->result,
            $row->conclusion,
            $row->follow_up,
            $row->visitor?->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
