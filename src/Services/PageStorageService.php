<?php
// app/PageBuilder/Services/PageStorageService.php

namespace Justino\PageBuilder\Services;

use Illuminate\Support\Facades\{File, Event, Cache, DB};
use Illuminate\Support\Str;
use Justino\PageBuilder\DTOs\{PageData, PageVersion};
use Justino\PageBuilder\Events\{PageCreated, PageUpdated, PagePublished, PageDeleted};
use Justino\PageBuilder\Exceptions\PageValidationException;

class PageStorageService
{
    protected $storagePath;
    protected $versionsPath;
    protected $maxRevisions = 10;
    
    public function __construct()
    {
        $this->storagePath = config('pagebuilder.storage.path', storage_path('app/pagebuilder'));
        $this->versionsPath = $this->storagePath . '/versions';
        
        $this->ensureDirectoriesExist();
    }
    
    /**
     * Garante que os diretórios de armazenamento existam
     */
    protected function ensureDirectoriesExist(): void
    {
        foreach ([$this->storagePath, $this->versionsPath] as $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }
    }
    
    /**
     * Encontra uma página pelo slug
     */
    public function find(string $slug, string $type = null): ?PageData
    {
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        if (!File::exists($filePath)) {
            return null;
        }
        
        try {
            $data = json_decode(File::get($filePath), true);
            
            if ($type && ($data['type'] ?? 'page') !== $type) {
                return null;
            }
            
            return PageData::fromArray($data);
        } catch (\Exception $e) {
            logger()->error('Erro ao carregar dados da página', ['slug' => $slug, 'error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Salva uma página (como rascunho ou publicado)
     */
    public function savePage(PageData $pageData, bool $publish = false, string $userId = null): bool
    {
        $this->validatePageData($pageData);
        
        $isNew = !$this->find($pageData->slug);
        
        // Salva como rascunho
        $result = File::put(
            $this->getDraftPath($pageData->slug),
            $pageData->toJson()
        );
        
        if ($result && $publish) {
            $this->publish($pageData->slug, $userId);
        }
        
        if ($result) {
            $event = $isNew ? new PageCreated($pageData) : new PageUpdated($pageData);
            Event::dispatch($event);
            
            $this->clearCache($pageData->slug);
        }
        
        return $result;
    }
    
    /**
     * Publica uma página
     */
    public function publish(string $slug, string $userId = null): bool
    {
        $draftPath = $this->getDraftPath($slug);
        $publishedPath = $this->getPublishedPath($slug);
        
        if (!File::exists($draftPath)) {
            throw new \Exception("Rascunho não encontrado para o slug: {$slug}");
        }
        
        // Cria backup de versão antes de publicar
        $this->createVersion($slug, 'published', $userId);
        
        // Move rascunho para publicado
        $result = File::move($draftPath, $publishedPath);
        
        if ($result) {
            Event::dispatch(new PagePublished($this->find($slug)));
            $this->clearCache($slug);
        }
        
        return $result;
    }
    
    /**
     * Remove uma página do estado publicado
     */
    public function unpublish(string $slug): bool
    {
        $publishedPath = $this->getPublishedPath($slug);
        
        if (File::exists($publishedPath)) {
            // Cria versão antes de despublicar
            $this->createVersion($slug, 'draft');
            
            return File::delete($publishedPath);
        }
        
        return false;
    }
    
    /**
     * Cria uma versão da página
     */
    public function createVersion(string $slug, string $type = 'revision', string $userId = null, string $note = null): bool
    {
        $pageData = $this->find($slug);
        
        if (!$pageData) {
            return false;
        }
        
        $versionId = Str::uuid();
        $version = new PageVersion(
            versionId: $versionId,
            slug: $slug,
            data: $pageData->toArray(),
            createdBy: $userId ?? 'system',
            createdAt: now()->toISOString(),
            type: $type,
            note: $note
        );
        
        $versionPath = $this->versionsPath . '/' . $slug . '_' . $versionId . '.json';
        
        // Limpa revisões antigas se necessário
        $this->cleanupOldRevisions($slug);
        
        return File::put($versionPath, json_encode($version, JSON_PRETTY_PRINT));
    }
    
    /**
     * Limpa revisões antigas mantendo apenas as mais recentes
     */
    protected function cleanupOldRevisions(string $slug): void
    {
        $pattern = $this->versionsPath . '/' . $slug . '_*.json';
        $files = glob($pattern);
        
        if (count($files) > $this->maxRevisions) {
            // Ordena por data de modificação (mais antigos primeiro)
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // Remove os arquivos mais antigos
            $filesToDelete = array_slice($files, 0, count($files) - $this->maxRevisions);
            foreach ($filesToDelete as $file) {
                File::delete($file);
            }
        }
    }
    
    /**
     * Valida os dados da página antes de salvar
     */
    protected function validatePageData(PageData $pageData): void
    {
        $validator = validator($pageData->toArray(), [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'alpha_dash',
                'max:100',
                function ($attribute, $value, $fail) use ($pageData) {
                    // Verifica se o slug já existe para outra página
                    $existing = $this->find($value);
                    if ($existing && $existing->slug !== $pageData->slug) {
                        $fail('O slug já está em uso por outra página.');
                    }
                },
            ],
            'type' => 'required|in:page,template',
        ]);
        
        if ($validator->fails()) {
            throw new PageValidationException($validator->errors()->first());
        }
    }
    
    /**
     * Limpa o cache da página
     */
    protected function clearCache(string $slug): void
    {
        Cache::forget("pagebuilder.page.{$slug}");
        Cache::forget("pagebuilder.styles.{$slug}");
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