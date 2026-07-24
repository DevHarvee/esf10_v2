<?php

namespace App\Http\Controllers;

use App\Models\GradingTerm;
use App\Models\Record;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getPermanentData(Student $student): array
    {
        $terms = GradingTerm::orderBy('term_order')->pluck('term_name')->all();
        $subjects = Subject::orderBy('id')->get();
        $years = SchoolYear::orderBy('sy')->get();

        $records = [];
        foreach ($years as $year) {
            $rows = Record::where('sname_id', $student->id)
                ->where('sy', $year->sy)
                ->get()
                ->keyBy('grading');
            if ($rows->isNotEmpty()) {
                $records[$year->sy] = $rows;
            }
        }

        $quarterColumnCount = count($terms);
        $tableColumnCount = $quarterColumnCount + 6;

        return compact('student', 'terms', 'subjects', 'records', 'quarterColumnCount', 'tableColumnCount');
    }

    public function permanent(Student $student)
    {
        return view('reports.permanent', $this->getPermanentData($student));
    }

    public function print(Request $request, Student $student)
    {
        $data = $this->getPermanentData($student);
        $data['autoprint'] = $request->boolean('autoprint');

        return view('reports.print', $data);
    }

    public function export(Student $student): Response
    {
        $response = view('reports.print', array_merge($this->getPermanentData($student), ['autoprint' => false]));
        return response($response->render(), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sf10-' . $student->id . '.xls"',
        ]);
    }
}
