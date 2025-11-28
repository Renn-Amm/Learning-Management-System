<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use AuthorizesRequests;
    
    public function index()
    {
        $this->authorize('viewAny', Skill::class);

        $skills = Skill::withCount('courses')->latest()->paginate(20);
        return view('skills.index', compact('skills'));
    }

    public function create()
    {
        $this->authorize('create', Skill::class);

        return view('skills.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Skill::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills',
        ]);

        Skill::create($validated);

        return redirect()->route('skills.index')->with('success', 'Skill created successfully.');
    }

    public function show(Skill $skill)
    {
        $this->authorize('view', $skill);

        $skill->load(['courses.teacher', 'courses.category']);
        
        return view('skills.show', compact('skill'));
    }

    public function edit(Skill $skill)
    {
        $this->authorize('update', $skill);

        return view('skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $this->authorize('update', $skill);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $skill->id,
        ]);

        $skill->update($validated);

        return redirect()->route('skills.index')->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $this->authorize('delete', $skill);

        if ($skill->courses()->count() > 0) {
            return redirect()->route('skills.index')->with('error', 'Cannot delete skill that is used by courses.');
        }

        $skill->delete();
        return redirect()->route('skills.index')->with('success', 'Skill deleted successfully.');
    }
}
