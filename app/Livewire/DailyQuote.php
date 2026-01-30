<?php

namespace App\Livewire;

use App\Services\QuoteService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class DailyQuote extends Component
{
    public $quote = null;
    public $loading = false;

    public function mount()
    {
        $this->loadQuote();
    }

    public function loadQuote()
    {
        $this->loading = true;
        
        try {
            $service = app(QuoteService::class);
            $result = $service->getDailyQuote();
            
            if ($result['success']) {
                $this->quote = $result['quote'];
            }
            
        } catch (\Exception $e) {
            Log::error('DailyQuote component error', [
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->loading = false;
        }
    }

    public function refresh()
    {
        $this->loadQuote();
    }

    public function render()
    {
        return view('livewire.daily-quote');
    }
}
