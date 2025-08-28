<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\PageBuilderService;
use Illuminate\Support\Facades\Storage;

class StorageBackupCommand extends Command
{
    protected $signature = 'pagebuilder:storage-backup 
                            {--full : Create a full backup including media}
                            {--reason= : Reason for the backup}
                            {--force : Force backup without confirmation}';
    
    protected $description = 'Create a backup of Page Builder storage';
    
    public function handle(PageBuilderService $pageBuilderService)
    {
        $reason = $this->option('reason') ?: ($this->option('full') ? 'full_backup' : 'manual_backup');
        
        if (!$this->option('force') && !$this->confirm('Do you wish to create a backup?', true)) {
            $this->info('Backup cancelled.');
            return 0;
        }
        
        $this->info('Creating backup...');
        
        try {
            $startTime = microtime(true);
            
            $result = $pageBuilderService->backup($reason);
            
            $elapsedTime = round(microtime(true) - $startTime, 2);
            
            if ($result) {
                $this->info("✅ Backup completed successfully in {$elapsedTime}s");
                
                if ($this->option('full')) {
                    $this->info('📦 Full backup created (including media files)');
                } else {
                    $this->info('📦 Data backup created');
                }
                
                // Show backup stats
                $stats = $pageBuilderService->getStorageStats();
                $this->line("Backups count: {$stats['backups']}");
                
                return 0;
            } else {
                $this->error('❌ Backup failed');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Backup error: " . $e->getMessage());
            return 1;
        }
    }
}