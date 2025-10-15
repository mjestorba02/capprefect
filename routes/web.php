<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\BehaviorController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ViolationCategoryController;
use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\ReformationProgramController;
use App\Http\Controllers\InfractionController;
use App\Http\Controllers\ClearanceHoldController;
use App\Http\Controllers\HearingScheduleController;
use App\Http\Controllers\ParentNotificationController;

// Redirect to login
Route::get('/', fn() => redirect()->route('login'));

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Sanction Management
Route::get('/sanctions', [SanctionController::class, 'index'])->name('sanctions.index');
Route::post('/sanctions', [SanctionController::class, 'store'])->name('sanctions.store');
Route::put('/sanctions/{id}', [SanctionController::class, 'update'])->name('sanctions.update');
Route::delete('/sanctions/{id}', [SanctionController::class, 'destroy'])->name('sanctions.destroy');

//Violation Categories
Route::resource('violation_categories', ViolationCategoryController::class);

//Incident Reports
Route::resource('incident_reports', IncidentReportController::class);
Route::get('/incident-reports/export', [IncidentReportController::class, 'export'])
    ->name('incident_reports.export');

//Behavior Management
Route::resource('behaviors', BehaviorController::class);
Route::patch('behaviors/{behavior}/resolve', [BehaviorController::class, 'resolve'])->name('behaviors.resolve');

//Reformation Program
Route::resource('reformation_programs', ReformationProgramController::class);

//Infraction
Route::resource('infractions', InfractionController::class);

Route::get('/violation-category/{id}', function ($id) {
    $category = \App\Models\ViolationCategory::where('category_name', $id)->first();
    return response()->json($category);
});

// Clearance Hold Management
Route::get('/clearance_holds', [ClearanceHoldController::class, 'index'])->name('clearance_holds.index');
Route::post('/clearance_holds', [ClearanceHoldController::class, 'store'])->name('clearance_holds.store');
Route::put('/clearance_holds/{id}', [ClearanceHoldController::class, 'update'])->name('clearance_holds.update');
Route::delete('/clearance_holds/{id}', [ClearanceHoldController::class, 'destroy'])->name('clearance_holds.destroy');

// Special actions
Route::post('/clearance_holds/flag', [ClearanceHoldController::class, 'flag'])->name('clearance_holds.flag');
Route::post('/clearance_holds/{id}/lift', [ClearanceHoldController::class, 'lift'])->name('clearance_holds.lift');

//Hearing Schedule
Route::resource('hearings', HearingScheduleController::class);
Route::post('/hearings/{hearing}', [HearingScheduleController::class, 'update'])->name('hearings.update');
Route::get('/hearings/student-info/{id}', [HearingScheduleController::class, 'getStudentInfo'])
    ->name('hearings.student-info');

Route::get('/notifications/parents', [ParentNotificationController::class, 'index'])->name('notifications.parents');
Route::post('/notifications/parents/notify', [ParentNotificationController::class, 'notify'])->name('notifications.parents.notify');