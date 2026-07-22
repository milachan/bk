<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Counseling;
use App\Models\HomeVisit;
use App\Models\LateRecord;
use App\Models\ParentMeeting;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationCategory;
use App\Models\ViolationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuickEntryController extends Controller
{
    public function create()
    {
        $violationCategories = ViolationCategory::orderBy('name')->get();
        $officers   = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_piket', 'guru_bk', 'admin']))->orderBy('name')->get();
        $counselors = User::whereHas('role', fn($q) => $q->whereIn('name', ['guru_bk', 'admin']))->orderBy('name')->get();
        return view('quick-entry.create', compact('violationCategories', 'officers', 'counselors'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');

        match ($type) {
            'late'           => $this->storeLate($request),
            'violation'      => $this->storeViolation($request),
            'counseling'     => $this->storeCounseling($request),
            'parent_meeting' => $this->storeParentMeeting($request),
            'home_visit'     => $this->storeHomeVisit($request),
            default          => abort(422, 'Tipe tidak valid'),
        };

        return redirect()->route('quick-entry.create')
            ->with('success', 'Data berhasil disimpan.');
    }

    // ── Helper: resolve officer field (id atau manual name) ──────────────────
    private function resolveOfficer(Request $request, string $idField, string $nameField): array
    {
        if ($request->input($idField) === 'other') {
            return [
                $idField   => null,
                $nameField => $request->input($nameField) ?? 'Tidak diketahui',
            ];
        }
        return [
            $idField   => $request->input($idField) ?: null,
            $nameField => null,
        ];
    }

    // ── Helper: resolve multi-user (array of ids + extras JSON) ─────────────
    private function resolveMultiStaff(Request $request, string $primaryIdField, string $primaryNameField, string $extraField): array
    {
        $selected = (array) $request->input($primaryIdField, []);
        $primary  = null;
        $primaryName = null;
        $extras   = [];

        foreach ($selected as $val) {
            if ($val === 'other') {
                $manualName = trim($request->input($primaryNameField, ''));
                if ($manualName) {
                    $extras[] = $manualName;
                }
            } elseif ($primary === null) {
                $primary = (int) $val; // first selected user becomes primary
            } else {
                // additional users stored in extras
                $user = User::find((int) $val);
                if ($user) $extras[] = $user->name;
            }
        }

        return [
            $primaryIdField   => $primary,
            $primaryNameField => $primaryName,
            $extraField       => !empty($extras) ? $extras : null,
        ];
    }

    private function storeLate(Request $request)
    {
        $request->validate([
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

        $officer = $this->resolveOfficer($request, 'officer_id', 'officer_name');

        $r = LateRecord::create([
            'student_id'       => $request->student_id,
            'date'             => $request->date,
            'arrive_time'      => $request->arrive_time,
            'entry_time'       => $request->entry_time,
            'duration_minutes' => $request->duration_minutes,
            'reason'           => $request->reason,
            'notes'            => $request->notes,
            'officer_id'       => $officer['officer_id'],
            'officer_name'     => $officer['officer_name'],
        ]);

        ActivityLog::log('created', 'late_record', $r->id, 'Input keterlambatan: '.$r->student->name);
    }

    private function storeViolation(Request $request)
    {
        $request->validate([
            'student_id'            => 'required|exists:students,id',
            'violation_category_id' => 'required',
            'date'                  => 'required|date',
            'points'                => 'nullable|integer|min:0',
            'description'           => 'nullable|string',
            'reporter_id'           => 'nullable',
            'reporter_name'         => 'nullable|string|max:255',
            'notes'                 => 'nullable|string',
            'evidence_photo'        => 'nullable|image|max:5120',
        ]);

        $reporter = $this->resolveOfficer($request, 'reporter_id', 'reporter_name');

        // Handle "Pelanggaran Lainnya"
        $catId  = $request->violation_category_id;
        $points = $request->points;
        if ($catId === 'other') {
            $request->validate([
                'other_violation_name'     => 'required|string|max:255',
                'other_violation_category' => 'required|in:ringan,sedang,berat',
                'other_violation_points'   => 'required|integer|min:0|max:100',
            ]);
            $newCat = \App\Models\ViolationCategory::create([
                'name'        => $request->other_violation_name,
                'category'    => $request->other_violation_category,
                'points'      => $request->other_violation_points,
                'description' => $request->other_violation_description,
            ]);
            $catId  = $newCat->id;
            $points = $request->other_violation_points;
        }

        $data = [
            'student_id'            => $request->student_id,
            'violation_category_id' => $catId,
            'date'                  => $request->date,
            'points'                => $points,
            'description'           => $request->description,
            'notes'                 => $request->notes,
            'reporter_id'           => $reporter['reporter_id'],
            'reporter_name'         => $reporter['reporter_name'],
        ];

        if ($request->hasFile('evidence_photo')) {
            $data['evidence_photo'] = $request->file('evidence_photo')->store('violations', 'public');
        }

        $r = ViolationRecord::create($data);
        ActivityLog::log('created', 'violation_record', $r->id, 'Input pelanggaran: '.$r->student->name);
    }

    private function storeCounseling(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'date'          => 'required|date',
            'problem'       => 'required|string',
            'result'        => 'nullable|string',
            'solution'      => 'nullable|string',
            'counselor_id'  => 'nullable',
            'counselor_name'=> 'nullable|string|max:255',
            'attachment'    => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $staff = $this->resolveMultiStaff($request, 'counselor_id', 'counselor_name', 'extra_counselors');

        $data = [
            'student_id'        => $request->student_id,
            'date'              => $request->date,
            'problem'           => $request->problem,
            'result'            => $request->result,
            'solution'          => $request->solution,
            'counselor_id'      => $staff['counselor_id'],
            'counselor_name'    => $staff['counselor_name'],
            'extra_counselors'  => $staff['extra_counselors'],
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('counselings', 'public');
        }

        $r = Counseling::create($data);
        ActivityLog::log('created', 'counseling', $r->id, 'Input konseling: '.$r->student->name);
    }

    private function storeParentMeeting(Request $request)
    {
        $request->validate([
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

        $staff = $this->resolveMultiStaff($request, 'handler_id', 'handler_name', 'extra_handlers');

        $data = [
            'student_id'      => $request->student_id,
            'meeting_date'    => $request->meeting_date,
            'reason'          => $request->reason,
            'parent_attended' => $request->parent_attended,
            'meeting_result'  => $request->meeting_result,
            'follow_up'       => $request->follow_up,
            'handler_id'      => $staff['handler_id'],
            'handler_name'    => $staff['handler_name'],
            'extra_handlers'  => $staff['extra_handlers'],
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('parent-meetings', 'public');
        }

        $r = ParentMeeting::create($data);
        ActivityLog::log('created', 'parent_meeting', $r->id, 'Input pemanggilan ortu: '.$r->student->name);
    }

    private function storeHomeVisit(Request $request)
    {
        $request->validate([
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

        $staff = $this->resolveMultiStaff($request, 'visitor_id', 'visitor_name', 'extra_visitors');

        $data = [
            'student_id'     => $request->student_id,
            'visit_date'     => $request->visit_date,
            'address'        => $request->address,
            'purpose'        => $request->purpose,
            'result'         => $request->result,
            'conclusion'     => $request->conclusion,
            'follow_up'      => $request->follow_up,
            'visitor_id'     => $staff['visitor_id'],
            'visitor_name'   => $staff['visitor_name'],
            'extra_visitors' => $staff['extra_visitors'],
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('home-visits', 'public');
        }

        $r = HomeVisit::create($data);
        ActivityLog::log('created', 'home_visit', $r->id, 'Input home visit: '.$r->student->name);
    }
}
