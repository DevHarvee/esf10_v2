<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    public function index()
    {
        $users = User::orderBy('fullname')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:70'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'pwd' => ['required', 'string', 'min:4'],
            'utype' => ['required', 'in:admin,teacher'],
        ]);

        User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'pwd' => hash('sha512', $data['pwd']),
            'utype' => $data['utype'],
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
        ]);

        $user->fullname = $data['fullname'];
        $user->email = $data['email'];
        $user->utype = $data['utype'];
        if (!empty($data['pwd'])) {
            $user->pwd = hash('sha512', $data['pwd']);
        }
        $user->save();

        return back()->with('ok', 'User account updated.');
    }
}
