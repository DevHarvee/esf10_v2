<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $grading = Grading::first();
        $currentSy = $grading?->sy ?? $request->session()->get('sy');
        $currentTerm = $grading?->grading ?? $request->session()->get('grading');

        $activeCurrentSy = Student::where('sy', $currentSy)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhere('status', 'Retained');
            });

        $summary = [
            'total_students' => Student::count(),
            'total_users' => User::count(),
            'enrolled_this_sy' => (clone $activeCurrentSy)->count(),
            'male' => (clone $activeCurrentSy)->where('sex', 'Male')->count(),
            'female' => (clone $activeCurrentSy)->where('sex', 'Female')->count(),
            'current_sy' => $currentSy,
            'current_term' => $currentTerm,
        ];

        return view('dashboard', compact('summary'));
    }
}
