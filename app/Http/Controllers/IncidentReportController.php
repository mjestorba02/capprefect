<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Student;
use App\Models\Sanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IncidentReportController extends Controller
{
    public function index()
    {
        $reports = IncidentReport::with(['student', 'category'])->paginate(10);
        $students = Student::all();
        $categories = Sanction::all();

        // Auto-generate next incident ID (e.g., IR001, IR002)
        $latest = IncidentReport::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;

        return view('incident_reports.index', compact('reports', 'students', 'categories', 'nextId'));
    }

    public function create()
    {
        $students = Student::all();
        $sanctions = Sanction::all();
        return view('incident_reports.create', compact('students', 'sanctions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'category_id' => 'required|exists:sanctions,sanction_id',
            'incident_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'reported_by' => 'required|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Generate next Incident ID (like IR001)
        $lastReport = IncidentReport::orderBy('id', 'desc')->first();
        $nextIdNum = $lastReport ? ((int) filter_var($lastReport->incident_id, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
        $incidentId = 'IR' . str_pad($nextIdNum, 3, '0', STR_PAD_LEFT);

        $incident = IncidentReport::create([
            'incident_id'  => $incidentId,
            'student_id'   => $request->student_id,
            'category_id'  => $request->category_id,
            'incident_date'=> $request->incident_date,
            'location'     => $request->location,
            'description'  => $request->description,
            'reported_by'  => $request->reported_by,
            'action_taken' => $request->action_taken,
            'status'       => $request->status,
        ]);

        return redirect()->route('incident_reports.index')
            ->with('success', 'Incident report created successfully.');
    }

    public function show(IncidentReport $incident_report)
    {
        return view('incident_reports.show', compact('incident_report'));
    }

    public function edit(IncidentReport $incident_report)
    {
        $students = Student::all();
        $sanctions = Sanction::all();
        return view('incident_reports.edit', compact('incident_report', 'students', 'sanctions'));
    }

    public function update(Request $request, IncidentReport $incident_report)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|exists:students,student_id',
            'category_id' => 'sometimes|exists:sanctions,sanction_id',
            'incident_date' => 'sometimes|date',
            'location' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'reported_by' => 'sometimes|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:255',
        ]);

        $incident_report->update($validated);

        return redirect()->route('incident_reports.index')->with('success', 'Incident report updated successfully.');
    }

    public function destroy(IncidentReport $incident_report)
    {
        $incident_report->delete();
        return redirect()->route('incident_reports.index')->with('success', 'Incident report deleted successfully.');
    }
}