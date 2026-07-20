<?php

namespace App\Http\Controllers;

use App\Models\Counseling;
use App\Models\HomeVisit;
use App\Models\LateRecord;
use App\Models\ParentMeeting;
use App\Models\Student;
use App\Models\ViolationRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        // --- Filter lokasi ---
        $filterLocation = $request->filter_location; // 'selatan', 'utara', atau null

        // --- Filter waktu ---
        $filterMode  = $request->filter_mode; // 'day', 'month', atau null
        $filterDate  = null;
        $filterMonth = null;
        $filterYear  = null;

        if ($filterMode === 'day' && $request->filter_date) {
            $filterDate = Carbon::parse($request->filter_date);
        } elseif ($filterMode === 'month' && $request->filter_month) {
            [$filterYear, $filterMonth] = explode('-', $request->filter_month);
            $filterYear  = (int) $filterYear;
            $filterMonth = (int) $filterMonth;
        }

        // Label filter untuk ditampilkan di UI
        $filterLabel = null;
        if ($filterMode === 'day' && $filterDate) {
            $filterLabel = $filterDate->translatedFormat('d F Y');
        } elseif ($filterMode === 'month' && $filterMonth) {
            $filterLabel = Carbon::create($filterYear, $filterMonth)->translatedFormat('F Y');
        }

        $locationLabel = match($filterLocation) {
            'selatan' => '🔴 Selatan (Jl. Cendrawasih)',
            'utara'   => '🔵 Utara (Jl. Sarbini)',
            default   => null,
        };

        // --- Helper closure untuk scope lokasi pada siswa ---
        $locScope = function ($q) use ($filterLocation) {
            if ($filterLocation) {
                $q->where('location', $filterLocation);
            }
        };

        // --- Stats cards ---
        $totalStudentsQ = Student::where('status', 'aktif');
        if ($filterLocation) $totalStudentsQ->where('location', $filterLocation);

        if ($filterMode === 'day' && $filterDate) {
            $stats = [
                'total_students'         => $totalStudentsQ->count(),
                'late_today'             => LateRecord::whereDate('date', $filterDate)->whereHas('student', $locScope)->count(),
                'late_this_month'        => LateRecord::whereDate('date', $filterDate)->whereHas('student', $locScope)->count(),
                'violations_this_month'  => ViolationRecord::whereDate('date', $filterDate)->whereHas('student', $locScope)->count(),
                'counselings_this_month' => Counseling::whereDate('date', $filterDate)->whereHas('student', $locScope)->count(),
                'parent_meetings'        => ParentMeeting::whereDate('meeting_date', $filterDate)->whereHas('student', $locScope)->count(),
                'home_visits'            => HomeVisit::whereDate('visit_date', $filterDate)->whereHas('student', $locScope)->count(),
            ];
        } elseif ($filterMode === 'month' && $filterMonth) {
            $stats = [
                'total_students'         => $totalStudentsQ->count(),
                'late_today'             => LateRecord::whereDate('date', $today)->whereHas('student', $locScope)->count(),
                'late_this_month'        => LateRecord::whereMonth('date', $filterMonth)->whereYear('date', $filterYear)->whereHas('student', $locScope)->count(),
                'violations_this_month'  => ViolationRecord::whereMonth('date', $filterMonth)->whereYear('date', $filterYear)->whereHas('student', $locScope)->count(),
                'counselings_this_month' => Counseling::whereMonth('date', $filterMonth)->whereYear('date', $filterYear)->whereHas('student', $locScope)->count(),
                'parent_meetings'        => ParentMeeting::whereMonth('meeting_date', $filterMonth)->whereYear('meeting_date', $filterYear)->whereHas('student', $locScope)->count(),
                'home_visits'            => HomeVisit::whereMonth('visit_date', $filterMonth)->whereYear('visit_date', $filterYear)->whereHas('student', $locScope)->count(),
            ];
        } else {
            $stats = [
                'total_students'         => $totalStudentsQ->count(),
                'late_today'             => LateRecord::whereDate('date', $today)->whereHas('student', $locScope)->count(),
                'late_this_month'        => LateRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('student', $locScope)->count(),
                'violations_this_month'  => ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('student', $locScope)->count(),
                'counselings_this_month' => Counseling::whereMonth('date', $month)->whereYear('date', $year)->whereHas('student', $locScope)->count(),
                'parent_meetings'        => ParentMeeting::whereMonth('meeting_date', $month)->whereYear('meeting_date', $year)->whereHas('student', $locScope)->count(),
                'home_visits'            => HomeVisit::whereMonth('visit_date', $month)->whereYear('visit_date', $year)->whereHas('student', $locScope)->count(),
            ];
        }

        // --- Data terlambat (widget bawah) ---
        $lateTodayQ = LateRecord::with(['student.class'])->whereHas('student', $locScope);
        if ($filterMode === 'day' && $filterDate) {
            $lateToday = $lateTodayQ->whereDate('date', $filterDate)->latest()->take(10)->get();
        } else {
            $lateToday = $lateTodayQ->whereDate('date', $today)->latest()->take(5)->get();
        }

        // --- Konseling hari ini ---
        $counselingsTodayQ = Counseling::with(['student.class', 'counselor'])->whereHas('student', $locScope);
        if ($filterMode === 'day' && $filterDate) {
            $counselingsToday = $counselingsTodayQ->whereDate('date', $filterDate)->get();
        } else {
            $counselingsToday = $counselingsTodayQ->whereDate('date', $today)->get();
        }

        // --- Top violators ---
        $topViolatorsQ = Student::with('class');
        if ($filterLocation) $topViolatorsQ->where('location', $filterLocation);

        if ($filterMode === 'day' && $filterDate) {
            $topViolators = $topViolatorsQ
                ->withSum(['violationRecords as violation_records_sum_points' => fn($q) => $q->whereDate('date', $filterDate)], 'points')
                ->orderByDesc('violation_records_sum_points')->take(10)->get()
                ->filter(fn($s) => ($s->violation_records_sum_points ?? 0) > 0)->take(5)->values();
        } elseif ($filterMode === 'month' && $filterMonth) {
            $topViolators = $topViolatorsQ
                ->withSum(['violationRecords as violation_records_sum_points' => fn($q) => $q->whereMonth('date', $filterMonth)->whereYear('date', $filterYear)], 'points')
                ->orderByDesc('violation_records_sum_points')->take(10)->get()
                ->filter(fn($s) => ($s->violation_records_sum_points ?? 0) > 0)->take(5)->values();
        } else {
            $topViolators = $topViolatorsQ
                ->withSum('violationRecords', 'points')
                ->orderByDesc('violation_records_sum_points')->take(10)->get()
                ->filter(fn($s) => ($s->violation_records_sum_points ?? 0) > 0)->take(5)->values();
        }

        // --- Most late ---
        $mostLateQ = Student::with('class');
        if ($filterLocation) $mostLateQ->where('location', $filterLocation);

        if ($filterMode === 'day' && $filterDate) {
            $mostLate = $mostLateQ
                ->withCount(['lateRecords as late_records_count' => fn($q) => $q->whereDate('date', $filterDate)])
                ->orderByDesc('late_records_count')->take(10)->get()
                ->filter(fn($s) => ($s->late_records_count ?? 0) > 0)->take(5)->values();
        } elseif ($filterMode === 'month' && $filterMonth) {
            $mostLate = $mostLateQ
                ->withCount(['lateRecords as late_records_count' => fn($q) => $q->whereMonth('date', $filterMonth)->whereYear('date', $filterYear)])
                ->orderByDesc('late_records_count')->take(10)->get()
                ->filter(fn($s) => ($s->late_records_count ?? 0) > 0)->take(5)->values();
        } else {
            $mostLate = $mostLateQ
                ->withCount('lateRecords')
                ->orderByDesc('late_records_count')->take(10)->get()
                ->filter(fn($s) => ($s->late_records_count ?? 0) > 0)->take(5)->values();
        }

        // --- Chart tren 6 bulan (selalu per bulan, tapi dikondisikan lokasi) ---
        $lateMonthly      = [];
        $violationMonthly = [];
        $counselingMonthly = [];
        $months           = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $lateQ = LateRecord::whereMonth('date', $date->month)->whereYear('date', $date->year);
            $vioQ  = ViolationRecord::whereMonth('date', $date->month)->whereYear('date', $date->year);
            $couQ  = Counseling::whereMonth('date', $date->month)->whereYear('date', $date->year);

            if ($filterLocation) {
                $lateQ->whereHas('student', $locScope);
                $vioQ->whereHas('student', $locScope);
                $couQ->whereHas('student', $locScope);
            }

            $lateMonthly[]       = $lateQ->count();
            $violationMonthly[]  = $vioQ->count();
            $counselingMonthly[] = $couQ->count();
        }

        // --- Violations per kelas ---
        $violationByClassQ = DB::table('violation_records')
            ->join('students', 'violation_records.student_id', '=', 'students.id')
            ->join('classes',  'students.class_id', '=', 'classes.id')
            ->select('classes.name as class_name', DB::raw('count(*) as total'));

        if ($filterLocation) {
            $violationByClassQ->where('students.location', $filterLocation);
        }

        if ($filterMode === 'day' && $filterDate) {
            $violationByClassQ->whereDate('violation_records.date', $filterDate);
        } elseif ($filterMode === 'month' && $filterMonth) {
            $violationByClassQ->whereMonth('violation_records.date', $filterMonth)
                              ->whereYear('violation_records.date', $filterYear);
        }

        $violationByClass = $violationByClassQ->groupBy('classes.name')->orderByDesc('total')->take(6)->get();

        return view('dashboard', compact(
            'stats', 'lateToday', 'counselingsToday', 'topViolators', 'mostLate',
            'months', 'lateMonthly', 'violationMonthly', 'counselingMonthly', 'violationByClass',
            'filterMode', 'filterDate', 'filterMonth', 'filterYear', 'filterLabel',
            'filterLocation', 'locationLabel'
        ));
    }
}
