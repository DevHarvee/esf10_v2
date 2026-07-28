<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\GradingTerm;
use App\Models\Record;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private function computeSchoolYearFinalRating(Student $student, array $terms): ?int
    {
        $rows = Record::query()
            ->where('sname_id', $student->id)
            ->where('sy', $student->sy)
            ->whereIn('grading', $terms)
            ->get()
            ->keyBy('grading');

        if ($rows->count() !== count($terms)) {
            return null;
        }

        $subjects = Subject::orderBy('id')->get();
        $subjectAverages = [];

        foreach ($subjects as $index => $subject) {
            $key = 's' . ($index + 1);
            $termValues = [];

            foreach ($terms as $term) {
                $row = $rows->get($term);
                if (!$row) {
                    continue;
                }

                $value = data_get($row, $key);
                if ($key === 's8') {
                    $value = round((((float) data_get($row, 's9')) + ((float) data_get($row, 's10')) + ((float) data_get($row, 's11')) + ((float) data_get($row, 's12'))) / 4, 2);
                }

                if ($value !== null && $value !== '') {
                    $termValues[] = (float) $value;
                }
            }

            if (count($termValues) > 0) {
                $subjectAverages[] = round(array_sum($termValues) / count($termValues));
            }
        }

        if (count($subjectAverages) === 0) {
            return null;
        }

        return (int) round(array_sum($subjectAverages) / count($subjectAverages));
    }

    private function computeCumulativeFinalRating(Student $student, array $terms): ?int
    {
        $schoolYears = Record::query()
            ->where('sname_id', $student->id)
            ->select('sy')
            ->distinct()
            ->orderBy('sy')
            ->pluck('sy');

        $yearlyRatings = [];
        foreach ($schoolYears as $schoolYear) {
            $yearStudent = clone $student;
            $yearStudent->sy = $schoolYear;
            $yearlyRating = $this->computeSchoolYearFinalRating($yearStudent, $terms);
            if ($yearlyRating !== null) {
                $yearlyRatings[] = $yearlyRating;
            }
        }

        if (count($yearlyRatings) === 0) {
            return null;
        }

        return (int) round(array_sum($yearlyRatings) / count($yearlyRatings));
    }

    public function index(Request $request)
    {
        $currentSy = Grading::first()?->sy;

        $baseQuery = Student::query()
            ->where('sy', $currentSy)
            ->where(function ($builder) {
                $builder->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Retained');
            });
        $query = Student::query()
            ->where('sy', $currentSy)
            ->where(function ($builder) {
                $builder->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Retained');
            });

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
            ->where(function ($builder) {
                $builder->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Retained');
            })
            ->orderBy('lname')
            ->get();

        return view('students.section', compact('students', 'section', 'currentSy'));
    }

    public function graduates(Request $request)
    {
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();
        $query = Student::query()->where('status', 'Graduated');

        if ($request->filled('sy')) {
            $query->where('sy', $request->string('sy'));
        }

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('sname', 'like', "%{$term}%")
                    ->orWhere('id', 'like', "%{$term}%")
                    ->orWhere('lrn', 'like', "%{$term}%");
            });
        }

        $graduates = $query
            ->orderByDesc('sy')
            ->orderBy('lname')
            ->paginate(20)
            ->withQueryString();

        $graduates->getCollection()->transform(function (Student $student) use ($terms) {
            $computedFinalRating = $this->computeCumulativeFinalRating($student, $terms);
            if ($computedFinalRating !== null) {
                $student->final_rating = (string) $computedFinalRating;
            }

            return $student;
        });

        $schoolYears = Student::query()
            ->where('status', 'Graduated')
            ->select('sy')
            ->whereNotNull('sy')
            ->where('sy', '!=', '')
            ->distinct()
            ->orderByDesc('sy')
            ->pluck('sy');

        return view('students.graduates', compact('graduates', 'schoolYears'));
    }
}
