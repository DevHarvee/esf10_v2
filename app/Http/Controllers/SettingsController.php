<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\GradingTerm;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', [
            'grading' => Grading::first(),
            'terms' => GradingTerm::orderBy('term_order')->get(),
            'sections' => Section::orderBy('grade_yr')->orderBy('section')->get(),
            'subjects' => Subject::orderBy('id')->get(),
            'schoolYears' => SchoolYear::orderByDesc('sy')->get(),
        ]);
    }

    public function updateGrading(Request $request)
    {
        $data = $request->validate([
            'grading' => ['required', 'string', 'max:20'],
            'sy' => ['required', 'string', 'max:20'],
            'terms' => ['required', 'string'],
        ]);

        $terms = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['terms']) ?: [])));
        $terms = array_values(array_unique($terms));
        if (!in_array($data['grading'], $terms, true)) {
            return back()->with('error', 'Current grading period must exist in grading terms.');
        }

        Grading::updateOrCreate(['id' => 1], ['grading' => $data['grading'], 'sy' => $data['sy']]);
        SchoolYear::firstOrCreate(['sy' => $data['sy']]);

        GradingTerm::query()->delete();
        foreach ($terms as $idx => $term) {
            GradingTerm::create([
                'term_name' => $term,
                'term_order' => $idx + 1,
            ]);
        }

        return back()->with('ok', 'Grading settings updated.');
    }

    public function addSection(Request $request)
    {
        $data = $request->validate([
            'grade_yr' => ['required', 'string', 'max:20'],
            'section' => ['required', 'string', 'max:30'],
        ]);

        Section::create($data);
        return back()->with('ok', 'Section added.');
    }

    public function addSubject(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:60'],
        ]);

        Subject::create($data);
        return back()->with('ok', 'Subject added.');
    }
}
