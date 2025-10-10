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

        $nextId = ReformationProgram::count() + 1;
        $program_id = 'RP' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        ReformationProgram::create([
            'program_id' => $program_id,
            'program_name' => $request->program_name,
            'description' => $request->description,
            'duration' => $request->duration,
            'responsible_office' => $request->responsible_office,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return redirect()->route('reformation_programs.index')->with('success', 'Reformation program added successfully.');
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