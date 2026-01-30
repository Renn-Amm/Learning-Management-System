<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDaily extends Command
{
    protected $signature = 'backup:daily';
    protected $description = 'Perform daily backup of database and files (10-day retention)';

    public function handle(BackupService $backupService)
    {
        $this->info('Starting daily backup...');

        $result = $backupService->performBackup();

        if ($result['success']) {
            $this->info('Backup completed successfully!');
            $this->info('Backup file: ' . ($result['file'] ?? 'N/A'));
            
            if (isset($result['database_size'])) {
                $this->info('Database size: ' . $this->formatBytes($result['database_size']));
            }
            
            if (isset($result['files_size'])) {
                $this->info('Files size: ' . $this->formatBytes($result['files_size']));
            }
            
            $this->info('Old backups cleaned up (30-day retention)');
            
            return Command::SUCCESS;
        } else {
            $this->error('Backup failed: ' . ($result['message'] ?? 'Unknown error'));
            return Command::FAILURE;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
