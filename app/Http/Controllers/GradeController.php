<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\GradingTerm;
use App\Models\InputtedGrade;
use App\Models\Record;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    private function isActiveStudentStatus(?string $status): bool
    {
        return !in_array($status, ['Promoted', 'Graduated'], true);
    }

    private function computeMapeh(array $data): string
    {
        $music = (float) ($data['s9'] ?? 0);
        $arts = (float) ($data['s10'] ?? 0);
        $pe = (float) ($data['s11'] ?? 0);
        $health = (float) ($data['s12'] ?? 0);

        return number_format(($music + $arts + $pe + $health) / 4, 2, '.', '');
    }

    private function buildColumnSpans(int $totalColumns, int $groupCount): array
    {
        $base = intdiv($totalColumns, $groupCount);
        $remainder = $totalColumns % $groupCount;
        $spans = [];

        for ($i = 0; $i < $groupCount; $i++) {
            $spans[] = $base + ($i < $remainder ? 1 : 0);
        }

        return $spans;
    }

    private function getGradeRowsByTerm(Student $student, string $sy)
    {
        $rows = InputtedGrade::where('sname_id', $student->id)
            ->where('sy', $sy)
            ->get()
            ->keyBy('grading');

        if ($rows->isNotEmpty()) {
            return $rows;
        }

        return Record::where('sname_id', $student->id)
            ->where('sy', $sy)
            ->get()
            ->keyBy('grading');
    }

    private function computeStudentFinalRating(Student $student, array $terms, string $sy): ?int
    {
        $rows = Record::where('sname_id', $student->id)
            ->where('sy', $sy)
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
        $schoolYears = Record::where('sname_id', $student->id)
            ->select('sy')
            ->distinct()
            ->orderBy('sy')
            ->pluck('sy');

        $yearlyRatings = [];
        foreach ($schoolYears as $schoolYear) {
            $yearlyRating = $this->computeStudentFinalRating($student, $terms, $schoolYear);
            if ($yearlyRating !== null) {
                $yearlyRatings[] = $yearlyRating;
            }
        }

        if (count($yearlyRatings) === 0) {
            return null;
        }

        return (int) round(array_sum($yearlyRatings) / count($yearlyRatings));
    }

    private function resolveCompletionStatus(Student $student, ?int $finalRating): string
    {
        if ($finalRating === null) {
            return '';
        }

        if ($finalRating < 75) {
            return 'Retained';
        }

        return $student->grade === 'Grade 10' ? 'Graduated' : 'Promoted';
    }

    public function index()
    {
        $teacherId = (string) Auth::id();
        $grading = Grading::first();
        $currentTerm = $grading?->grading ?? '';
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();

        $students = Student::where('uid', $teacherId)
            ->where('sy', $grading?->sy)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Retained');
            })
            ->orderBy('grade')
            ->orderBy('section')
            ->orderBy('lname')
            ->get();

        $statuses = [];
        $locked = [];
        foreach ($students as $student) {
            foreach ($terms as $term) {
                $hasInput = InputtedGrade::where('sname_id', $student->id)
                    ->where('grading', $term)
                    ->where('sy', $grading?->sy)
                    ->exists();

                $hasRecord = Record::where('sname_id', $student->id)
                    ->where('grading', $term)
                    ->where('sy', $grading?->sy)
                    ->exists();

                $statuses[$student->id][$term] = $hasInput || $hasRecord;
            }

            $locked[$student->id] = Record::where('sname_id', $student->id)
                ->where('grading', $currentTerm)
                ->where('sy', $grading?->sy)
                ->exists();
        }

        return view('grades.index', compact('students', 'terms', 'statuses', 'grading', 'locked'));
    }

    public function create(Student $student)
    {
        $subjects = Subject::orderBy('id')->get();
        $grading = Grading::first();

        $isLocked = Record::where('sname_id', $student->id)
            ->where('grading', $grading?->grading)
            ->where('sy', $grading?->sy)
            ->exists();

        if ($isLocked) {
            return redirect()->route('grades.index')->with('error', 'Cannot input grade. This term is already consolidated.');
        }

        return view('grades.create', compact('student', 'subjects', 'grading'));
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $fields = [];
        for ($i = 1; $i <= 12; $i++) {
            if ($i === 8) {
                continue;
            }

            if ($i >= 9 && $i <= 12) {
                $fields['s' . $i] = ['required', 'numeric', 'min:0', 'max:100'];
            } else {
                $fields['s' . $i] = ['nullable', 'numeric', 'min:0', 'max:100'];
            }
        }
        $data = $request->validate($fields);

        $grading = Grading::first();
        $exists = InputtedGrade::where('sname_id', $student->id)
            ->where('grading', $grading?->grading)
            ->where('sy', $grading?->sy)
            ->exists();

        $alreadyConsolidated = Record::where('sname_id', $student->id)
            ->where('grading', $grading?->grading)
            ->where('sy', $grading?->sy)
            ->exists();

        if ($exists || $alreadyConsolidated) {
            return back()->with('error', 'Grades already submitted for this quarter.');
        }

        $data['s8'] = $this->computeMapeh($data);

        InputtedGrade::create(array_merge($data, [
            'sname_id' => $student->id,
            'uid' => (string) Auth::id(),
            'section' => $student->section,
            'grade' => $student->grade,
            'grading' => $grading?->grading ?? 'First Grading',
            'sy' => $grading?->sy ?? date('Y') . '-' . (date('Y') + 1),
        ]));

        return redirect()->route('grades.index')->with('ok', 'Grades saved.');
    }

    public function edit(Student $student)
    {
        $grading = Grading::first();
        $grade = InputtedGrade::where('sname_id', $student->id)
            ->where('grading', $grading?->grading)
            ->where('sy', $grading?->sy)
            ->first();

        if (!$grade) {
            $alreadyConsolidated = Record::where('sname_id', $student->id)
                ->where('grading', $grading?->grading)
                ->where('sy', $grading?->sy)
                ->exists();

            if ($alreadyConsolidated) {
                return redirect()->route('grades.index')->with('error', 'Cannot edit already consolidated grades.');
            }

            return redirect()->route('grades.index')->with('error', 'No draft grade found for this term.');
        }

        if (Record::where('sname_id', $student->id)->where('grading', $grade->grading)->where('sy', $grade->sy)->exists()) {
            return redirect()->route('grades.index')->with('error', 'Cannot edit already consolidated grades.');
        }

        $subjects = Subject::orderBy('id')->get();
        return view('grades.edit', compact('student', 'grade', 'subjects', 'grading'));
    }

    public function update(Request $request, Student $student)
    {
        $grading = Grading::first();
        $row = InputtedGrade::where('sname_id', $student->id)
            ->where('grading', $grading?->grading)
            ->where('sy', $grading?->sy)
            ->firstOrFail();

        if (Record::where('sname_id', $student->id)->where('grading', $row->grading)->where('sy', $row->sy)->exists()) {
            return redirect()->route('grades.index')->with('error', 'Cannot edit already consolidated grades.');
        }

        $fields = [];
        for ($i = 1; $i <= 12; $i++) {
            if ($i === 8) {
                continue;
            }

            if ($i >= 9 && $i <= 12) {
                $fields['s' . $i] = ['required', 'numeric', 'min:0', 'max:100'];
            } else {
                $fields['s' . $i] = ['nullable', 'numeric', 'min:0', 'max:100'];
            }
        }
        $data = $request->validate($fields);

        $data['s8'] = $this->computeMapeh($data);

        $row->update($data);
        return redirect()->route('grades.index')->with('ok', 'Grades updated.');
    }

    public function review(Student $student)
    {
        $grading = Grading::first();
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();
        $subjects = Subject::orderBy('id')->get();
        $rows = $this->getGradeRowsByTerm($student, $grading?->sy ?? $student->sy);

        return view('grades.review', compact('student', 'terms', 'subjects', 'rows', 'grading'));
    }

    public function print(Student $student)
    {
        $grading = Grading::first();
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();
        $subjects = Subject::orderBy('id')->get();
        $schoolYear = $grading?->sy ?? $student->sy;
        $rows = $this->getGradeRowsByTerm($student, $schoolYear);

        $quarterColumnCount = count($terms);
        $tableColumnCount = $quarterColumnCount + 6;
        $remedialSpans = $this->buildColumnSpans($tableColumnCount, 5);

        return view('grades.print', compact(
            'student',
            'terms',
            'subjects',
            'rows',
            'grading',
            'schoolYear',
            'quarterColumnCount',
            'tableColumnCount',
            'remedialSpans'
        ));
    }

    public function consolidate(Request $request)
    {
        $teacherId = (string) Auth::id();
        $grading = Grading::first();
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();

        if (($grading?->grading ?? '') !== ($terms[count($terms) - 1] ?? 'Fourth Grading')) {
            return back()->with('error', 'Consolidation is only allowed in the final grading term.');
        }

        $students = Student::where('uid', $teacherId)->where('sy', $grading?->sy)->get();

        foreach ($students as $student) {
            foreach ($terms as $term) {
                $has = InputtedGrade::where('sname_id', $student->id)
                    ->where('sy', $grading?->sy)
                    ->where('grading', $term)
                    ->exists()
                    || Record::where('sname_id', $student->id)
                        ->where('sy', $grading?->sy)
                        ->where('grading', $term)
                        ->exists();
                if (!$has) {
                    return back()->with('error', "Missing {$term} grades for {$student->sname}.");
                }
            }
        }

        DB::transaction(function () use ($teacherId, $grading) {
            $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();
            $grades = InputtedGrade::where('uid', $teacherId)->where('sy', $grading?->sy)->get();

            foreach ($grades as $grade) {
                Record::create($grade->only([
                    'sname_id', 'uid', 'section', 'grade', 'grading', 'sy',
                    's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's9', 's10', 's11', 's12',
                ]) + ['status' => '']);
            }

            $students = Student::where('uid', $teacherId)
                ->where('sy', $grading?->sy)
                ->get();

            foreach ($students as $student) {
                $schoolYearFinalRating = $this->computeStudentFinalRating($student, $terms, $grading?->sy ?? $student->sy);
                $finalRating = $this->computeCumulativeFinalRating($student, $terms);
                $student->update([
                    'final_rating' => $finalRating === null ? '' : (string) $finalRating,
                    'status' => $this->resolveCompletionStatus($student, $schoolYearFinalRating),
                ]);
            }

            InputtedGrade::where('uid', $teacherId)->where('sy', $grading?->sy)->delete();
        });

        return redirect()->route('grades.index')->with('ok', 'Grades consolidated and submitted to registrar.');
    }
}
