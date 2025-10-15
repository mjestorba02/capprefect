<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Student;
use App\Models\ViolationCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentReportController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::all();
        $categories = ViolationCategory::all();

        // Filters
        $categoryId = $request->input('category_id');
        $from = $request->input('from');
        $to = $request->input('to');

        // Query with filters
        $query = IncidentReport::with(['student', 'category']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($from && $to) {
            $query->whereBetween('incident_date', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('incident_date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('incident_date', '<=', $to);
        }

        $reports = $query->orderByDesc('incident_date')->paginate(10)->withQueryString();

        // Auto-generate next ID
        $latest = IncidentReport::latest('id')->first();
        $nextId = $latest ? $latest->id + 1 : 1;

        return view('incident_reports.index', compact('reports', 'students', 'categories', 'nextId', 'from', 'to', 'categoryId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'category_id' => 'required|exists:violation_categories,id',
            'incident_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reported_by' => 'required|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // Generate next Incident ID (like IR001)
        $lastReport = IncidentReport::orderBy('id', 'desc')->first();
        $nextIdNum = $lastReport ? ((int) filter_var($lastReport->incident_id, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;
        $incidentId = 'IR' . str_pad($nextIdNum, 3, '0', STR_PAD_LEFT);

        IncidentReport::create([
            'incident_id'   => $incidentId,
            'student_id'    => $request->student_id,
            'category_id'   => $request->category_id,
            'incident_date' => $request->incident_date,
            'location'      => $request->location,
            'description'   => $request->description,
            'reported_by'   => $request->reported_by,
            'action_taken'  => $request->action_taken,
            'status'        => $request->status,
        ]);

        return redirect()->route('incident_reports.index')
            ->with('success', 'Incident report created successfully.');
    }

    public function update(Request $request, IncidentReport $incident_report)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|exists:students,student_id',
            'category_id' => 'sometimes|exists:violation_categories,id',
            'incident_date' => 'sometimes|date',
            'location' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'reported_by' => 'sometimes|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:255',
        ]);

        $incident_report->update($validated);

        return redirect()->route('incident_reports.index')
            ->with('success', 'Incident report updated successfully.');
    }

    public function destroy(IncidentReport $incident_report)
    {
        $incident_report->delete();

        return redirect()->route('incident_reports.index')
            ->with('success', 'Incident report deleted successfully.');
    }

    public function export(Request $request)
    {
        $categoryId = $request->input('category_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $query = IncidentReport::with(['student', 'category']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($from && $to) {
            $query->whereBetween('incident_date', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('incident_date', '>=', $from);
        } elseif ($to) {
            $query->whereDate('incident_date', '<=', $to);
        }

        $reports = $query->orderBy('incident_date')->get();

        // ✅ DomPDF export
        $pdf = Pdf::loadView('incident_reports.export', compact('reports'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('incident_reports_' . now()->format('Ymd_His') . '.pdf');
    }
}