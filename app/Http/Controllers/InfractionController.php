<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infraction;
use App\Models\Student;
use App\Models\ViolationCategory;

class InfractionController extends Controller
{
    /**
     * Display all infractions and related data.
     */
    public function index()
    {
        $infractions = Infraction::with('student')->latest()->paginate(10);
        $students = Student::all();
        $categories = ViolationCategory::where('status', 'Active')->get();

        return view('infractions.index', compact('infractions', 'students', 'categories'));
    }

    /**
     * Store a new infraction record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,student_id',
            'violation_category' => 'required|string|max:255',
            'severity'           => 'required|string|max:50',
            'reported_by'        => 'required|string|max:255',
            'sanction_assigned'  => 'nullable|string|max:255',
            'parent_notified'    => 'nullable|boolean',
            'status'             => 'required|string|max:50',
            'description'        => 'required|string',
        ]);

        Infraction::create([
            'student_id'         => $request->student_id,
            'violation_category' => $request->violation_category,
            'severity'           => $request->severity,
            'reported_by'        => $request->reported_by,
            'sanction_assigned'  => $request->sanction_assigned,
            'parent_notified'    => $request->parent_notified ?? false,
            'status'             => $request->status,
            'description'        => $request->description,
            'datetime'           => now(), // optional: set current time if not in form
        ]);

        return redirect()->route('infractions.index')
            ->with('success', 'Infraction logged successfully.');
    }

    /**
     * Update an existing infraction.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,student_id',
            'violation_category' => 'required|string|max:255',
            'severity'           => 'required|string|max:50',
            'reported_by'        => 'required|string|max:255',
            'sanction_assigned'  => 'nullable|string|max:255',
            'parent_notified'    => 'nullable|boolean',
            'status'             => 'required|string|max:50',
            'description'        => 'required|string',
        ]);

        $infraction = Infraction::findOrFail($id);
        $infraction->update([
            'student_id'         => $request->student_id,
            'violation_category' => $request->violation_category,
            'severity'           => $request->severity,
            'reported_by'        => $request->reported_by,
            'sanction_assigned'  => $request->sanction_assigned,
            'parent_notified'    => $request->parent_notified ?? false,
            'status'             => $request->status,
            'description'        => $request->description,
            'datetime'           => $request->datetime ?? $infraction->datetime,
        ]);

        return redirect()->route('infractions.index')
            ->with('success', 'Infraction updated successfully.');
    }

    /**
     * Delete an infraction.
     */
    public function destroy($id)
    {
        $infraction = Infraction::findOrFail($id);
        $infraction->delete();

        return redirect()->route('infractions.index')
            ->with('success', 'Infraction deleted successfully.');
    }
}