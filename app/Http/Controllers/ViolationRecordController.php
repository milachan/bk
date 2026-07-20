<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationCategory;
use App\Models\ViolationRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ViolationRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = ViolationRecord::with(['student.class', 'violationCategory', 'reporter'])->latest('date');

        if ($request->search) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->class_id) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->category) {
            $query->whereHas('violationCategory', fn($q) => $q->where('category', $request->category));
        }
        if ($request->location) {
            $query->whereHas('student', fn($q) => $q->where('location', $request->location));
        }
        if ($request->date_from) $query->whereDate('date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('date', '<=', $request->date_to);

        $records    = $query->paginate(20)->withQueryString();
        $classes    = SchoolClass::orderBy('name')->get();
        $categories = ViolationCategory::orderBy('name')->get();

        // Stats
        $month = now()->month;
        $year  = now()->year;
        $stats = [
            'this_month'  => ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->count(),
            'points_month'=> ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->sum('points'),
            'berat'       => ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('violationCategory', fn($q) => $q->where('category', 'berat'))->count(),
            'total'       => ViolationRecord::count(),
        ];

        // Chart: kategori bulan ini
        $chartCats   = ['Ringan', 'Sedang', 'Berat'];
        $chartCatData = [
            ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('violationCategory', fn($q) => $q->where('category', 'ringan'))->count(),
            ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('violationCategory', fn($q) => $q->where('category', 'sedang'))->count(),
            ViolationRecord::whereMonth('date', $month)->whereYear('date', $year)->whereHas('violationCategory', fn($q) => $q->where('category', 'berat'))->count(),
        ];

        // Top siswa poin terbanyak bulan ini
        $topStudents = Student::with('class')
            ->withSum(['violationRecords as points_this_month' => fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year)], 'points')
            ->orderByDesc('points_this_month')
            ->take(5)
            ->get()
            ->filter(fn($s) => ($s->points_this_month ?? 0) > 0)
            ->values();

        return view('violation-records.index', compact('records', 'classes', 'categories', 'stats', 'chartCats', 'chartCatData', 'topStudents'));
    }

    public function create(Request $request)
    {
        $students             = Student::where('status', 'aktif')->orderBy('name')->get();
        $violationCategories  = ViolationCategory::orderBy('name')->get();
        $reporters            = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_piket', 'guru_bk', 'admin']))->orderBy('name')->get();
        $selectedStudent      = $request->student_id ? Student::find($request->student_id) : null;
        return view('violation-records.create', compact('students', 'violationCategories', 'reporters', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'            => 'required|exists:students,id',
            'violation_category_id' => 'required',
            'date'                  => 'required|date',
            'points'                => 'required|integer|min:0',
            'description'           => 'nullable|string',
            'reporter_id'           => 'nullable|exists:users,id',
            'notes'                 => 'nullable|string',
            'evidence_photo'        => 'nullable|image|max:5120',
        ]);

        // Handle "Pelanggaran Lainnya"
        if ($request->violation_category_id === 'other') {
            $request->validate([
                'other_violation_name'     => 'required|string|max:255',
                'other_violation_category' => 'required|in:ringan,sedang,berat',
                'other_violation_points'   => 'required|integer|min:0|max:100',
            ]);

            $newCategory = ViolationCategory::create([
                'name'        => $request->other_violation_name,
                'category'    => $request->other_violation_category,
                'points'      => $request->other_violation_points,
                'description' => $request->other_violation_description,
            ]);

            $validated['violation_category_id'] = $newCategory->id;
            $validated['points'] = $request->other_violation_points;
        }

        if ($request->hasFile('evidence_photo')) {
            $validated['evidence_photo'] = $request->file('evidence_photo')->store('violations', 'public');
        }

        $record = ViolationRecord::create($validated);
        ActivityLog::log('created', 'violation_record', $record->id, 'Input pelanggaran: '.$record->student->name);

        return redirect()->route('violation-records.index')->with('success', 'Data pelanggaran berhasil disimpan.');
    }

    public function edit(ViolationRecord $violationRecord)
    {
        $students             = Student::where('status', 'aktif')->orderBy('name')->get();
        $violationCategories  = ViolationCategory::orderBy('name')->get();
        $reporters            = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_piket', 'guru_bk', 'admin']))->orderBy('name')->get();
        return view('violation-records.edit', compact('violationRecord', 'students', 'violationCategories', 'reporters'));
    }

    public function update(Request $request, ViolationRecord $violationRecord)
    {
        $validated = $request->validate([
            'student_id'            => 'required|exists:students,id',
            'violation_category_id' => 'required',
            'date'                  => 'required|date',
            'points'                => 'required|integer|min:0',
            'description'           => 'nullable|string',
            'reporter_id'           => 'nullable|exists:users,id',
            'notes'                 => 'nullable|string',
            'evidence_photo'        => 'nullable|image|max:5120',
        ]);

        // Handle "Pelanggaran Lainnya"
        if ($request->violation_category_id === 'other') {
            $request->validate([
                'other_violation_name'     => 'required|string|max:255',
                'other_violation_category' => 'required|in:ringan,sedang,berat',
                'other_violation_points'   => 'required|integer|min:0|max:100',
            ]);

            $newCategory = ViolationCategory::create([
                'name'        => $request->other_violation_name,
                'category'    => $request->other_violation_category,
                'points'      => $request->other_violation_points,
                'description' => $request->other_violation_description,
            ]);

            $validated['violation_category_id'] = $newCategory->id;
            $validated['points'] = $request->other_violation_points;
        }

        if ($request->hasFile('evidence_photo')) {
            if ($violationRecord->evidence_photo) Storage::disk('public')->delete($violationRecord->evidence_photo);
            $validated['evidence_photo'] = $request->file('evidence_photo')->store('violations', 'public');
        }

        $violationRecord->update($validated);
        ActivityLog::log('updated', 'violation_record', $violationRecord->id, 'Update pelanggaran: '.$violationRecord->student->name);

        return redirect()->route('violation-records.index')->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy(ViolationRecord $violationRecord)
    {
        if ($violationRecord->evidence_photo) Storage::disk('public')->delete($violationRecord->evidence_photo);
        ActivityLog::log('deleted', 'violation_record', $violationRecord->id, 'Hapus pelanggaran: '.$violationRecord->student->name);
        $violationRecord->delete();
        return redirect()->route('violation-records.index')->with('success', 'Data berhasil dihapus.');
    }
}
