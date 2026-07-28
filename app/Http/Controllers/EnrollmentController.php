<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function intake()
    {
        return view('enrollment.intake');
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:50'],
        ]);

        $student = Student::find($data['id']);
        if ($student) {
            return redirect()->route('enrollment.reentry', $student->id);
        }

        return redirect()->route('enrollment.new', ['id' => $data['id']]);
    }

    public function newEntry(Request $request)
    {
        $sections = Section::orderBy('grade_yr')->orderBy('section')->get();
        $teachers = User::where('utype', 'teacher')
            ->whereNotNull('assigned_grade')
            ->where('assigned_grade', '!=', '')
            ->whereNotNull('assigned_section')
            ->where('assigned_section', '!=', '')
            ->orderBy('fullname')
            ->get();

        return view('enrollment.new', [
            'studentId' => $request->query('id', ''),
            'sections' => $sections,
            'teachers' => $teachers,
        ]);
    }

    public function reEntry(Student $student)
    {
        $sections = Section::orderBy('grade_yr')->orderBy('section')->get();
        $teachers = User::where('utype', 'teacher')
            ->whereNotNull('assigned_grade')
            ->where('assigned_grade', '!=', '')
            ->whereNotNull('assigned_section')
            ->where('assigned_section', '!=', '')
            ->orderBy('fullname')
            ->get();

        return view('enrollment.reentry', compact('student', 'sections', 'teachers'));
    }

    public function storeNew(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'string', 'max:50', 'unique:students,id'],
            'lrn' => ['required', 'string', 'max:40'],
            'fname' => ['required', 'string', 'max:55'],
            'mname' => ['required', 'string', 'max:55'],
            'lname' => ['required', 'string', 'max:55'],
            'ext' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', 'in:Male,Female'],
            'bday' => ['required', 'date'],
            'grade' => ['required', 'string', 'max:40', Rule::exists('sections', 'grade_yr')],
            'section' => [
                'required',
                'string',
                'max:40',
                Rule::exists('sections', 'section')->where(fn ($query) => $query->where('grade_yr', $request->input('grade'))),
            ],
            'uid' => [
                'required',
                'integer',
                Rule::exists('users', 'uid')->where(fn ($query) => $query
                    ->where('utype', 'teacher')
                    ->where('assigned_grade', $request->input('grade'))
                    ->where('assigned_section', $request->input('section'))),
            ],
        ]);

        $grading = Grading::first();
        $fname = strtoupper($data['fname']);
        $mname = strtoupper($data['mname']);
        $lname = strtoupper($data['lname']);
        $ext = strtoupper((string) ($data['ext'] ?? ''));

        Student::create([
            'id' => $data['id'],
            'fname' => $fname,
            'mname' => $mname,
            'lname' => $lname,
            'ext' => $ext,
            'bday' => $data['bday'],
            'sex' => $data['sex'],
            'sname' => trim($fname . ' ' . $mname . ' ' . $lname . ' ' . $ext),
            'grade' => $data['grade'],
            'section' => $data['section'],
            'uid' => (string) $data['uid'],
            'grading' => $grading?->grading ?? 'First Grading',
            'sy' => $grading?->sy ?? date('Y') . '-' . (date('Y') + 1),
            'lrn' => $data['lrn'],
        ]);

        return redirect()->route('students.index')->with('ok', 'Student enrolled successfully.');
    }

    public function storeReEntry(Request $request, Student $student)
    {
        $data = $request->validate([
            'grade' => ['required', 'string', 'max:40', Rule::exists('sections', 'grade_yr')],
            'section' => [
                'required',
                'string',
                'max:40',
                Rule::exists('sections', 'section')->where(fn ($query) => $query->where('grade_yr', $request->input('grade'))),
            ],
            'uid' => [
                'required',
                'integer',
                Rule::exists('users', 'uid')->where(fn ($query) => $query
                    ->where('utype', 'teacher')
                    ->where('assigned_grade', $request->input('grade'))
                    ->where('assigned_section', $request->input('section'))),
            ],
        ]);

        $grading = Grading::first();
        $student->grade = $data['grade'];
        $student->section = $data['section'];
        $student->uid = (string) $data['uid'];
        $student->grading = $grading?->grading ?? 'First Grading';
        $student->sy = $grading?->sy ?? date('Y') . '-' . (date('Y') + 1);
        $student->save();

        return redirect()->route('students.index')->with('ok', 'Student re-enrolled successfully.');
    }
}
