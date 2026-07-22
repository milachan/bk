<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ParentMeeting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParentMeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = ParentMeeting::with(['student.class', 'handler'])->latest('meeting_date');

        if ($request->search) {
            $query->whereHas('student', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }
        if ($request->date_from) $query->whereDate('meeting_date', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('meeting_date', '<=', $request->date_to);
        if ($request->location) {
            $query->whereHas('student', fn($q) => $q->where('location', $request->location));
        }
        if ($request->attended !== null && $request->attended !== '') {
            $query->where('parent_attended', $request->attended);
        }

        $records  = $query->paginate(20)->withQueryString();
        $handlers = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();

        $month = now()->month;
        $year  = now()->year;
        $stats = [
            'this_month' => ParentMeeting::whereMonth('meeting_date', $month)->whereYear('meeting_date', $year)->count(),
            'hadir'      => ParentMeeting::whereMonth('meeting_date', $month)->whereYear('meeting_date', $year)->where('parent_attended', true)->count(),
            'tidak_hadir'=> ParentMeeting::whereMonth('meeting_date', $month)->whereYear('meeting_date', $year)->where('parent_attended', false)->count(),
            'total'      => ParentMeeting::count(),
        ];

        // Chart 7 hari terakhir
        $chartLabels = [];
        $chartHadir  = [];
        $chartTidak  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = \Carbon\Carbon::now()->subDays($i);
            $chartLabels[] = $day->translatedFormat('D d/m');
            $chartHadir[]  = ParentMeeting::whereDate('meeting_date', $day->toDateString())->where('parent_attended', true)->count();
            $chartTidak[]  = ParentMeeting::whereDate('meeting_date', $day->toDateString())->where('parent_attended', false)->count();
        }

        return view('parent-meetings.index', compact('records', 'handlers', 'stats', 'chartLabels', 'chartHadir', 'chartTidak'));
    }

    public function create(Request $request)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $handlers = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        $selectedStudent = $request->student_id ? Student::find($request->student_id) : null;
        return view('parent-meetings.create', compact('students', 'handlers', 'selectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'meeting_date'    => 'required|date',
            'reason'          => 'required|string',
            'parent_attended' => 'required|boolean',
            'meeting_result'  => 'nullable|string',
            'follow_up'       => 'nullable|string',
            'handler_id'      => 'nullable',
            'handler_name'    => 'nullable|string|max:255',
            'attachment'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'handler_id', 'handler_name', 'extra_handlers');

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('parent-meetings', 'public');
        }

        $record = ParentMeeting::create($validated);
        ActivityLog::log('created', 'parent_meeting', $record->id, 'Input pemanggilan ortu: '.$record->student->name);

        return redirect()->route('parent-meetings.index')->with('success', 'Data pemanggilan orang tua berhasil disimpan.');
    }

    public function edit(ParentMeeting $parentMeeting)
    {
        $students = Student::where('status', 'aktif')->orderBy('name')->get();
        $handlers = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        return view('parent-meetings.edit', compact('parentMeeting', 'students', 'handlers'));
    }

    public function update(Request $request, ParentMeeting $parentMeeting)
    {
        $validated = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'meeting_date'    => 'required|date',
            'reason'          => 'required|string',
            'parent_attended' => 'required|boolean',
            'meeting_result'  => 'nullable|string',
            'follow_up'       => 'nullable|string',
            'handler_id'      => 'nullable',
            'handler_name'    => 'nullable|string|max:255',
            'attachment'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $validated = $this->resolveMultiStaff($request, $validated, 'handler_id', 'handler_name', 'extra_handlers');

        if ($request->hasFile('attachment')) {
            if ($parentMeeting->attachment) Storage::disk('public')->delete($parentMeeting->attachment);
            $validated['attachment'] = $request->file('attachment')->store('parent-meetings', 'public');
        }

        $parentMeeting->update($validated);
        ActivityLog::log('updated', 'parent_meeting', $parentMeeting->id, 'Update pemanggilan ortu: '.$parentMeeting->student->name);

        return redirect()->route('parent-meetings.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(ParentMeeting $parentMeeting)
    {
        if ($parentMeeting->attachment) Storage::disk('public')->delete($parentMeeting->attachment);
        ActivityLog::log('deleted', 'parent_meeting', $parentMeeting->id, 'Hapus pemanggilan ortu: '.$parentMeeting->student->name);
        $parentMeeting->delete();
        return redirect()->route('parent-meetings.index')->with('success', 'Data berhasil dihapus.');
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
