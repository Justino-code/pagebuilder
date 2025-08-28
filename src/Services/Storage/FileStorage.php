<?php

namespace Justino\PageBuilder\Services\Storage;

use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\DTOs\{PageData, TemplateData, PageVersion};
use Justino\PageBuilder\Events\{PageCreated, PageUpdated, PagePublished, PageUnpublished, PageDeleted};
use Justino\PageBuilder\Exceptions\{PageValidationException, PageNotFoundException, StorageException};
use Illuminate\Support\Facades\{File, Cache, Event, Storage, DB};
use Illuminate\Support\Str;

class FileStorage extends BaseStorage
{
    protected $storagePath;
    protected $versionsPath;
    protected $backupPath;

    public function __construct()
    {
        $this->storagePath = config('pagebuilder.storage.path', storage_path('app/pagebuilder'));
        $this->versionsPath = $this->storagePath . '/versions';
        $this->backupPath = $this->storagePath . '/backups';
        $this->maxRevisions = config('pagebuilder.storage.max_revisions', 10);
        $this->cacheEnabled = config('pagebuilder.cache.enabled', true);
        $this->cacheDuration = config('pagebuilder.cache.duration', 3600);
        
        $this->ensureDirectoriesExist();
    }

    /**
     * Garante que os diretórios de armazenamento existam
     */
    protected function ensureDirectoriesExist(): void
    {
        foreach ([$this->storagePath, $this->versionsPath, $this->backupPath] as $path) {
            if (!File::exists($path)) {
                if (!File::makeDirectory($path, 0755, true)) {
                    throw new StorageException('create_directory', $path, 
                        "Não foi possível criar o diretório: {$path}");
                }
            }
        }
    }

    public function savePage(PageData $pageData, bool $publish = false, ?string $userId = null): bool
    {
        return $this->transaction(function() use ($pageData, $publish, $userId) {
            $this->validatePageData($pageData);
            
            $isNew = !$this->pageExists($pageData->slug);
            $pageData->setModifiedBy($userId ?? 'system');
            
            if (!$publish) {
                $result = $this->saveDraft($pageData);
                
                if ($result) {
                    $event = $isNew ? new PageCreated($pageData) : new PageUpdated($pageData);
                    Event::dispatch($event);
                }
                
                return $result;
            }
            
            $this->createVersion($pageData->slug, 'published', $userId, 'Publicação inicial');
            $result = $this->savePublished($pageData);
            
            if ($result) {
                $this->deleteDraft($pageData->slug);
                
                $event = $isNew ? new PageCreated($pageData) : new PageUpdated($pageData);
                Event::dispatch($event);
                Event::dispatch(new PagePublished($pageData));
            }
            
            return $result;
        });
    }

    public function loadPage(string $slug, string $type = null): ?PageData
    {
        $cacheKey = $this->getCacheKey($slug);
        
        // Tentar recuperar do cache primeiro
        if ($cached = $this->cacheGet($cacheKey)) {
            if (!$type || $cached['type'] === $type) {
                return PageData::fromArray($cached);
            }
        }
        
        $filePath = $this->getPublishedPath($slug);
        
        // Se não encontrar publicado, procurar rascunho
        if (!File::exists($filePath)) {
            $filePath = $this->getDraftPath($slug);
            if (!File::exists($filePath)) {
                return null;
            }
        }
        
        try {
            $content = File::get($filePath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new StorageException('parse_json', $filePath,
                    "Erro ao decodificar JSON do arquivo: " . json_last_error_msg());
            }
            
            if ($type && ($data['type'] ?? 'page') !== $type) {
                return null;
            }
            
            $pageData = PageData::fromArray($data);
            
            // Armazenar em cache
            $this->cachePut($cacheKey, $pageData->toArray());
            
            return $pageData;
        } catch (\Exception $e) {
            throw new StorageException('read_file', $filePath, 
                "Erro ao ler arquivo: " . $e->getMessage());
        }
    }

