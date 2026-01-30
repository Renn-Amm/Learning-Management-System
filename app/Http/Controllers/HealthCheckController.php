<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    public function index()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $allHealthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            
            return $value === 'test' 
                ? ['status' => 'ok', 'message' => 'Cache is working']
                : ['status' => 'error', 'message' => 'Cache read/write failed'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache system error'];
        }
    }

    private function checkStorage(): array
    {
        try {
            $publicDiskExists = Storage::disk('public')->exists('');
            $privateDiskExists = Storage::disk('private')->exists('');
            
            return ($publicDiskExists || $privateDiskExists)
                ? ['status' => 'ok', 'message' => 'Storage is accessible']
                : ['status' => 'error', 'message' => 'Storage not accessible'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage system error'];
        }
    }
}
