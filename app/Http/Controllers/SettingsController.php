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
            'sections' => Section::orderBy('grade_yr')->orderBy('section')->paginate(10, ['*'], 'sections_page'),
            'subjects' => Subject::orderBy('id')->paginate(10, ['*'], 'subjects_page'),
            'schoolYears' => SchoolYear::orderByDesc('sy')->get(),
        ]);
    }

    public function updateGrading(Request $request)
    {
        $data = $request->validate([
            'grading' => ['required', 'string', 'max:20'],
            'sy' => ['required', 'string', 'max:20'],
        ]);

        $termExists = GradingTerm::query()->where('term_name', $data['grading'])->exists();
        if (!$termExists) {
            return back()->with('error', 'Current grading period must exist in grading terms.');
        }

        Grading::updateOrCreate(['id' => 1], ['grading' => $data['grading'], 'sy' => $data['sy']]);
        SchoolYear::firstOrCreate(['sy' => $data['sy']]);

        return back()->with('ok', 'Grading settings updated.');
    }

    public function addTerm(Request $request)
    {
        $data = $request->validate([
            'term_name' => ['required', 'string', 'max:100'],
        ]);

        $termName = trim($data['term_name']);
        $exists = GradingTerm::query()->where('term_name', $termName)->exists();
        if ($exists) {
            return back()->with('error', 'Term already exists.');
        }

        $nextOrder = (int) GradingTerm::query()->max('term_order') + 1;
        GradingTerm::create([
            'term_name' => $termName,
            'term_order' => $nextOrder,
        ]);

        return back()->with('ok', 'Term added.');
    }

    public function updateTerm(Request $request, GradingTerm $term)
    {
        $data = $request->validate([
            'term_name' => ['required', 'string', 'max:100'],
        ]);

        $newName = trim($data['term_name']);
        $duplicate = GradingTerm::query()
            ->where('term_name', $newName)
            ->where('id', '!=', $term->id)
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Term already exists.');
        }

        $previousName = $term->term_name;
        $term->update(['term_name' => $newName]);

        $grading = Grading::first();
        if ($grading && $grading->grading === $previousName) {
            $grading->update(['grading' => $newName]);
        }

        return back()->with('ok', 'Term updated.');
    }

    public function deleteTerm(GradingTerm $term)
    {
        if (GradingTerm::query()->count() <= 1) {
            return back()->with('error', 'At least one grading term is required.');
        }

        $deletedName = $term->term_name;
        $term->delete();

        $orderedTerms = GradingTerm::query()->orderBy('term_order')->orderBy('id')->get();
        foreach ($orderedTerms as $index => $row) {
            $row->update(['term_order' => $index + 1]);
        }

        $grading = Grading::first();
        if ($grading && $grading->grading === $deletedName) {
            $replacement = GradingTerm::query()->orderBy('term_order')->value('term_name');
            if ($replacement) {
                $grading->update(['grading' => $replacement]);
            }
        }

        return back()->with('ok', 'Term removed.');
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

    public function updateSection(Request $request, Section $section)
    {
        $data = $request->validate([
            'grade_yr' => ['required', 'string', 'max:20'],
            'section' => ['required', 'string', 'max:30'],
        ]);

        $section->update($data);
        return back()->with('ok', 'Section updated.');
    }

    public function deleteSection(Section $section)
    {
        $section->delete();
        return back()->with('ok', 'Section removed.');
    }

    public function addSubject(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:60'],
        ]);

        Subject::create($data);
        return back()->with('ok', 'Subject added.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:60'],
        ]);

        $subject->update($data);
        return back()->with('ok', 'Subject updated.');
    }

    public function deleteSubject(Subject $subject)
    {
        $subject->delete();
        return back()->with('ok', 'Subject removed.');
    }
}
