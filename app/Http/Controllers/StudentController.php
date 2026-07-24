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

        return view('students.index', compact('students', 'currentSy'));
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
