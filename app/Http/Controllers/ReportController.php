<?php

namespace App\Http\Controllers;

use App\Models\Counseling;
use App\Models\HomeVisit;
use App\Models\LateRecord;
use App\Models\ParentMeeting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\ViolationRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CounselingsExport;
use App\Exports\HomeVisitsExport;
use App\Exports\LateRecordsExport;
use App\Exports\ParentMeetingsExport;
use App\Exports\StudentReportExport;
use App\Exports\ViolationRecordsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes  = SchoolClass::orderBy('name')->get();
        $students = Student::orderBy('name')->get();

        $type      = $request->type ?? 'late';
        $classId   = $request->class_id;
        $studentId = $request->student_id;
        $month     = $request->month;
        $year      = $request->year ?? now()->year;
        $location  = $request->location;

        $data = $this->getReportData($type, $classId, $studentId, $month, $year, $location);

        // --- Chart: tren 6 bulan terakhir untuk tipe yang dipilih ---
        $chartLabels = [];
        $chartValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = \Carbon\Carbon::now()->subMonths($i);
            $chartLabels[] = $d->translatedFormat('M Y');
            $chartValues[] = $this->getReportData($type, $classId, $studentId, $d->month, $d->year, $location)->count();
        }

        // --- Stats ringkas ---
        $stats = [
            'total'      => $data->count(),
            'this_month' => $this->getReportData($type, $classId, $studentId, now()->month, now()->year, $location)->count(),
            'this_year'  => $this->getReportData($type, $classId, null, null, now()->year, $location)->count(),
        ];

        return view('reports.index', compact(
            'classes', 'students', 'type', 'classId', 'studentId', 'month', 'year', 'location', 'data',
            'chartLabels', 'chartValues', 'stats'
        ));
    }

    public function printPdf(Request $request)
    {
        $type      = $request->type ?? 'late';
        $classId   = $request->class_id;
        $studentId = $request->student_id;
        $month     = $request->month;
        $year      = $request->year ?? now()->year;
        $location  = $request->location;
        $classes   = SchoolClass::orderBy('name')->get();
        $class     = $classId ? $classes->find($classId) : null;

        $data = $this->getReportData($type, $classId, $studentId, $month, $year, $location);

        $viewName = 'reports.pdf.'.str_replace('-', '_', $type);
        $pdf = Pdf::loadView($viewName, compact('data', 'month', 'year', 'classes', 'classId', 'class', 'location'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-bk-'.$type.'-'.now()->format('Ymd').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type      = $request->type ?? 'late';
        $classId   = $request->class_id;
        $studentId = $request->student_id;
        $month     = $request->month;
        $year      = $request->year ?? now()->year;

        $filename = 'laporan-bk-'.$type.'-'.now()->format('Ymd').'.xlsx';

        return match ($type) {
            'late'           => Excel::download(new LateRecordsExport($classId, $studentId, $month, $year), $filename),
            'violation'      => Excel::download(new ViolationRecordsExport($classId, $studentId, $month, $year), $filename),
            'counseling'     => Excel::download(new CounselingsExport($classId, $studentId, $month, $year), $filename),
            'parent_meeting' => Excel::download(new ParentMeetingsExport($classId, $studentId, $month, $year), $filename),
            'home_visit'     => Excel::download(new HomeVisitsExport($classId, $studentId, $month, $year), $filename),
            default          => Excel::download(new StudentReportExport($classId, $month, $year), $filename),
        };
    }

    private function getReportData($type, $classId, $studentId, $month, $year, $location = null)
    {
        if ($type === 'late') {
            $query = LateRecord::with(['student.class', 'officer'])->latest('date');
            if ($classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
            if ($studentId) $query->where('student_id', $studentId);
            if ($month)     $query->whereMonth('date', $month)->whereYear('date', $year);
            if ($location)  $query->whereHas('student', fn($q) => $q->where('location', $location));
            return $query->get();
        }
        if ($type === 'violation') {
            $query = ViolationRecord::with(['student.class', 'violationCategory', 'reporter'])->latest('date');
            if ($classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
            if ($studentId) $query->where('student_id', $studentId);
            if ($month)     $query->whereMonth('date', $month)->whereYear('date', $year);
            if ($location)  $query->whereHas('student', fn($q) => $q->where('location', $location));
            return $query->get();
        }
        if ($type === 'counseling') {
            $query = Counseling::with(['student.class', 'counselor'])->latest('date');
            if ($classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
            if ($studentId) $query->where('student_id', $studentId);
            if ($month)     $query->whereMonth('date', $month)->whereYear('date', $year);
            if ($location)  $query->whereHas('student', fn($q) => $q->where('location', $location));
            return $query->get();
        }
        if ($type === 'parent_meeting') {
            $query = ParentMeeting::with(['student.class', 'handler'])->latest('meeting_date');
            if ($classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
            if ($studentId) $query->where('student_id', $studentId);
            if ($month)     $query->whereMonth('meeting_date', $month)->whereYear('meeting_date', $year);
            if ($location)  $query->whereHas('student', fn($q) => $q->where('location', $location));
            return $query->get();
        }
        if ($type === 'home_visit') {
            $query = HomeVisit::with(['student.class', 'visitor'])->latest('visit_date');
            if ($classId)   $query->whereHas('student', fn($q) => $q->where('class_id', $classId));
            if ($studentId) $query->where('student_id', $studentId);
            if ($month)     $query->whereMonth('visit_date', $month)->whereYear('visit_date', $year);
            if ($location)  $query->whereHas('student', fn($q) => $q->where('location', $location));
            return $query->get();
        }
        if ($type === 'student') {
            $query = Student::with(['class'])
                ->withCount('lateRecords')
                ->withCount('counselings')
                ->withSum('violationRecords', 'points')
                ->orderBy('name');
            if ($classId)   $query->where('class_id', $classId);
            if ($studentId) $query->where('id', $studentId);
            if ($location)  $query->where('location', $location);
            return $query->get();
        }
        return collect();
    }
}
