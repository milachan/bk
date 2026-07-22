<?php

namespace App\Http\Controllers;

use App\Exports\StudentTemplateExport;
use App\Imports\StudentsImport;
use App\Models\ActivityLog;
use App\Models\Counseling;
use App\Models\HomeVisit;
use App\Models\LateRecord;
use App\Models\ParentMeeting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\ViolationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('class')->orderBy('name');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('nis', 'like', '%'.$request->search.'%')
                  ->orWhere('nisn', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->location) {
            $query->where('location', $request->location);
        }

        $students = $query->paginate(20)->withQueryString();
        $classes  = SchoolClass::orderBy('name')->get();

        return view('students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'          => 'required|unique:students,nis',
            'nisn'         => 'nullable|unique:students,nisn',
            'name'         => 'required|string|max:255',
            'gender'       => 'required|in:L,P',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date',
            'religion'     => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'phone'        => 'nullable|string|max:20',
            'parent_name'  => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'class_id'     => 'nullable|exists:classes,id',
            'location'     => 'nullable|in:selatan,utara',
            'status'       => 'required|in:aktif,lulus,pindah,keluar',
            'photo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($validated);
        ActivityLog::log('created', 'student', $student->id, 'Tambah data siswa: '.$student->name);

        return redirect()->route('students.show', $student)->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load('class.homeroomTeacher');

        $stats = [
            'late_count'         => $student->lateRecords()->count(),
            'total_points'       => $student->violationRecords()->sum('points'),
            'counseling_count'   => $student->counselings()->count(),
            'parent_meeting_count' => $student->parentMeetings()->count(),
            'home_visit_count'   => $student->homeVisits()->count(),
        ];

        // Timeline: gabungan semua riwayat
        $lates = $student->lateRecords()->with('officer')->orderByDesc('date')->get()->map(fn($r) => [
            'type' => 'late', 'date' => $r->date, 'data' => $r,
        ]);
        $violations = $student->violationRecords()->with('violationCategory', 'reporter')->orderByDesc('date')->get()->map(fn($r) => [
            'type' => 'violation', 'date' => $r->date, 'data' => $r,
        ]);
        $counselings = $student->counselings()->with('counselor')->orderByDesc('date')->get()->map(fn($r) => [
            'type' => 'counseling', 'date' => $r->date, 'data' => $r,
        ]);
        $parentMeetings = $student->parentMeetings()->with('handler')->orderByDesc('meeting_date')->get()->map(fn($r) => [
            'type' => 'parent_meeting', 'date' => $r->meeting_date, 'data' => $r,
        ]);
        $homeVisits = $student->homeVisits()->with('visitor')->orderByDesc('visit_date')->get()->map(fn($r) => [
            'type' => 'home_visit', 'date' => $r->visit_date, 'data' => $r,
        ]);

        $timeline = $lates->concat($violations)->concat($counselings)->concat($parentMeetings)->concat($homeVisits)
            ->sortByDesc('date')->values();

        return view('students.show', compact('student', 'stats', 'timeline'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nis'          => 'required|unique:students,nis,'.$student->id,
            'nisn'         => 'nullable|unique:students,nisn,'.$student->id,
            'name'         => 'required|string|max:255',
            'gender'       => 'required|in:L,P',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date',
            'religion'     => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'phone'        => 'nullable|string|max:20',
            'parent_name'  => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'class_id'     => 'nullable|exists:classes,id',
            'location'     => 'nullable|in:selatan,utara',
            'status'       => 'required|in:aktif,lulus,pindah,keluar',
            'photo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) Storage::disk('public')->delete($student->photo);
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);
        ActivityLog::log('updated', 'student', $student->id, 'Update data siswa: '.$student->name);

        return redirect()->route('students.show', $student)->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if (!auth()->user()->canDelete()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data.');
        }
        if ($student->photo) Storage::disk('public')->delete($student->photo);
        ActivityLog::log('deleted', 'student', $student->id, 'Hapus siswa: '.$student->name);
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport(), 'template-import-siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            ActivityLog::log('created', 'student', null, "Import {$import->imported} siswa dari Excel");

            $msg = "Import selesai: {$import->imported} siswa berhasil diimpor.";
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} baris dilewati.";
            }

            return redirect()->route('students.index')
                ->with('success', $msg)
                ->with('import_errors', $import->errors);
        } catch (\Exception $e) {
            return redirect()->route('students.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}

