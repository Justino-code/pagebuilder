<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\PageBuilderService;
use Illuminate\Support\Facades\Storage;

class StorageStatsCommand extends Command
{
    protected $signature = 'pagebuilder:storage-stats 
                            {--json : Output as JSON}
                            {--details : Show detailed information}';
    
    protected $description = 'Display storage statistics for Page Builder';
    
    public function handle(PageBuilderService $pageBuilderService)
    {
        try {
            $stats = $pageBuilderService->getStorageStats();
            
            if ($this->option('json')) {
                $this->output->write(json_encode($stats, JSON_PRETTY_PRINT));
                return 0;
            }
            
            $this->displayStatsTable($stats);
            
            if ($this->option('details')) {
                $this->displayDetailedInfo($pageBuilderService);
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error retrieving storage stats: " . $e->getMessage());
            return 1;
        }
    }
    
    protected function displayStatsTable(array $stats): void
    {
        $this->info('📊 Page Builder Storage Statistics');
        $this->line('');
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Published Pages', $stats['pages']],
                ['Draft Pages', $stats['drafts']],
                ['Versions', $stats['versions']],
                ['Backups', $stats['backups']],
                ['Total Size', $stats['total_size_formatted']],
            ]
        );
    }
    
    protected function displayDetailedInfo(PageBuilderService $pageBuilderService): void
    {
        $this->info('📄 Page Details');
        
        $pages = $pageBuilderService->listPages();
        $pageData = [];
        
        foreach ($pages as $page) {
            $pageData[] = [
                $page->slug,
                $page->title,
                $page->published ? '✅' : '📝',
                $page->type,
                $this->formatDate($page->updatedAt),
            ];
        }
        
        if (!empty($pageData)) {
            $this->table(
                ['Slug', 'Title', 'Status', 'Type', 'Last Updated'],
                $pageData
            );
        } else {
            $this->line('No pages found.');
        }
        
        // Templates info
        $this->info('🎨 Template Details');
        
        $templates = $pageBuilderService->listTemplates();
        $templateData = [];
        
        foreach ($templates as $template) {
            $templateData[] = [
                $template->slug,
                $template->name,
                $template->type,
                $template->isDefault ? '✅' : '',
                $this->formatDate($template->updatedAt),
            ];
        }
        
        if (!empty($templateData)) {
            $this->table(
                ['Slug', 'Name', 'Type', 'Default', 'Last Updated'],
                $templateData
            );
        } else {
            $this->line('No templates found.');
        }
    }
    
    protected function formatDate(?string $date): string
    {
        if (!$date) return 'N/A';
        
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d H:i');
        } catch (\Exception $e) {
            return $date;
        }
    }
}