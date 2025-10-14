<?php

namespace App\Http\Controllers;

use App\Models\Sanction;
use App\Models\Student;
use App\Models\ViolationCategory;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    public function index() {
        $sanctions = Sanction::with('student')->paginate(10);
        $students = Student::all();
        $violationCategories = ViolationCategory::where('status', 'active')->get();
        return view('sanctions.index', compact('sanctions', 'students', 'violationCategories'));
    }

    public function store(Request $request) {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'offense' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'sanction_type' => 'required|string|max:255',
            'severity' => 'required|string|max:50',
            'status' => 'required|string|max:50'
        ]);

        Sanction::create($request->all());
        return redirect()->route('sanctions.index')->with('success', 'Sanction added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'offense' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'sanction_type' => 'required|string|max:255',
            'severity' => 'required|string|max:50',
            'status' => 'required|string|max:50',
        ]);

        $sanction = Sanction::findOrFail($id);
        $sanction->update($request->all());

        return redirect()->route('sanctions.index')->with('success', 'Sanction updated successfully.');
    }

    public function destroy($id) {
        Sanction::findOrFail($id)->delete();
        return redirect()->route('sanctions.index')->with('success', 'Sanction deleted successfully.');
    }
}