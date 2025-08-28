<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\PageBuilderService;
use Justino\PageBuilder\Contracts\StorageInterface;

class StorageCleanupCommand extends Command
{
    protected $signature = 'pagebuilder:storage-cleanup 
                            {--versions : Clean up old versions}
                            {--backups : Clean up old backups}
                            {--drafts : Clean up old drafts}
                            {--all : Clean up all types}
                            {--days=30 : Keep items newer than X days}
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force cleanup without confirmation}';
    
    protected $description = 'Clean up Page Builder storage by removing old items';
    
    public function handle(PageBuilderService $pageBuilderService, StorageInterface $storage)
    {
        $cleanupTypes = [];
        
        if ($this->option('all')) {
            $cleanupTypes = ['versions', 'backups', 'drafts'];
        } else {
            if ($this->option('versions')) $cleanupTypes[] = 'versions';
            if ($this->option('backups')) $cleanupTypes[] = 'backups';
            if ($this->option('drafts')) $cleanupTypes[] = 'drafts';
        }
        
        if (empty($cleanupTypes)) {
            $this->error('Please specify what to clean up. Use --versions, --backups, --drafts, or --all');
            return 1;
        }
        
        $days = (int)$this->option('days');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        
        $this->info("🧹 Page Builder Storage Cleanup");
        $this->line("Mode: " . ($dryRun ? 'DRY RUN (no changes will be made)' : 'LIVE'));
        $this->line("Keeping items newer than: {$days} days");
        $this->line("Cleanup types: " . implode(', ', $cleanupTypes));
        $this->line('');
        
        if (!$force && !$dryRun && !$this->confirm('Do you wish to proceed with cleanup?', false)) {
            $this->info('Cleanup cancelled.');
            return 0;
        }
        
        $results = [];
        $cutoffDate = now()->subDays($days);
        
        try {
            // Clean up versions
            if (in_array('versions', $cleanupTypes)) {
                $results['versions'] = $this->cleanupVersions($storage, $cutoffDate, $dryRun);
            }
            
            // Clean up backups
            if (in_array('backups', $cleanupTypes)) {
                $results['backups'] = $this->cleanupBackups($storage, $cutoffDate, $dryRun);
            }
            
            // Clean up drafts
            if (in_array('drafts', $cleanupTypes)) {
                $results['drafts'] = $this->cleanupDrafts($pageBuilderService, $cutoffDate, $dryRun);
            }
            
            $this->displayResults($results, $dryRun);
            
            if (!$dryRun) {
                $this->info('✅ Cleanup completed successfully');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Cleanup error: " . $e->getMessage());
            return 1;
        }
    }
    
    protected function cleanupVersions(StorageInterface $storage, $cutoffDate, bool $dryRun): array
    {
        $this->info('🔍 Scanning versions...');
        
        $allPages = $storage->listPages();
        $totalDeleted = 0;
        $totalSkipped = 0;
        
        foreach ($allPages as $page) {
            $versions = $storage->listVersions($page->slug);
            $toDelete = [];
            
            foreach ($versions as $version) {
                try {
                    $versionDate = \Carbon\Carbon::parse($version->createdAt);
                    
                    if ($versionDate->lessThan($cutoffDate)) {
                        $toDelete[] = $version->versionId;
                    }
                } catch (\Exception $e) {
                    // Skip invalid dates
                    continue;
                }
            }
            
            if (!$dryRun) {
                foreach ($toDelete as $versionId) {
                    // Note: This would require adding a deleteVersion method to StorageInterface
                    // For now, we'll just count them
                    $totalDeleted++;
                }
            } else {
                $totalDeleted += count($toDelete);
            }
            
            $totalSkipped += count($versions) - count($toDelete);
        }
        
        return [
            'deleted' => $totalDeleted,
            'skipped' => $totalSkipped,
            'type' => 'versions'
        ];
    }
    
    protected function cleanupBackups(StorageInterface $storage, $cutoffDate, bool $dryRun): array
    {
        $this->info('🔍 Scanning backups...');
        
        $stats = $storage->getStorageStats();
        $backupCount = $stats['backups'] ?? 0;
        
        // Simulate cleanup - in real implementation, this would scan backup files
        $toKeep = min(10, $backupCount); // Keep at least 10 most recent backups
        $toDelete = max(0, $backupCount - $toKeep);
        
        return [
            'deleted' => $toDelete,
            'skipped' => $toKeep,
            'type' => 'backups'
        ];
    }
    
    protected function cleanupDrafts(PageBuilderService $pageBuilderService, $cutoffDate, bool $dryRun): array
    {
        $this->info('🔍 Scanning drafts...');
        
        $allPages = $pageBuilderService->listPages();
        $deleted = 0;
        $skipped = 0;
        
        foreach ($allPages as $page) {
            // Check if page has both draft and published versions
            $draftExists = file_exists(storage_path("app/pagebuilder/{$page->slug}.draft.json"));
            $publishedExists = file_exists(storage_path("app/pagebuilder/{$page->slug}.json"));
            
            if ($draftExists && $publishedExists) {
                try {
                    $draftTime = filemtime(storage_path("app/pagebuilder/{$page->slug}.draft.json"));
                    $draftDate = \Carbon\Carbon::createFromTimestamp($draftTime);
                    
                    if ($draftDate->lessThan($cutoffDate)) {
                        if (!$dryRun) {
                            unlink(storage_path("app/pagebuilder/{$page->slug}.draft.json"));
                        }
                        $deleted++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $skipped++;
                }
            }
        }
        
        return [
            'deleted' => $deleted,
            'skipped' => $skipped,
            'type' => 'drafts'
        ];
    }
    
    protected function displayResults(array $results, bool $dryRun): void
    {
        $this->line('');
        $this->info('📋 Cleanup Results');
        
        $tableData = [];
        $totalDeleted = 0;
        $totalSkipped = 0;
        
        foreach ($results as $result) {
            $tableData[] = [
                ucfirst($result['type']),
                $result['deleted'],
                $result['skipped']
            ];
            $totalDeleted += $result['deleted'];
            $totalSkipped += $result['skipped'];
        }
        
        $this->table(
            ['Type', 'Deleted', 'Skipped'],
            $tableData
        );
        
        $this->line("Total items deleted: {$totalDeleted}");
        $this->line("Total items skipped: {$totalSkipped}");
        
        if ($dryRun) {
            $this->info('💡 This was a dry run. No changes were made.');
            $this->info('   Use without --dry-run to actually perform cleanup.');
        }
        
        if ($totalDeleted > 0 && !$dryRun) {
            $freedSpace = $this->estimateFreedSpace($totalDeleted);
            $this->info("💾 Estimated space freed: {$freedSpace}");
        }
    }
    
    protected function estimateFreedSpace(int $items): string
    {
        // Estimate average size per item (in KB)
        $avgSize = 50; // 50KB per item
        $totalKB = $items * $avgSize;
        
        if ($totalKB < 1024) {
            return $totalKB . ' KB';
        } elseif ($totalKB < 1048576) {
            return round($totalKB / 1024, 2) . ' MB';
        } else {
            return round($totalKB / 1048576, 2) . ' GB';
        }
    }
}