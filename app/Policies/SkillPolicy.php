<?php

namespace App\Policies;

use App\Models\Skill;
use App\Models\User;

class SkillPolicy
{
    /**
     * Determine if the user can view any skills.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher(); // Only teachers manage skills
    }

    /**
     * Determine if the user can view the skill.
     */
    public function view(User $user, Skill $skill): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can create skills.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can update the skill.
     * All teachers can update skills (shared resource).
     */
    public function update(User $user, Skill $skill): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can delete the skill.
     * All teachers can delete unused skills.
     */
    public function delete(User $user, Skill $skill): bool
    {
        return $user->isTeacher() && $skill->courses()->count() === 0;
    }
}
