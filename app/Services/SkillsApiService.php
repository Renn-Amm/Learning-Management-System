<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Skills API Service
 * 
 * Integrates with skills metadata API to provide:
 * - Skill information and descriptions
 * - Related skills suggestions
 * - Industry-relevant skills data
 */
class SkillsApiService
{
    private string $apiKey;
    private string $baseUrl;
    private bool $enabled;
    private int $cacheDuration;

    public function __construct()
    {
        $this->apiKey = config('services.skills_api.key', '');
        $this->baseUrl = config('services.skills_api.url', 'https://emsiservices.com/skills');
        $this->enabled = config('services.skills_api.enabled', true);
        $this->cacheDuration = config('services.skills_api.cache_duration', 86400);
    }

    /**
     * Get skill information by name
     */
    public function getSkillInfo(string $skillName): array
    {
        if (!$this->enabled || empty($this->apiKey)) {
            return $this->fallbackResponse();
        }

        $cacheKey = 'skills_api_info_' . md5($skillName);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($skillName) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                ->get("{$this->baseUrl}/versions/latest/skills", [
                    'q' => $skillName,
                    'limit' => 5,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'data' => $this->sanitizeSkills($data['data'] ?? []),
                    ];
                }

                Log::warning('Skills API returned non-200 status', [
                    'status' => $response->status(),
                    'skill' => $skillName,
                ]);

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('Skills API request failed', [
                    'error' => $e->getMessage(),
                    'skill' => $skillName,
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    /**
     * Get related skills for a given skill
     */
    public function getRelatedSkills(string $skillName, int $limit = 5): array
    {
        if (!$this->enabled || empty($this->apiKey)) {
            return $this->fallbackResponse();
        }

        $cacheKey = 'skills_api_related_' . md5($skillName . $limit);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($skillName, $limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                ->get("{$this->baseUrl}/versions/latest/related", [
                    'skill' => $skillName,
                    'limit' => min($limit, 20),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'data' => $this->sanitizeSkills($data['data'] ?? []),
                    ];
                }

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('Skills API related skills request failed', [
                    'error' => $e->getMessage(),
                    'skill' => $skillName,
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    /**
     * Search skills by query
     */
    public function searchSkills(string $query, int $limit = 10): array
    {
        if (!$this->enabled || empty($this->apiKey)) {
            return $this->fallbackResponse();
        }

        $cacheKey = 'skills_api_search_' . md5($query . $limit);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($query, $limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                ->get("{$this->baseUrl}/versions/latest/skills", [
                    'q' => $query,
                    'limit' => min($limit, 50),
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'data' => $this->sanitizeSkills($data['data'] ?? []),
                        'count' => count($data['data'] ?? []),
                    ];
                }

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('Skills API search failed', [
                    'error' => $e->getMessage(),
                    'query' => $query,
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    /**
     * Sanitize skills data
     */
    private function sanitizeSkills(array $skills): array
    {
        return array_map(function ($skill) {
            return [
                'name' => $skill['name'] ?? 'Unknown Skill',
                'description' => $skill['description'] ?? '',
                'type' => $skill['type']['name'] ?? 'General',
                'category' => $skill['category']['name'] ?? 'Uncategorized',
            ];
        }, $skills);
    }

    /**
     * Fallback response when API is unavailable
     */
    private function fallbackResponse(): array
    {
        return [
            'success' => false,
            'data' => [],
            'message' => 'Skills data is currently unavailable.',
        ];
    }
}
