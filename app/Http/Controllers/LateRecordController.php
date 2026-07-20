<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LateRecord;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LateRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = LateRecord::with(['student.class', 'officer'])->latest('date');

        if ($request->search) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->location) {
            $query->whereHas('student', fn($q) => $q->where('location', $request->location));
        }
        if ($request->date_from) $query->whereDate('date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('date', '<=', $request->date_to);

        $records = $query->paginate(20)->withQueryString();
        $classes = SchoolClass::orderBy('name')->get();

        // Stats untuk cards
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        $stats = [
            'today'       => LateRecord::whereDate('date', $today)->count(),
            'this_month'  => LateRecord::whereMonth('date', $month)->whereYear('date', $year)->count(),
            'this_week'   => LateRecord::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'avg_duration'=> round(LateRecord::whereMonth('date', $month)->whereYear('date', $year)->avg('duration_minutes') ?? 0),
        ];

        // Chart data: 7 hari terakhir
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $chartLabels[] = $d->translatedFormat('D, d M');
            $chartData[]   = LateRecord::whereDate('date', $d->format('Y-m-d'))->count();
        }

        // Top 5 paling sering terlambat bulan ini
        $topStudents = Student::with('class')
            ->withCount(['lateRecords as late_this_month' => fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year)])
            ->orderByDesc('late_this_month')
            ->take(5)
            ->get()
            ->filter(fn($s) => $s->late_this_month > 0)
            ->values();

        return view('late-records.index', compact('records', 'classes', 'stats', 'chartLabels', 'chartData', 'topStudents'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $officers = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_piket', 'guru_bk', 'admin']))->orderBy('name')->get();
        $selectedStudent = $request->student_id ? Student::find($request->student_id) : null;
        return view('late-records.create', compact('students', 'officers', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'date'             => 'required|date',
            'arrive_time'      => 'nullable|date_format:H:i',
            'entry_time'       => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'reason'           => 'nullable|string|max:255',
            'officer_id'       => 'nullable',
            'officer_name'     => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        // Handle "Lainnya (Ketik Manual)"
        if ($request->officer_id === 'other') {
            $validated['officer_id']   = null;
            $validated['officer_name'] = $request->officer_name;
        } elseif (!$request->officer_id) {
            $validated['officer_id']   = null;
            $validated['officer_name'] = null;
        }

        $record = LateRecord::create($validated);
        ActivityLog::log('created', 'late_record', $record->id, 'Input keterlambatan: '.$record->student->name);

        return redirect()->route('late-records.index')->with('success', 'Data keterlambatan berhasil disimpan.');
    }

    public function edit(LateRecord $lateRecord)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $officers = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_piket', 'guru_bk', 'admin']))->orderBy('name')->get();
        return view('late-records.edit', compact('lateRecord', 'students', 'officers'));
    }

    public function update(Request $request, LateRecord $lateRecord)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'date'             => 'required|date',
            'arrive_time'      => 'nullable|date_format:H:i',
            'entry_time'       => 'nullable|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:1',
            'reason'           => 'nullable|string|max:255',
            'officer_id'       => 'nullable',
            'officer_name'     => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        if ($request->officer_id === 'other') {
            $validated['officer_id']   = null;
            $validated['officer_name'] = $request->officer_name;
        } elseif (!$request->officer_id) {
            $validated['officer_id']   = null;
            $validated['officer_name'] = null;
        }

        $lateRecord->update($validated);
        ActivityLog::log('updated', 'late_record', $lateRecord->id, 'Update keterlambatan: '.$lateRecord->student->name);

        return redirect()->route('late-records.index')->with('success', 'Data keterlambatan berhasil diperbarui.');
    }

    public function destroy(LateRecord $lateRecord)
    {
        ActivityLog::log('deleted', 'late_record', $lateRecord->id, 'Hapus keterlambatan: '.$lateRecord->student->name);
        $lateRecord->delete();
        return redirect()->route('late-records.index')->with('success', 'Data berhasil dihapus.');
    }
}
