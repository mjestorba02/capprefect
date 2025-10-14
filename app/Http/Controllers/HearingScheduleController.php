<?php

namespace App\Http\Controllers;

use App\Models\HearingSchedule;
use App\Models\Student;
use App\Models\ViolationCategory;
use App\Models\Sanction;
use Illuminate\Http\Request;

class HearingScheduleController extends Controller
{
    public function index()
    {
        $hearings = HearingSchedule::with(['respondent', 'violation'])->paginate(10);

        // ✅ Only students who have sanctions
        $students = Student::whereIn('student_id', function ($query) {
            $query->select('student_id')->from('sanctions');
        })->get();

        $violations = ViolationCategory::all();

        return view('hearings.index', compact('hearings', 'students', 'violations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'respondent_id' => 'required|exists:students,student_id',
            'complainant' => 'required|string|max:255',
            'offense' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string|max:255',
            'officer_panel' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        // ✅ Get violation from sanction record
        $sanction = Sanction::where('student_id', $request->respondent_id)->first();
        if (!$sanction) {
            return redirect()->back()->with('error', 'Selected student has no sanction record.');
        }

        // ✅ Auto-generate record number (e.g., HS001)
        $latest = HearingSchedule::latest('id')->first();
        $nextNo = $latest ? $latest->id + 1 : 1;
        $recordNo = 'HS' . str_pad($nextNo, 3, '0', STR_PAD_LEFT);

        HearingSchedule::create([
            'record_no' => $recordNo,
            'respondent_id' => $request->respondent_id,
            'violation_id' => $sanction->category_id,
            'complainant' => $request->complainant,
            'offense' => $request->offense,
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
            'complainant' => 'required|string|max:255',
            'offense' => 'required|string|max:255',
            'date_of_hearing' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string|max:255',
            'officer_panel' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $sanction = Sanction::where('student_id', $request->respondent_id)->first();
        if (!$sanction) {
            return redirect()->back()->with('error', 'Selected student has no sanction record.');
        }

        $hearing->update([
            'respondent_id' => $request->respondent_id,
            'violation_id' => $sanction->category_id,
            'complainant' => $request->complainant,
            'offense' => $request->offense,
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

    public function getStudentInfo($id)
    {
        $student = Student::where('student_id', $id)->first();
        $sanction = Sanction::where('student_id', $id)->latest()->first();

        return response()->json([
            'year_level' => $student->year_level ?? 'N/A',
            'offense'     => $sanction->offense ?? 'N/A'
        ]);
    }
}