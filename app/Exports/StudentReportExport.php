<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classId,
        private ?int $month,
        private ?int $year
    ) {}

    public function collection()
    {
        $query = Student::with(['class'])->withCount(['lateRecords', 'counselings', 'parentMeetings', 'homeVisits'])->withSum('violationRecords', 'points')->orderBy('name');
        if ($this->classId) $query->where('class_id', $this->classId);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Status', 'Keterlambatan', 'Total Poin Pelanggaran', 'Konseling', 'Pemanggilan Ortu', 'Home Visit'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nis,
            $row->name,
            $row->class?->name,
            $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            ucfirst($row->status),
            $row->late_records_count,
            $row->violation_records_sum_points ?? 0,
            $row->counselings_count,
            $row->parent_meetings_count,
            $row->home_visits_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
