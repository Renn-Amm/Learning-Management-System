<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of skills with their courses count.
     * Eager load courses relationship to prevent N+1 queries.
     */
    public function index()
    {
        // Authorization: Only teachers can view skills
        $this->authorize('viewAny', Skill::class);

        // Eager load courses count to prevent N+1 queries
        $skills = Skill::withCount('courses')->latest()->paginate(20);
        return view('skills.index', compact('skills'));
    }

    public function create()
    {
        // Authorization: Only teachers can create skills
        $this->authorize('create', Skill::class);

        return view('skills.create');
    }

    public function store(Request $request)
    {
        // Authorization: Only teachers can create skills
        $this->authorize('create', Skill::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills',
        ]);

        Skill::create($validated);

        return redirect()->route('skills.index')->with('success', 'Skill created successfully.');
    }

    /**
     * Display the specified skill with its courses.
     * Eager load courses relationship to prevent N+1 queries.
     */
    public function show(Skill $skill)
    {
        // Authorization: Only teachers can view skills
        $this->authorize('view', $skill);

        // Eager load courses with their teachers to prevent N+1 queries
        $skill->load(['courses.teacher', 'courses.category']);
        
        return view('skills.show', compact('skill'));
    }

    public function edit(Skill $skill)
    {
        // Authorization: Only teachers can edit skills
        $this->authorize('update', $skill);

        return view('skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        // Authorization: Only teachers can update skills
        $this->authorize('update', $skill);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $skill->id,
        ]);

        $skill->update($validated);

        return redirect()->route('skills.index')->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        // Authorization: Only teachers can delete unused skills
        $this->authorize('delete', $skill);

        if ($skill->courses()->count() > 0) {
            return redirect()->route('skills.index')->with('error', 'Cannot delete skill that is used by courses.');
        }

        $skill->delete();
        return redirect()->route('skills.index')->with('success', 'Skill deleted successfully.');
    }
}
