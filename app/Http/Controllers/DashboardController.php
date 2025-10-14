<?php

namespace App\Http\Controllers;

use App\Models\{
    Student,
    Sanction,
    HearingSchedule,
    Infraction,
    Behavior,
    ClearanceHold,
    ReformationProgram
};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $totalStudents = Student::count();
        $activeSanctions = Sanction::where('status', 'Active')->count();
        $pendingHearings = HearingSchedule::where('status', 'Pending')->count();
        $unresolvedInfractions = Infraction::where('status', 'Unresolved')->count();
        $behaviorReports = Behavior::count();
        $clearanceHolds = ClearanceHold::where('status', 'On Hold')->count();
        $activePrograms = ReformationProgram::where('status', 'Active')->count();

        // Recent Activity (combine various logs)
        $recentActivity = collect()
            ->merge(Behavior::latest()->take(3)->get()->map(fn($r) => [
                'type' => 'Behavior Report',
                'desc' => $r->description,
                'date' => $r->date_recorded,
            ]))
            ->merge(Sanction::latest()->take(3)->get()->map(fn($r) => [
                'type' => 'Sanction Issued',
                'desc' => $r->offense,
                'date' => $r->date_issued,
            ]))
            ->merge(Infraction::latest()->take(3)->get()->map(fn($r) => [
                'type' => 'Infraction Logged',
                'desc' => $r->description,
                'date' => $r->datetime,
            ]))
            ->sortByDesc('date')
            ->take(5);

        return view('dashboard', compact(
            'totalStudents',
            'activeSanctions',
            'pendingHearings',
            'unresolvedInfractions',
            'behaviorReports',
            'clearanceHolds',
            'activePrograms',
            'recentActivity'
        ));
    }
}