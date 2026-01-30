<?php

namespace App\Livewire;

use App\Services\SkillsApiService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class SkillSuggestions extends Component
{
    public $searchQuery = '';
    public $suggestions = [];
    public $loading = false;
    public $error = null;

    protected $rules = [
        'searchQuery' => 'nullable|string|min:2|max:50',
    ];

    public function searchSkills()
    {
        if (empty($this->searchQuery) || strlen($this->searchQuery) < 2) {
            $this->suggestions = [];
            return;
        }

        $this->validate();
        $this->loading = true;
        $this->error = null;

        try {
            $service = app(SkillsApiService::class);
            $result = $service->searchSkills($this->searchQuery, 10);

            if ($result['success']) {
                $this->suggestions = $result['data'];
                $this->error = null;
            } else {
                $this->suggestions = [];
                $this->error = null; // Silent failure
            }
        } catch (\Exception $e) {
            Log::error('Skills API search failed in SkillSuggestions component', [
                'error' => $e->getMessage(),
                'query' => $this->searchQuery,
            ]);
            $this->suggestions = [];
            $this->error = null; // Silent failure
        } finally {
            $this->loading = false;
        }
    }

    public function addSkill($skillName)
    {
        $this->dispatch('skill-selected', skillName: $skillName);
    }

    public function render()
    {
        return view('livewire.skill-suggestions');
    }
}
