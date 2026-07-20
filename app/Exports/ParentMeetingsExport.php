<?php

namespace App\Exports;

use App\Models\ParentMeeting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParentMeetingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private ?int $classId,
        private ?int $studentId,
        private ?int $month,
        private ?int $year
    ) {}

    public function collection()
    {
        $query = ParentMeeting::with(['student.class', 'handler'])->latest('meeting_date');
        if ($this->studentId) $query->where('student_id', $this->studentId);
        if ($this->classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        if ($this->month)     $query->whereMonth('meeting_date', $this->month)->whereYear('meeting_date', $this->year);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Alasan', 'Orang Tua Hadir', 'Hasil Pertemuan', 'Kesepakatan', 'Tindak Lanjut', 'Penangani'];
    }

    public function map($row): array
    {
        static $no = 0; $no++;
        return [
            $no,
            $row->meeting_date?->format('d/m/Y'),
            $row->student?->nis,
            $row->student?->name,
            $row->student?->class?->name,
            $row->reason,
            $row->parent_attended ? 'Hadir' : 'Tidak Hadir',
            $row->meeting_result,
            $row->agreement,
            $row->follow_up,
            $row->handler?->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
