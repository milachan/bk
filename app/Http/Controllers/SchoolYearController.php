<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::orderByDesc('is_active')->orderByDesc('name')->paginate(20);
        return view('school-years.index', compact('schoolYears'));
    }

    public function create()
    {
        return view('school-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:school_years,name',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        if (!empty($validated['is_active'])) {
            SchoolYear::where('is_active', true)->update(['is_active' => false]);
        }

        SchoolYear::create($validated);
        return redirect()->route('school-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(SchoolYear $schoolYear)
    {
        return view('school-years.edit', compact('schoolYear'));
    }

    public function update(Request $request, SchoolYear $schoolYear)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:20|unique:school_years,name,'.$schoolYear->id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        if (!empty($validated['is_active'])) {
            SchoolYear::where('is_active', true)->where('id', '!=', $schoolYear->id)->update(['is_active' => false]);
        }

        $schoolYear->update($validated);
        return redirect()->route('school-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(SchoolYear $schoolYear)
    {
        $schoolYear->delete();
        return redirect()->route('school-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
