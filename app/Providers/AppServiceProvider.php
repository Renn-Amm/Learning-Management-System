<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Message;
use App\Models\Skill;
use App\Policies\CategoryPolicy;
use App\Policies\CoursePolicy;
use App\Policies\LessonPolicy;
use App\Policies\MessagePolicy;
use App\Policies\SkillPolicy;
use App\View\Composers\MessageNotificationComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        Lesson::class => LessonPolicy::class,
        Category::class => CategoryPolicy::class,
        Message::class => MessagePolicy::class,
        Skill::class => SkillPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Register authorization policies for models.
     */
    public function boot(): void
    {
        // Register all policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Register view composer for message notifications
        View::composer('layouts.navigation', MessageNotificationComposer::class);
    }
}
