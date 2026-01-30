<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupService
{
    private string $backupDisk;
    private bool $enabled;

    public function __construct()
    {
        $this->backupDisk = config('backup.disk', 's3');
        $this->enabled = config('backup.enabled', true);
    }

    public function performBackup(): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Backup is disabled in configuration',
            ];
        }

        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupPath = storage_path("app/backups/backup_{$timestamp}");

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $databaseBackup = $this->backupDatabase($timestamp);
            $filesBackup = $this->backupFiles($timestamp);

            $zipFile = $this->createZipArchive($timestamp, $databaseBackup, $filesBackup);

            if ($this->backupDisk !== 'local') {
                $this->uploadToRemoteStorage($zipFile, $timestamp);
            }

            $this->cleanupOldBackups();

            Log::info('Backup completed successfully', [
                'timestamp' => $timestamp,
                'disk' => $this->backupDisk,
            ]);

            return [
                'success' => true,
                'message' => 'Backup completed successfully',
                'file' => $zipFile,
                'timestamp' => $timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
            ];
        }
    }

    private function backupDatabase(string $timestamp): string
    {
        $dbPath = database_path('database.sqlite');
        $backupDbPath = storage_path("app/backups/database_{$timestamp}.sqlite");

        if (file_exists($dbPath)) {
            copy($dbPath, $backupDbPath);
        }

        return $backupDbPath;
    }

    private function backupFiles(string $timestamp): string
    {
        $sourcePath = storage_path('app/public');
        $backupPath = storage_path("app/backups/files_{$timestamp}");

        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        if (file_exists($sourcePath)) {
            $this->recursiveCopy($sourcePath, $backupPath);
        }

        return $backupPath;
    }

    private function recursiveCopy(string $source, string $dest): void
    {
        if (!file_exists($dest)) {
            mkdir($dest, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($source . '/' . $file)) {
                    $this->recursiveCopy($source . '/' . $file, $dest . '/' . $file);
                } else {
                    copy($source . '/' . $file, $dest . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function createZipArchive(string $timestamp, string $databaseBackup, string $filesBackup): string
    {
        $zipFile = storage_path("app/backups/backup_{$timestamp}.zip");
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
            if (file_exists($databaseBackup)) {
                $zip->addFile($databaseBackup, 'database.sqlite');
            }

            if (file_exists($filesBackup)) {
                $this->addDirectoryToZip($zip, $filesBackup, 'files');
            }

            $zip->close();
        }

        if (file_exists($databaseBackup)) {
            unlink($databaseBackup);
        }
        if (file_exists($filesBackup)) {
            $this->deleteDirectory($filesBackup);
        }

        return $zipFile;
    }

    private function addDirectoryToZip(ZipArchive $zip, string $directory, string $localPath = ''): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $localPath . '/' . substr($filePath, strlen($directory) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    private function uploadToRemoteStorage(string $zipFile, string $timestamp): void
    {
        $fileName = "backup_{$timestamp}.zip";
        $fileContents = file_get_contents($zipFile);

        Storage::disk($this->backupDisk)->put("backups/{$fileName}", $fileContents);

        Log::info('Backup uploaded to remote storage', [
            'disk' => $this->backupDisk,
            'file' => $fileName,
        ]);
    }

    private function cleanupOldBackups(): void
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_*.zip');

        // Keep backups for 10 days
        $retentionDays = 10;
        $cutoffTime = now()->subDays($retentionDays)->timestamp;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                Log::info('Deleted old backup', ['file' => basename($file)]);
            }
        }

        // Also cleanup old backups from remote storage if configured
        if ($this->backupDisk !== 'local') {
            $this->cleanupRemoteBackups($retentionDays);
        }
    }

    private function cleanupRemoteBackups(int $retentionDays): void
    {
        try {
            $files = Storage::disk($this->backupDisk)->files('backups');
            $cutoffTime = now()->subDays($retentionDays)->timestamp;

            foreach ($files as $file) {
                $lastModified = Storage::disk($this->backupDisk)->lastModified($file);
                
                if ($lastModified < $cutoffTime) {
                    Storage::disk($this->backupDisk)->delete($file);
                    Log::info('Deleted old remote backup', ['file' => $file]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to cleanup remote backups', ['error' => $e->getMessage()]);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
