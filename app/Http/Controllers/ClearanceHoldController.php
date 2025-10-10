<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClearanceHold;
use App\Models\Student;

class ClearanceHoldController extends Controller
{
    /**
     * Display all clearance holds and related students.
     */
    public function index()
    {
        $holds = ClearanceHold::with('student')->latest()->paginate(10);
        $students = Student::all();

        return view('clearance_holds.index', compact('holds', 'students'));
    }

    /**
     * Store a new clearance hold.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,student_id',
            'department'   => 'required|string|max:255',
            'reason'       => 'required|string|max:1000',
            'date_flagged' => 'required|date',
            'status'       => 'required|in:Active,Cleared',
            'cleared_date' => 'nullable|date',
            'cleared_by'   => 'nullable|string|max:255',
        ]);

        ClearanceHold::create($request->all());

        return redirect()->route('clearance_holds.index')
            ->with('success', 'Clearance hold added successfully.');
    }

    /**
     * Update an existing clearance hold.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,student_id',
            'department'   => 'required|string|max:255',
            'reason'       => 'required|string|max:1000',
            'date_flagged' => 'required|date',
            'status'       => 'required|in:Active,Cleared',
            'cleared_date' => 'nullable|date',
            'cleared_by'   => 'nullable|string|max:255',
        ]);

        $hold = ClearanceHold::findOrFail($id);
        $hold->update($request->all());

        return redirect()->route('clearance_holds.index')
            ->with('success', 'Clearance hold updated successfully.');
    }

    /**
     * Delete a clearance hold.
     */
    public function destroy($id)
    {
        $hold = ClearanceHold::findOrFail($id);
        $hold->delete();

        return redirect()->route('clearance_holds.index')
            ->with('success', 'Clearance hold deleted successfully.');
    }

    /**
     * Flag a student with a clearance hold.
     */
    public function flag(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'department' => 'required|string|max:255',
            'reason'     => 'required|string|max:1000',
        ]);

        ClearanceHold::create([
            'student_id'   => $request->student_id,
            'department'   => $request->department,
            'reason'       => $request->reason,
            'date_flagged' => now(),
            'status'       => 'Active',
        ]);

        return redirect()->route('clearance_holds.index')
            ->with('success', 'Clearance hold flagged successfully.');
    }

    /**
     * Lift a clearance hold.
     */
    public function lift($id)
    {
        $hold = ClearanceHold::findOrFail($id);

        $hold->update([
            'status'       => 'Cleared',
            'cleared_date' => now(),
            'cleared_by'   => auth()->user()->name ?? 'System Admin',
        ]);

        return redirect()->route('clearance_holds.index')
            ->with('success', 'Clearance hold lifted successfully.');
    }
}