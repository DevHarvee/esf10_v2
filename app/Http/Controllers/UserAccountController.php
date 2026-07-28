<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('fullname')->paginate(20);
        $sections = Section::orderBy('grade_yr')->orderBy('section')->get();

        return view('users.index', compact('users', 'sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:70'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'pwd' => ['required', 'string', 'min:4'],
            'utype' => ['required', 'in:admin,teacher'],
            'assigned_grade' => ['nullable', 'string', 'max:40', Rule::exists('sections', 'grade_yr')],
            'assigned_section' => [
                'nullable',
                'string',
                'max:40',
                Rule::exists('sections', 'section')->where(fn ($query) => $query->where('grade_yr', $request->input('assigned_grade'))),
            ],
        ]);

        if ($data['utype'] === 'teacher') {
            if (empty($data['assigned_grade']) || empty($data['assigned_section'])) {
                return back()->with('error', 'Assigned grade and section are required for teacher accounts.')->withInput();
            }
        } else {
            $data['assigned_grade'] = null;
            $data['assigned_section'] = null;
        }

        User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'pwd' => hash('sha512', $data['pwd']),
            'utype' => $data['utype'],
            'assigned_grade' => $data['assigned_grade'],
            'assigned_section' => $data['assigned_section'],
        ]);

        return back()->with('ok', 'User account created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:70'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->uid . ',uid'],
            'pwd' => ['nullable', 'string', 'min:4'],
            'utype' => ['required', 'in:admin,teacher'],
            'assigned_grade' => ['nullable', 'string', 'max:40', Rule::exists('sections', 'grade_yr')],
            'assigned_section' => [
                'nullable',
                'string',
                'max:40',
                Rule::exists('sections', 'section')->where(fn ($query) => $query->where('grade_yr', $request->input('assigned_grade'))),
            ],
        ]);

        if ($data['utype'] === 'teacher') {
            if (empty($data['assigned_grade']) || empty($data['assigned_section'])) {
                return back()->with('error', 'Assigned grade and section are required for teacher accounts.')->withInput();
            }
        } else {
            $data['assigned_grade'] = null;
            $data['assigned_section'] = null;
        }

        $user->fullname = $data['fullname'];
        $user->email = $data['email'];
        $user->utype = $data['utype'];
        $user->assigned_grade = $data['assigned_grade'];
        $user->assigned_section = $data['assigned_section'];
        if (!empty($data['pwd'])) {
            $user->pwd = hash('sha512', $data['pwd']);
        }
        $user->save();

        return back()->with('ok', 'User account updated.');
    }
}
