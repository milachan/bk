<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = $request->input('q', '');
        $students = Student::where('status', 'aktif')
            ->where(fn($query) => $query
                ->where('name', 'like', '%'.$q.'%')
                ->orWhere('nis', 'like', '%'.$q.'%')
            )
            ->with('class')
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'nis', 'class_id', 'location']);

        return response()->json($students->map(fn($s) => [
            'id'       => $s->id,
            'text'     => $s->name.' ('.$s->nis.')',
            'name'     => $s->name,
            'nis'      => $s->nis,
            'class'    => $s->class?->name ?? '-',
            'location' => $s->location ?? '',
        ]));
    }
}
