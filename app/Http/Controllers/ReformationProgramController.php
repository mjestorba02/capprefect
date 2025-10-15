<?php

namespace App\Http\Controllers;

use App\Models\ReformationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReformationProgramController extends Controller
{
    public function index()
    {
        $programs = ReformationProgram::latest()->paginate(10);
        $nextId = ReformationProgram::count() + 1;

        return view('reformation_programs.index', compact('programs', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|string|max:255',
            'responsible_office' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        // 🔹 Find the latest existing program_id (e.g., RP001, RP002, etc.)
        $lastProgram = \App\Models\ReformationProgram::orderBy('program_id', 'desc')->first();

        if ($lastProgram && preg_match('/RP(\d+)/', $lastProgram->program_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1; // Start from RP001 if no records exist
        }

        // 🔹 Generate new program_id safely
        $program_id = 'RP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // 🔹 Double-check if it already exists (in case of race condition)
        while (\App\Models\ReformationProgram::where('program_id', $program_id)->exists()) {
            $nextNumber++;
            $program_id = 'RP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        // 🔹 Create the record
        \App\Models\ReformationProgram::create([
            'program_id' => $program_id,
            'program_name' => $request->program_name,
            'description' => $request->description,
            'duration' => $request->duration,
            'responsible_office' => $request->responsible_office,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return redirect()->route('reformation_programs.index')
            ->with('success', 'Reformation program added successfully.');
    }

    public function update(Request $request, ReformationProgram $reformation_program)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|string|max:255',
            'responsible_office' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        $reformation_program->update($request->only([
            'program_name', 'description', 'duration',
            'responsible_office', 'type', 'status'
        ]));

        return redirect()->route('reformation_programs.index')->with('success', 'Reformation program updated successfully.');
    }

    public function destroy(ReformationProgram $reformation_program)
    {
        $reformation_program->delete();
        return redirect()->route('reformation_programs.index')->with('success', 'Reformation program deleted successfully.');
    }
}