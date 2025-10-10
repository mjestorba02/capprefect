<?php

namespace App\Http\Controllers;

use App\Models\Behavior;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BehaviorController extends Controller
{
    public function index()
    {
        $behaviors = Behavior::with('student')->paginate(10);
        $students = Student::all();
        return view('behaviors.index', compact('behaviors', 'students'));
    }

    public function create()
    {
        $students = Student::all();
        return view('behaviors.create', compact('students'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'date_recorded' => 'required|date',
            'behavior_type' => 'required|in:Positive,Negative',
            'behavior_category' => 'required|string|max:255',
            'description' => 'required|string',
            'recorded_by' => 'required|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'points' => 'required|integer',
            'status' => 'required|in:Active,Resolved',
        ]);

        // Auto-generate behavior_id
        $last = Behavior::latest('id')->first();
        $nextId = 'B' . str_pad(($last?->id ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        $behavior = Behavior::create(array_merge($request->all(), [
            'behavior_id' => $nextId,
        ]));

        return redirect()->route('behaviors.index')->with('success', 'Behavior recorded successfully.');
    }

    public function edit(Behavior $behavior)
    {
        return view('behaviors.edit', compact('behavior'));
    }

    public function update(Request $request, Behavior $behavior)
    {
        $request->validate([
            'date_recorded' => 'sometimes|date',
            'behavior_type' => 'sometimes|in:Positive,Negative',
            'behavior_category' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'recorded_by' => 'sometimes|string|max:255',
            'action_taken' => 'nullable|string|max:255',
            'points' => 'sometimes|integer',
            'status' => 'sometimes|in:Active,Resolved',
        ]);

        $behavior->update($request->all());

        return redirect()->route('behaviors.index')->with('success', 'Behavior updated successfully.');
    }

    public function destroy(Behavior $behavior)
    {
        $behavior->delete();
        return redirect()->route('behaviors.index')->with('success', 'Behavior deleted.');
    }

    public function resolve(Behavior $behavior)
    {
        $behavior->update(['status' => 'Resolved']);
        return redirect()->route('behaviors.index')->with('success', 'Behavior marked as resolved.');
    }
}