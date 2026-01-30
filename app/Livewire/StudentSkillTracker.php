<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\SkillsApiService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class StudentSkillTracker extends Component
{
    public $userId;
    public $learnedSkills = [];
    public $relatedSkills = [];
    public $loading = false;

    public function mount($userId = null)
    {
        $this->userId = $userId ?? auth()->id();
        $this->loadSkills();
    }

    public function loadSkills()
    {
        $this->loading = true;

        try {
            $user = User::with(['enrolledCourses.skills'])->find($this->userId);
            
            if ($user) {
                // Get all unique skills from enrolled courses
                $skills = collect();
                foreach ($user->enrolledCourses as $course) {
                    foreach ($course->skills as $skill) {
                        $skills->push($skill->name);
                    }
                }
                
                $this->learnedSkills = $skills->unique()->values()->toArray();
                
                // Get related skills for the first learned skill (if any)
                if (!empty($this->learnedSkills)) {
                    $this->loadRelatedSkills($this->learnedSkills[0]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load student skills', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function loadRelatedSkills($skillName)
    {
        try {
            $service = app(SkillsApiService::class);
            $result = $service->getRelatedSkills($skillName, 5);

            if ($result['success']) {
                $this->relatedSkills = collect($result['data'])
                    ->pluck('name')
                    ->filter(function($skill) {
                        return !in_array($skill, $this->learnedSkills);
                    })
                    ->take(5)
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error('Failed to load related skills', [
                'error' => $e->getMessage(),
                'skill' => $skillName,
            ]);
            $this->relatedSkills = [];
        }
    }

    public function render()
    {
        return view('livewire.student-skill-tracker');
    }
}
