<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])
            ->where('pwd', hash('sha512', $data['password']))
            ->first();

        if (!$user) {
            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $grading = Grading::first();
        $request->session()->put('grading', $grading?->grading ?? 'First Grading');
        $request->session()->put('sy', $grading?->sy ?? date('Y') . '-' . (date('Y') + 1));

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
