<?php

namespace App\Http\Controllers;

use App\Models\Counseling;
use App\Models\LateRecord;
use App\Models\ParentMeeting;
use App\Models\Student;
use App\Models\ViolationRecord;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;

        if (!$q) {
            return view('search.index', ['q' => '', 'results' => null]);
        }

        $students = Student::with('class')
            ->where('name', 'like', "%$q%")
            ->orWhere('nis', 'like', "%$q%")
            ->orWhere('nisn', 'like', "%$q%")
            ->orderBy('name')
            ->take(10)
            ->get();

        $lates = LateRecord::with(['student.class'])
            ->whereHas('student', fn($sq) => $sq->where('name', 'like', "%$q%"))
            ->orWhere('reason', 'like', "%$q%")
            ->latest('date')
            ->take(10)
            ->get();

        $violations = ViolationRecord::with(['student.class', 'violationCategory'])
            ->whereHas('student', fn($sq) => $sq->where('name', 'like', "%$q%"))
            ->orWhere('description', 'like', "%$q%")
            ->latest('date')
            ->take(10)
            ->get();

        $counselings = Counseling::with(['student.class'])
            ->whereHas('student', fn($sq) => $sq->where('name', 'like', "%$q%"))
            ->orWhere('problem', 'like', "%$q%")
            ->latest('date')
            ->take(10)
            ->get();

        $parentMeetings = ParentMeeting::with(['student.class'])
            ->whereHas('student', fn($sq) => $sq->where('name', 'like', "%$q%"))
            ->orWhere('reason', 'like', "%$q%")
            ->latest('meeting_date')
            ->take(10)
            ->get();

        $results = [
            'students'       => $students,
            'lates'          => $lates,
            'violations'     => $violations,
            'counselings'    => $counselings,
            'parentMeetings' => $parentMeetings,
        ];

        return view('search.index', compact('q', 'results'));
    }
}
