<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Backup database and files to remote storage';

    public function handle(BackupService $backupService): int
    {
        $this->info('Starting backup process...');

        $result = $backupService->performBackup();

        if ($result['success']) {
            $this->info($result['message']);
            $this->info('Backup file: ' . ($result['file'] ?? 'N/A'));
            return Command::SUCCESS;
        }

        $this->error($result['message']);
        return Command::FAILURE;
    }
}
