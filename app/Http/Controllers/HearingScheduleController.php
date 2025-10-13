<?php

namespace App\Http\Controllers;

use App\Models\HearingSchedule;
use App\Models\Student;
use App\Models\ViolationCategory;
use Illuminate\Http\Request;

class HearingScheduleController extends Controller
{
    public function index()
    {
        $hearings = HearingSchedule::with(['respondent', 'violation'])->paginate(10);
        $students = Student::all();
        $violations = ViolationCategory::all();

        return view('hearings.index', compact('hearings', 'students', 'violations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'respondent_id' => 'required|exists:students,student_id',
            'violation_id' => 'required|exists:violation_categories,id',
            'complainant' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string|max:255',
            'officer_panel' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        // Auto-generate record number like HS001
        $latest = HearingSchedule::latest('id')->first();
        $nextNo = $latest ? $latest->id + 1 : 1;
        $recordNo = 'HS' . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

        HearingSchedule::create([
            'record_no' => $recordNo,
            'respondent_id' => $request->respondent_id,
            'violation_id' => $request->violation_id,
            'complainant' => $request->complainant,
            'date_of_hearing' => $request->date_of_hearing,
            'time' => $request->time,
            'venue' => $request->venue,
            'officer_panel' => $request->officer_panel,
            'status' => $request->status ?? 'Pending',
        ]);

        return redirect()->route('hearings.index')->with('success', 'Hearing schedule added successfully.');
    }

    public function update(Request $request, HearingSchedule $hearing)
    {
        $request->validate([
            'respondent_id' => 'required|exists:students,student_id',
            'violation_id' => 'required|exists:violation_categories,id',
            'complainant' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string|max:255',
            'officer_panel' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $hearing->update([
            'respondent_id' => $request->respondent_id,
            'violation_id' => $request->violation_id,
            'complainant' => $request->complainant,
            'date_of_hearing' => $request->date_of_hearing,
            'time' => $request->time,
            'venue' => $request->venue,
            'officer_panel' => $request->officer_panel,
            'status' => $request->status,
        ]);

        return redirect()->route('hearings.index')->with('success', 'Hearing schedule updated successfully.');
    }

    public function destroy(HearingSchedule $hearing)
    {
        $hearing->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing schedule deleted successfully.');
    }
}