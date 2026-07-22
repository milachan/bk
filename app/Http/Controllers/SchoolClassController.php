<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['homeroomTeacher', 'schoolYear'])->withCount('students')->orderBy('level')->orderBy('name')->paginate(20);
        return view('school-classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers    = User::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('is_active')->orderByDesc('name')->get();
        return view('school-classes.create', compact('teachers', 'schoolYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'level'               => 'required|in:VII,VIII,IX',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'school_year_id'      => 'nullable|exists:school_years,id',
        ]);

        SchoolClass::create($validated);
        return redirect()->route('school-classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $schoolClass)
    {
        $teachers    = User::orderBy('name')->get();
        $schoolYears = SchoolYear::orderByDesc('is_active')->orderByDesc('name')->get();
        return view('school-classes.edit', compact('schoolClass', 'teachers', 'schoolYears'));
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'level'               => 'required|in:X,XI,XII',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'school_year_id'      => 'nullable|exists:school_years,id',
        ]);

        $schoolClass->update($validated);
        return redirect()->route('school-classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        $schoolClass->delete();
        return redirect()->route('school-classes.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
