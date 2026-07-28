<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $currentSy = Grading::first()?->sy;

        $baseQuery = Student::query()->where('sy', $currentSy);
        $query = Student::query()->where('sy', $currentSy);

        if ($request->filled('grade')) {
            $query->where('grade', $request->string('grade'));
        }
        if ($request->filled('section')) {
            $query->where('section', $request->string('section'));
        }
        if ($request->filled('sex')) {
            $query->where('sex', $request->string('sex'));
        }
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('sname', 'like', "%{$term}%")
                    ->orWhere('id', 'like', "%{$term}%")
                    ->orWhere('lrn', 'like', "%{$term}%");
            });
        }

        $students = $query->orderBy('grade')->orderBy('section')->orderBy('lname')->paginate(20)->withQueryString();

        $gradeOptions = (clone $baseQuery)
            ->select('grade')
            ->whereNotNull('grade')
            ->where('grade', '!=', '')
            ->distinct()
            ->orderBy('grade')
            ->pluck('grade');

        $sectionOptions = (clone $baseQuery)
            ->select('section')
            ->whereNotNull('section')
            ->where('section', '!=', '')
            ->distinct()
            ->orderBy('section')
            ->pluck('section');

        $sexOptions = (clone $baseQuery)
            ->select('sex')
            ->whereNotNull('sex')
            ->where('sex', '!=', '')
            ->distinct()
            ->orderBy('sex')
            ->pluck('sex');

        return view('students.index', compact('students', 'currentSy', 'gradeOptions', 'sectionOptions', 'sexOptions'));
    }

    public function sectionRoster(string $section)
    {
        $currentSy = Grading::first()?->sy;
        $students = Student::where('sy', $currentSy)
            ->where('section', $section)
            ->orderBy('lname')
            ->get();

        return view('students.section', compact('students', 'section', 'currentSy'));
    }
}
