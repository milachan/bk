<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Counseling;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class CounselingController extends Controller
{
    public function index(Request $request)
    {
        $query = Counseling::with(['student.class', 'counselor'])->latest('date');

        if ($request->search) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->location) {
            $query->whereHas('student', fn($q) => $q->where('location', $request->location));
        }
        if ($request->date_from) $query->whereDate('date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('date', '<=', $request->date_to);

        $records    = $query->paginate(20)->withQueryString();
        $counselors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();

        $month = now()->month;
        $year  = now()->year;
        $stats = [
            'this_month' => Counseling::whereMonth('date', $month)->whereYear('date', $year)->count(),
            'this_week'  => Counseling::whereBetween('date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])->count(),
            'today'      => Counseling::whereDate('date', today())->count(),
            'total'      => Counseling::count(),
        ];

        // Recent 5 konseling unik siswa bulan ini
        $recentStudents = Counseling::with('student.class')
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->orderByDesc('date')
            ->take(5)
            ->get();

        return view('counselings.index', compact('records', 'counselors', 'stats', 'recentStudents'));
    }

    public function create(Request $request)
    {
        $students  = Student::where('status', 'aktif')->orderBy('name')->get();
        $counselors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        $selectedStudent = $request->student_id ? Student::find($request->student_id) : null;
        return view('counselings.create', compact('students', 'counselors', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'date'          => 'required|date',
            'problem'       => 'required|string',
            'result'        => 'nullable|string',
            'solution'      => 'nullable|string',
            'follow_up'     => 'nullable|string',
            'counselor_id'  => 'nullable',
            'counselor_name'=> 'nullable|string|max:255',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'counselor_id', 'counselor_name', 'extra_counselors');

        $record = Counseling::create($validated);
        ActivityLog::log('created', 'counseling', $record->id, 'Input konseling: '.$record->student->name);

        return redirect()->route('counselings.index')->with('success', 'Data konseling berhasil disimpan.');
    }

    public function edit(Counseling $counseling)
    {
        $students   = Student::where('status', 'aktif')->orderBy('name')->get();
        $counselors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        return view('counselings.edit', compact('counseling', 'students', 'counselors'));
    }

    public function update(Request $request, Counseling $counseling)
    {
        $validated = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'date'          => 'required|date',
            'problem'       => 'required|string',
            'result'        => 'nullable|string',
            'solution'      => 'nullable|string',
            'follow_up'     => 'nullable|string',
            'counselor_id'  => 'nullable',
            'counselor_name'=> 'nullable|string|max:255',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'counselor_id', 'counselor_name', 'extra_counselors');

        $counseling->update($validated);
        ActivityLog::log('updated', 'counseling', $counseling->id, 'Update konseling: '.$counseling->student->name);

        return redirect()->route('counselings.index')->with('success', 'Data konseling berhasil diperbarui.');
    }

    public function destroy(Counseling $counseling)
    {
        ActivityLog::log('deleted', 'counseling', $counseling->id, 'Hapus konseling: '.$counseling->student->name);
        $counseling->delete();
        return redirect()->route('counselings.index')->with('success', 'Data berhasil dihapus.');
    }

    private function resolveMultiStaff($request, array $validated, string $idField, string $nameField, string $extraField): array
    {
        $selected = (array) $request->input($idField, []);
        $primary  = null;
        $extras   = [];

        foreach ($selected as $val) {
            if ($val === 'other') {
                $manual = trim($request->input($nameField, ''));
                if ($manual) $extras[] = $manual;
            } elseif ($primary === null) {
                $primary = (int) $val;
            } else {
                $u = \App\Models\User::find((int) $val);
                if ($u) $extras[] = $u->name;
            }
        }

        $validated[$idField]   = $primary;
        $validated[$nameField] = null;
        $validated[$extraField] = !empty($extras) ? $extras : null;
        return $validated;
    }
}