    public function listPages(string $type = 'page'): array
    {
        $items = [];
        $files = File::files($this->storagePath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'json' && !Str::contains($file->getFilename(), '.draft.')) {
                try {
                    $content = File::get($file->getPathname());
                    $data = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && (!$type || ($data['type'] ?? 'page') === $type)) {
                        $items[] = PageData::fromArray($data);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        // Ordenar por data de atualização (mais recente primeiro)
        usort($items, function($a, $b) {
            return strtotime($b->updatedAt) - strtotime($a->updatedAt);
        });
        
        return $items;
    }

    public function deletePage(string $slug): bool
    {
        return $this->transaction(function() use ($slug) {
            $pageData = $this->loadPage($slug);
            
            $deleted = true;
            $draftPath = $this->getDraftPath($slug);
            $publishedPath = $this->getPublishedPath($slug);
            
            if (File::exists($draftPath)) {
                $deleted = $deleted && File::delete($draftPath);
            }
            
            if (File::exists($publishedPath)) {
                $deleted = $deleted && File::delete($publishedPath);
            }
            
            if ($deleted && $pageData) {
                Event::dispatch(new PageDeleted($pageData));
                $this->clearCache($slug);
                
                $this->createBackup($slug, 'delete');
                $this->deleteVersions($slug);
            }
            
            return $deleted;
        });
    }

    public function publishPage(string $slug, ?string $userId = null): bool
    {
        $draftPath = $this->getDraftPath($slug);
        
        if (!File::exists($draftPath)) {
            throw new PageNotFoundException($slug, 'page', "Rascunho não encontrado para publicação");
        }
        
        return $this->transaction(function() use ($slug, $userId, $draftPath) {
            $content = File::get($draftPath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new StorageException('parse_json', $draftPath,
                    "Erro ao decodificar JSON do rascunho: " . json_last_error_msg());
            }
            
            $pageData = PageData::fromArray($data);
            $pageData->markAsPublished();
            $pageData->setModifiedBy($userId ?? 'system');
            
            $this->createVersion($slug, 'published', $userId, 'Publicação');
            $result = $this->savePublished($pageData);
            
            if ($result) {
                $this->deleteDraft($slug);
                Event::dispatch(new PagePublished($pageData));
                $this->clearCache($slug);
            }
            
            return $result;
        });
    }

    public function unpublishPage(string $slug, ?string $userId = null): bool
    {
        $publishedPath = $this->getPublishedPath($slug);
        
        if (!File::exists($publishedPath)) {
            throw new PageNotFoundException($slug, 'page', "Página publicada não encontrada");
        }
        
        return $this->transaction(function() use ($slug, $userId, $publishedPath) {
            $content = File::get($publishedPath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new StorageException('parse_json', $publishedPath,
                    "Erro ao decodificar JSON publicado: " . json_last_error_msg());
            }
            
            $pageData = PageData::fromArray($data);
            $pageData->markAsDraft();
            $pageData->setModifiedBy($userId ?? 'system');
            
            $this->createVersion($slug, 'draft', $userId, 'Despublicação');
            $result = $this->saveDraft($pageData);
            
            if ($result) {
                File::delete($publishedPath);
                Event::dispatch(new PageUnpublished($pageData));
                $this->clearCache($slug);
            }
            
            return $result;
        });
    }

    public function createVersion(string $slug, string $type = 'revision', ?string $userId = null, ?string $note = null): bool
    {
        $pageData = $this->loadPage($slug);
        
        if (!$pageData) {
            throw new PageNotFoundException($slug, 'page');
        }
        
        $versionId = $this->generateVersionId();
        $version = new PageVersion(
            versionId: $versionId,
            slug: $slug,
            data: $pageData->toArray(),
            createdBy: $userId ?? 'system',
            createdAt: now()->toISOString(),
            type: $type,
            note: $note,
            versionNumber: $pageData->version
        );
        
        $versionPath = $this->versionsPath . '/' . $slug . '_' . $versionId . '.json';
        
        try {
            $result = File::put($versionPath, $version->toJson());
            
            if ($result) {
                $this->cleanupOldRevisions($slug);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new StorageException('create_version', $versionPath,
                "Erro ao criar versão: " . $e->getMessage());
        }
    }

    public function listVersions(string $slug): array
    {
        $pattern = $this->versionsPath . '/' . $slug . '_*.json';
        $files = glob($pattern);
        $versions = [];
        
        foreach ($files as $file) {
            try {
                $content = File::get($file);
                $data = json_decode($content, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    $versions[] = PageVersion::fromArray($data);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        
        usort($versions, function($a, $b) {
            return strtotime($b->createdAt) - strtotime($a->createdAt);
        });
        
        return $versions;
    }

    public function restoreVersion(string $slug, string $versionId, ?string $userId = null): bool
    {
        $versionPath = $this->versionsPath . '/' . $slug . '_' . $versionId . '.json';
        
        if (!File::exists($versionPath)) {
            throw new PageNotFoundException($versionId, 'version');
        }
        
        try {
            $content = File::get($versionPath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new StorageException('parse_json', $versionPath,
                    "Erro ao decodificar versão: " . json_last_error_msg());
            }
            
            $version = PageVersion::fromArray($data);
            $pageData = PageData::fromArray($version->data);
            $pageData->setModifiedBy($userId ?? 'system');
            $pageData->updateVersion();
            
            $currentPageData = $this->loadPage($slug);
            if ($currentPageData) {
                $this->createVersion($slug, 'revision', $userId, "Antes de restaurar versão {$versionId}");
            }
            
            $result = $this->saveDraft($pageData);
            
            if ($result) {
                $this->createVersion($slug, 'revision', $userId, "Restauração da versão {$versionId}");
                $this->clearCache($slug);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            throw new StorageException('restore_version', $versionPath,
                "Erro ao restaurar versão: " . $e->getMessage());
        }
    }

    public function saveTemplate(TemplateData $templateData, ?string $userId = null): bool
    {
        // Implementação similar a savePage mas para templates
        $templateData->setModifiedBy($userId ?? 'system');
        
        $templatePath = $this->getPublishedPath($templateData->slug);
        
        try {
            $result = File::put($templatePath, $templateData->toJson());
            
            if ($result) {
                $this->clearCache($templateData->slug);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new StorageException('save_template', $templatePath,
                "Erro ao salvar template: " . $e->getMessage());
        }
    }

    public function loadTemplate(string $slug): ?TemplateData
    {
        $filePath = $this->getPublishedPath($slug);
        
        if (!File::exists($filePath)) {
            return null;
        }
        
        try {
            $content = File::get($filePath);
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new StorageException('parse_json', $filePath,
                    "Erro ao decodificar JSON do template: " . json_last_error_msg());
            }
            
            return TemplateData::fromArray($data);
        } catch (\Exception $e) {
            throw new StorageException('read_file', $filePath, 
                "Erro ao ler template: " . $e->getMessage());
        }
    }

    public function listTemplates(string $type = 'template'): array
    {
        $items = [];
        $files = File::files($this->storagePath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                try {
                    $content = File::get($file->getPathname());
                    $data = json_decode($content, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && 
                        ($data['type'] ?? 'page') === $type) {
                        $items[] = TemplateData::fromArray($data);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        
        usort($items, function($a, $b) {
            return strtotime($b->updatedAt) - strtotime($a->updatedAt);
        });
        
        return $items;
    }

    public function setDefaultTemplate(string $slug, string $type): bool
    {
        return $this->transaction(function() use ($slug, $type) {
            // Primeiro, remover default de todos os templates do mesmo tipo
            $templates = $this->listTemplates($type);
            foreach ($templates as $template) {
                if ($template->isDefault && $template->slug !== $slug) {
                    $template->removeAsDefault();
                    $this->saveTemplate($template);
                }
            }
            
            // Agora definir o novo template como default
            $template = $this->loadTemplate($slug);
            if ($template) {
                $template->markAsDefault();
                return $this->saveTemplate($template);
            }
            
            return false;
        });
    }

    public function getStorageStats(): array
    {
        $pages = count(glob($this->storagePath . '/*.json'));
        $drafts = count(glob($this->storagePath . '/*.draft.json'));
        $versions = count(glob($this->versionsPath . '/*.json'));
        $backups = count(glob($this->backupPath . '/*.json'));
        
        $totalSize = 0;
        
        foreach (glob($this->storagePath . '/*.json') as $file) {
            $totalSize += filesize($file);
        }
        
        foreach (glob($this->versionsPath . '/*.json') as $file) {
            $totalSize += filesize($file);
        }
        
        foreach (glob($this->backupPath . '/*.json') as $file) {
            $totalSize += filesize($file);
        }
        
        return [
            'pages' => $pages - $drafts,
            'drafts' => $drafts,
            'versions' => $versions,
            'backups' => $backups,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize)
        ];
    }

    public function backup(string $reason = 'manual'): bool
    {
        $backupData = [
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'pages' => array_map(fn($p) => $p->toArray(), $this->listPages()),
            'templates' => array_map(fn($t) => $t->toArray(), $this->listTemplates())
        ];
        
        $backupPath = $this->backupPath . '/full_backup_' . now()->format('Y-m-d_His') . '.json';
        
        try {
            return File::put($backupPath, json_encode($backupData, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function pageExists(string $slug): bool
    {
        return File::exists($this->getPublishedPath($slug)) || 
               File::exists($this->getDraftPath($slug));
    }

    public function templateExists(string $slug): bool
    {
        return File::exists($this->getPublishedPath($slug));
    }

    protected function isSlugInUse(string $slug, string $currentSlug = null): bool
    {
        $existing = $this->loadPage($slug);
        return $existing && $existing->slug !== $currentSlug;
    }

    /**
     * Salva uma página como rascunho
     */
    protected function saveDraft(PageData $pageData): bool
    {
        $draftPath = $this->getDraftPath($pageData->slug);
        
        try {
            $result = File::put($draftPath, $pageData->toJson());
            
            if ($result) {
                $this->clearCache($pageData->slug);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new StorageException('save_draft', $draftPath,
                "Erro ao salvar rascunho: " . $e->getMessage());
        }
    }

    /**
     * Salva uma página como publicada
     */
    protected function savePublished(PageData $pageData): bool
    {
        $publishedPath = $this->getPublishedPath($pageData->slug);
        
        try {
            $result = File::put($publishedPath, $pageData->toJson());
            
            if ($result) {
                $this->clearCache($pageData->slug);
            }
            
            return $result;
        } catch (\Exception $e) {
            throw new StorageException('save_published', $publishedPath,
                "Erro ao salvar página publicada: " . $e->getMessage());
        }
    }

    /**
     * Deleta um rascunho
     */
    protected function deleteDraft(string $slug): bool
    {
        $draftPath = $this->getDraftPath($slug);
        
        if (File::exists($draftPath)) {
            return File::delete($draftPath);
        }
        
        return true;
    }

    /**
     * Limpa revisões antigas
     */
    protected function cleanupOldRevisions(string $slug): void
    {
        $pattern = $this->versionsPath . '/' . $slug . '_*.json';
        $files = glob($pattern);
        
        if (count($files) > $this->maxRevisions) {
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            $filesToDelete = array_slice($files, 0, count($files) - $this->maxRevisions);
            foreach ($filesToDelete as $file) {
                try {
                    File::delete($file);
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    /**
     * Deleta todas as versões de uma página
     */
    protected function deleteVersions(string $slug): void
    {
        $pattern = $this->versionsPath . '/' . $slug . '_*.json';
        $files = glob($pattern);
        
        foreach ($files as $file) {
            try {
                File::delete($file);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Cria um backup de uma página específica
     */
    protected function createBackup(string $slug, string $reason): bool
    {
        $backupData = [
            'slug' => $slug,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'published' => $this->loadPage($slug)?->toArray(),
            'versions' => array_map(fn($v) => $v->toArray(), $this->listVersions($slug))
        ];
        
        $backupPath = $this->backupPath . '/' . $slug . '_' . now()->format('Y-m-d_His') . '.json';
        
        try {
            return File::put($backupPath, json_encode($backupData, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retorna o caminho do arquivo de rascunho
     */
    protected function getDraftPath(string $slug): string
    {
        return $this->storagePath . '/' . $slug . '.draft.json';
    }

    /**
     * Retorna o caminho do arquivo publicado
     */
    protected function getPublishedPath(string $slug): string
    {
        return $this->storagePath . '/' . $slug . '.json';
    }
}