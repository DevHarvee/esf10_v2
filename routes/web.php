<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('esf.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/enrollment', [EnrollmentController::class, 'intake'])->name('enrollment.intake');
        Route::post('/enrollment/check', [EnrollmentController::class, 'check'])->name('enrollment.check');
        Route::get('/enrollment/new', [EnrollmentController::class, 'newEntry'])->name('enrollment.new');
        Route::post('/enrollment/new', [EnrollmentController::class, 'storeNew'])->name('enrollment.new.store');
        Route::get('/enrollment/re-entry/{student}', [EnrollmentController::class, 'reEntry'])->name('enrollment.reentry');
        Route::post('/enrollment/re-entry/{student}', [EnrollmentController::class, 'storeReEntry'])->name('enrollment.reentry.store');

        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/section/{section}', [StudentController::class, 'sectionRoster'])->name('students.section');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/grading', [SettingsController::class, 'updateGrading'])->name('settings.grading');
        Route::post('/settings/terms', [SettingsController::class, 'addTerm'])->name('settings.terms');
        Route::put('/settings/terms/{term}', [SettingsController::class, 'updateTerm'])->name('settings.terms.update');
        Route::delete('/settings/terms/{term}', [SettingsController::class, 'deleteTerm'])->name('settings.terms.delete');
        Route::post('/settings/sections', [SettingsController::class, 'addSection'])->name('settings.sections');
        Route::put('/settings/sections/{section}', [SettingsController::class, 'updateSection'])->name('settings.sections.update');
        Route::delete('/settings/sections/{section}', [SettingsController::class, 'deleteSection'])->name('settings.sections.delete');
        Route::post('/settings/subjects', [SettingsController::class, 'addSubject'])->name('settings.subjects');
        Route::put('/settings/subjects/{subject}', [SettingsController::class, 'updateSubject'])->name('settings.subjects.update');
        Route::delete('/settings/subjects/{subject}', [SettingsController::class, 'deleteSubject'])->name('settings.subjects.delete');

        Route::get('/users', [UserAccountController::class, 'index'])->name('users.index');
        Route::post('/users', [UserAccountController::class, 'store'])->name('users.store');
        Route::post('/users/{user}', [UserAccountController::class, 'update'])->name('users.update');
    });

    Route::middleware('role:teacher')->group(function () {
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/{student}/input', [GradeController::class, 'create'])->name('grades.create');
        Route::post('/grades/{student}/input', [GradeController::class, 'store'])->name('grades.store');
        Route::get('/grades/{student}/edit', [GradeController::class, 'edit'])->name('grades.edit');
        Route::post('/grades/{student}/edit', [GradeController::class, 'update'])->name('grades.update');
        Route::get('/grades/{student}/review', [GradeController::class, 'review'])->name('grades.review');
        Route::get('/grades/{student}/print', [GradeController::class, 'print'])->name('grades.print');
        Route::post('/grades/consolidate', [GradeController::class, 'consolidate'])->name('grades.consolidate');
    });

    Route::get('/reports/{student}', [ReportController::class, 'permanent'])->name('reports.permanent');
    Route::get('/reports/{student}/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/{student}/export', [ReportController::class, 'export'])->name('reports.export');
});
