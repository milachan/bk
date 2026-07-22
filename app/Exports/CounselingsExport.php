<?php

namespace App\Exports;

use App\Models\Counseling;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CounselingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classId,
        private ?int $studentId,
        private ?int $month,
        private ?int $year
    ) {}

    public function collection()
    {
        $query = Counseling::with(['student.class', 'counselor'])->latest('date');
        if ($this->studentId) $query->where('student_id', $this->studentId);
        if ($this->classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        if ($this->month)     $query->whereMonth('date', $this->month)->whereYear('date', $this->year);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Masalah', 'Hasil', 'Solusi', 'Guru BK'];
    }

    public function map($row): array
    {
        static $no = 0; $no++;
        return [
            $no,
            $row->date?->format('d/m/Y'),
            $row->student?->nis,
            $row->student?->name,
            $row->student?->class?->name,
            $row->problem,
            $row->result,
            $row->solution,
            $row->counselor?->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
