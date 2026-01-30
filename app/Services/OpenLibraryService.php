<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenLibraryService
{
    private string $baseUrl;
    private bool $enabled;
    private int $cacheDuration;

    private const CATEGORY_SUBJECT_MAP = [
        'programming' => 'computer_science',
        'math' => 'mathematics',
        'business' => 'business',
        'design' => 'design',
        'science' => 'science',
        'history' => 'history',
        'literature' => 'literature',
        'art' => 'art',
    ];

    public function __construct()
    {
        $this->baseUrl = config('services.openlibrary.url', 'https://openlibrary.org');
        $this->enabled = config('services.openlibrary.enabled', true);
        $this->cacheDuration = config('services.openlibrary.cache_duration', 3600);
    }

    public function getSubjectWorks(string $subject, int $limit = 10, bool $details = false): array
    {
        if (!$this->enabled) {
            return $this->fallbackResponse();
        }

        $cacheKey = "openlibrary_subject_" . md5($subject . $limit . ($details ? 'details' : ''));

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($subject, $limit, $details) {
            try {
                $params = ['limit' => min($limit, 50)];
                if ($details) {
                    $params['details'] = 'true';
                }

                $response = Http::timeout(10)
                    ->get("{$this->baseUrl}/subjects/{$subject}.json", $params);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'data' => $this->sanitizeWorks($data['works'] ?? []),
                        'count' => count($data['works'] ?? []),
                        'subject_name' => $data['name'] ?? $subject,
                    ];
                }

                Log::warning('OpenLibrary subjects API returned non-200 status', [
                    'status' => $response->status(),
                    'subject' => $subject,
                ]);

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('OpenLibrary subjects API request failed', [
                    'error' => $e->getMessage(),
                    'subject' => $subject,
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    public function searchBooks(string $query, int $limit = 10, int $offset = 0): array
    {
        if (!$this->enabled) {
            return $this->fallbackResponse();
        }

        $sanitizedQuery = $this->sanitizeSearchQuery($query);
        $cacheKey = "openlibrary_search_" . md5($sanitizedQuery . $limit . $offset);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($sanitizedQuery, $limit, $offset) {
            try {
                $response = Http::timeout(10)
                    ->get("{$this->baseUrl}/search.json", [
                        'q' => $sanitizedQuery,
                        'limit' => min($limit, 100),
                        'offset' => $offset,
                        'fields' => 'key,title,author_name,first_publish_year,cover_i',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'data' => $this->sanitizeSearchResults($data['docs'] ?? []),
                        'count' => $data['numFound'] ?? 0,
                    ];
                }

                Log::warning('OpenLibrary search API returned non-200 status', [
                    'status' => $response->status(),
                    'query' => $sanitizedQuery,
                ]);

                return $this->fallbackResponse();
            } catch (\Exception $e) {
                Log::error('OpenLibrary search API request failed', [
                    'error' => $e->getMessage(),
                    'query' => $sanitizedQuery,
                ]);

                return $this->fallbackResponse();
            }
        });
    }

    public function getResourcesByCategory(string $categoryName, int $limit = 5): array
    {
        $subject = $this->mapCategoryToSubject($categoryName);
        return $this->getSubjectWorks($subject, $limit);
    }

    public function getResourcesBySkill(string $skillName, int $limit = 5): array
    {
        return $this->searchBooks($skillName, $limit);
    }

    private function mapCategoryToSubject(string $categoryName): string
    {
        $normalized = strtolower(trim($categoryName));
        return self::CATEGORY_SUBJECT_MAP[$normalized] ?? $normalized;
    }

    private function sanitizeSearchQuery(string $query): string
    {
        return trim(strip_tags($query));
    }

    private function sanitizeSearchResults(array $results): array
    {
        return array_map(function ($result) {
            return [
                'title' => $result['title'] ?? 'Unknown Title',
                'author_name' => is_array($result['author_name'] ?? null) 
                    ? $result['author_name'] 
                    : ['Unknown Author'],
                'first_publish_year' => $result['first_publish_year'] ?? null,
                'cover_id' => $result['cover_i'] ?? null,
            ];
        }, $results);
    }

    private function sanitizeWorks(array $works): array
    {
        return array_map(function ($work) {
            $authors = [];
            if (isset($work['authors']) && is_array($work['authors'])) {
                $authors = array_map(fn($author) => $author['name'] ?? 'Unknown', $work['authors']);
            }
            
            return [
                'title' => $work['title'] ?? 'Unknown Title',
                'authors' => !empty($authors) ? $authors : ['Unknown Author'],
                'first_publish_year' => $work['first_publish_year'] ?? null,
                'cover_id' => $work['cover_id'] ?? null,
            ];
        }, $works);
    }

    private function fallbackResponse(): array
    {
        return [
            'success' => false,
            'data' => [],
            'count' => 0,
            'message' => 'External book service is currently unavailable. Please try again later.',
        ];
    }
}
