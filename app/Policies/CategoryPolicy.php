<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine if the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher(); // Only teachers manage categories
    }

    /**
     * Determine if the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine if the user can update the category.
     * Only the teacher who created it can update, or if no owner (system category).
     */
    public function update(User $user, Category $category): bool
    {
        if (!$user->isTeacher()) {
            return false;
        }
        
        // System categories (no user_id) cannot be edited
        if (!$category->user_id) {
            return false;
        }
        
        return $category->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the category.
     * Only the teacher who created it can delete, and only if no courses exist.
     */
    public function delete(User $user, Category $category): bool
    {
        if (!$user->isTeacher()) {
            return false;
        }
        
        // System categories cannot be deleted
        if (!$category->user_id) {
            return false;
        }
        
        return $category->user_id === $user->id && $category->courses()->count() === 0;
    }
}
