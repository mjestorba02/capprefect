<?php

namespace App\Http\Controllers;

use App\Models\ViolationCategory;
use Illuminate\Http\Request;

class ViolationCategoryController extends Controller
{
    /**
     * Display all violation categories.
     */
    public function index()
    {
        $categories = ViolationCategory::orderBy('id', 'asc')->paginate(10);
        return view('violation_categories.index', compact('categories'));
    }

    /**
     * Store a new violation category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|unique:violation_categories,category_id',
            'category_name' => 'required|string|max:255',
            'description' => 'required|string',
            'severity_level' => 'required|string',
            'default_sanction' => 'required|string',
            'status' => 'required|string',
        ]);

        ViolationCategory::create($validated);

        return redirect()->route('violation_categories.index')->with('success', 'Violation category added successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroy($id)
    {
        $category = ViolationCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('violation_categories.index')->with('success', 'Violation category deleted successfully.');
    }
}