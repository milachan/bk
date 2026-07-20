<?php

namespace App\Http\Controllers;

use App\Models\ViolationCategory;
use Illuminate\Http\Request;

class ViolationCategoryController extends Controller
{
    public function index()
    {
        $categories = ViolationCategory::orderBy('category')->orderBy('points')->paginate(20);
        return view('violation-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('violation-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:violation_categories,name',
            'category'    => 'required|in:ringan,sedang,berat',
            'points'      => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        ViolationCategory::create($validated);
        return redirect()->route('violation-categories.index')->with('success', 'Master pelanggaran berhasil ditambahkan.');
    }

    public function edit(ViolationCategory $violationCategory)
    {
        return view('violation-categories.edit', compact('violationCategory'));
    }

    public function update(Request $request, ViolationCategory $violationCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:violation_categories,name,'.$violationCategory->id,
            'category'    => 'required|in:ringan,sedang,berat',
            'points'      => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $violationCategory->update($validated);
        return redirect()->route('violation-categories.index')->with('success', 'Master pelanggaran berhasil diperbarui.');
    }

    public function destroy(ViolationCategory $violationCategory)
    {
        $violationCategory->delete();
        return redirect()->route('violation-categories.index')->with('success', 'Data berhasil dihapus.');
    }
}
