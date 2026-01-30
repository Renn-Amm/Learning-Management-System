<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QuoteService
{
    private string $baseUrl;
    private bool $enabled;
    private int $cacheDuration;

    public function __construct()
    {
        $this->baseUrl = config('services.zenquotes.url', 'https://zenquotes.io');
        $this->enabled = config('services.zenquotes.enabled', true);
        $this->cacheDuration = config('services.zenquotes.cache_duration', 86400);
    }

    public function getDailyQuote(): array
    {
        if (!$this->enabled) {
            return $this->fallbackResponse();
        }

        $cacheKey = 'zenquotes_daily_' . now()->format('Y-m-d');

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            try {
                $response = Http::timeout(5)
                    ->get("{$this->baseUrl}/api/quotes");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data) && is_array($data)) {
                        $quote = $data[0];
                        return [
                            'success' => true,
                            'quote' => $this->sanitizeQuote($quote),
                        ];
                    }
                }

                Log::warning('ZenQuotes API returned non-200 status or empty data', [
                    'status' => $response->status(),
                ]);

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('ZenQuotes API request failed', [
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    public function getRandomQuote(): array
    {
        if (!$this->enabled) {
            return $this->fallbackResponse();
        }

        $cacheKey = 'zenquotes_random_' . now()->format('Y-m-d-H');

        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = Http::timeout(5)
                    ->get("{$this->baseUrl}/api/random");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data) && is_array($data)) {
                        $quote = $data[0];
                        return [
                            'success' => true,
                            'quote' => $this->sanitizeQuote($quote),
                        ];
                    }
                }

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('ZenQuotes random API request failed', [
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    private function sanitizeQuote(array $quote): array
    {
        return [
            'text' => $quote['q'] ?? $quote['quote'] ?? '',
            'author' => $quote['a'] ?? $quote['author'] ?? 'Unknown',
        ];
    }

    private function fallbackResponse(): array
    {
        return [
            'success' => false,
            'quote' => null,
        ];
    }
}
