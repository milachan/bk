<?php

namespace App\Exports;

use App\Models\ViolationRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ViolationRecordsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classId,
        private ?int $studentId,
        private ?int $month,
        private ?int $year
    ) {}

    public function collection()
    {
        $query = ViolationRecord::with(['student.class', 'violationCategory', 'reporter'])->latest('date');
        if ($this->classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        if ($this->studentId) $query->where('student_id', $this->studentId);
        if ($this->month)     $query->whereMonth('date', $this->month)->whereYear('date', $this->year);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Jenis Pelanggaran', 'Kategori', 'Poin', 'Kronologi', 'Pelapor', 'Catatan'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->date?->format('d/m/Y'),
            $row->student?->nis,
            $row->student?->name,
            $row->student?->class?->name,
            $row->violationCategory?->name,
            $row->violationCategory?->category,
            $row->points,
            $row->description,
            $row->reporter?->name,
            $row->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
