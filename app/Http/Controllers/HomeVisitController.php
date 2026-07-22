<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomeVisit;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeVisitController extends Controller
{
    public function index(Request $request)
    {
        $query = HomeVisit::with(['student.class', 'visitor'])->latest('visit_date');

        if ($request->search) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->location) {
            $query->whereHas('student', fn($q) => $q->where('location', $request->location));
        }
        if ($request->date_from) $query->whereDate('visit_date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('visit_date', '<=', $request->date_to);

        $records  = $query->paginate(20)->withQueryString();
        $visitors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();

        $month = now()->month;
        $year  = now()->year;
        $stats = [
            'this_month' => HomeVisit::whereMonth('visit_date', $month)->whereYear('visit_date', $year)->count(),
            'this_week'  => HomeVisit::whereBetween('visit_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])->count(),
            'total'      => HomeVisit::count(),
            'this_year'  => HomeVisit::whereYear('visit_date', $year)->count(),
        ];

        // Chart 7 hari terakhir
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = \Carbon\Carbon::now()->subDays($i);
            $chartLabels[] = $day->translatedFormat('D d/m');
            $chartData[]   = HomeVisit::whereDate('visit_date', $day->toDateString())->count();
        }

        // Top siswa home visit bulan ini
        $topStudents = Student::with('class')
            ->withCount(['homeVisits as hv_this_month' => fn($q) =>
                $q->whereMonth('visit_date', $month)->whereYear('visit_date', $year)
            ])
            ->orderByDesc('hv_this_month')
            ->limit(10)
            ->get()
            ->filter(fn($s) => $s->hv_this_month > 0)
            ->take(5)
            ->values();

        return view('home-visits.index', compact('records', 'visitors', 'stats', 'chartLabels', 'chartData', 'topStudents'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $visitors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        $selectedStudent = $request->student_id ? Student::find($request->student_id) : null;
        return view('home-visits.create', compact('students', 'visitors', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'visit_date'   => 'required|date',
            'address'      => 'required|string',
            'purpose'      => 'required|string',
            'result'       => 'nullable|string',
            'conclusion'   => 'nullable|string',
            'follow_up'    => 'nullable|string',
            'visitor_id'   => 'nullable',
            'visitor_name' => 'nullable|string|max:255',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'visitor_id', 'visitor_name', 'extra_visitors');

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('home-visits', 'public');
        }

        $record = HomeVisit::create($validated);
        ActivityLog::log('created', 'home_visit', $record->id, 'Input home visit: '.$record->student->name);

        return redirect()->route('home-visits.index')->with('success', 'Data home visit berhasil disimpan.');
    }

    public function edit(HomeVisit $homeVisit)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $visitors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        return view('home-visits.edit', compact('homeVisit', 'students', 'visitors'));
    }

    public function update(Request $request, HomeVisit $homeVisit)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'visit_date'   => 'required|date',
            'address'      => 'required|string',
            'purpose'      => 'required|string',
            'result'       => 'nullable|string',
            'conclusion'   => 'nullable|string',
            'follow_up'    => 'nullable|string',
            'visitor_id'   => 'nullable',
            'visitor_name' => 'nullable|string|max:255',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'visitor_id', 'visitor_name', 'extra_visitors');

        if ($request->hasFile('attachment')) {
            if ($homeVisit->attachment) Storage::disk('public')->delete($homeVisit->attachment);
            $validated['attachment'] = $request->file('attachment')->store('home-visits', 'public');
        }

        $homeVisit->update($validated);
        ActivityLog::log('updated', 'home_visit', $homeVisit->id, 'Update home visit: '.$homeVisit->student->name);

        return redirect()->route('home-visits.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(HomeVisit $homeVisit)
    {
        if ($homeVisit->attachment) Storage::disk('public')->delete($homeVisit->attachment);
        ActivityLog::log('deleted', 'home_visit', $homeVisit->id, 'Hapus home visit: '.$homeVisit->student->name);
        $homeVisit->delete();
        return redirect()->route('home-visits.index')->with('success', 'Data berhasil dihapus.');
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

        $validated[$idField]    = $primary;
        $validated[$nameField]  = null;
        $validated[$extraField] = !empty($extras) ? $extras : null;
        return $validated;
    }
}
