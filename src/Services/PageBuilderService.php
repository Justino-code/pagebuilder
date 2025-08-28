<?php

namespace Justino\PageBuilder\Services;

use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\DTOs\PageData;
use Justino\PageBuilder\DTOs\TemplateData;
use Justino\PageBuilder\Exceptions\PageValidationException;
use Justino\PageBuilder\Services\BlockManager;
use Illuminate\Support\Facades\Log;

class PageBuilderService
{
    protected $storage;
    protected $blockManager;
    
    public function __construct(StorageInterface $storage, BlockManager $blockManager)
    {
        $this->storage = $storage;
        $this->blockManager = $blockManager;
    }
    
    /**
     * Cria uma nova página
     */
    public function createPage(array $data, ?string $userId = null, bool $publish = false): PageData
    {
        $pageData = PageData::fromArray($data);
        
        if ($this->storage->savePage($pageData, $publish, $userId)) {
            Log::info('Página criada', ['slug' => $pageData->slug, 'user_id' => $userId]);
            return $pageData;
        }
        
        throw new \RuntimeException('Falha ao criar página');
    }
    
    /**
     * Atualiza uma página existente
     */
    public function updatePage(string $slug, array $data, ?string $userId = null, bool $publish = null): PageData
    {
        $existingPage = $this->storage->loadPage($slug);
        
        if (!$existingPage) {
            throw new PageValidationException("Página não encontrada: {$slug}");
        }
        
        // Mesclar dados mantendo valores existentes para campos não fornecidos
        $updatedData = array_merge($existingPage->toArray(), $data);
        $pageData = PageData::fromArray($updatedData);
        
        // Determinar se deve publicar (mantém o estado atual se não especificado)
        $shouldPublish = $publish ?? $existingPage->published;
        
        if ($this->storage->savePage($pageData, $shouldPublish, $userId)) {
            Log::info('Página atualizada', ['slug' => $slug, 'user_id' => $userId]);
            return $pageData;
        }
        
        throw new \RuntimeException('Falha ao atualizar página');
    }
    
    /**
     * Publica uma página
     */
    public function publishPage(string $slug, ?string $userId = null): bool
    {
        $result = $this->storage->publishPage($slug, $userId);
        
        if ($result) {
            Log::info('Página publicada', ['slug' => $slug, 'user_id' => $userId]);
        }
        
        return $result;
    }
    
    /**
     * Despublica uma página
     */
    public function unpublishPage(string $slug, ?string $userId = null): bool
    {
        $result = $this->storage->unpublishPage($slug, $userId);
        
        if ($result) {
            Log::info('Página despublicada', ['slug' => $slug, 'user_id' => $userId]);
        }
        
        return $result;
    }
    
    /**
     * Obtém uma página pelo slug
     */
    public function getPage(string $slug): ?PageData
    {
        return $this->storage->loadPage($slug);
    }
    
    /**
     * Lista todas as páginas de um tipo específico
     */
    public function listPages(string $type = 'page'): array
    {
        return $this->storage->listPages($type);
    }
    
    /**
     * Deleta uma página
     */
    public function deletePage(string $slug, ?string $userId = null): bool
    {
        $result = $this->storage->deletePage($slug);
        
        if ($result) {
            Log::info('Página deletada', ['slug' => $slug, 'user_id' => $userId]);
        }
        
        return $result;
    }
    
    /**
     * Cria um template
     */
    public function createTemplate(array $data, ?string $userId = null): TemplateData
    {
        $templateData = TemplateData::fromArray($data);
        
        if ($this->storage->saveTemplate($templateData, $userId)) {
            Log::info('Template criado', ['slug' => $templateData->slug, 'user_id' => $userId]);
            return $templateData;
        }
        
        throw new \RuntimeException('Falha ao criar template');
    }
    
    /**
     * Obtém um template pelo slug
     */
    public function getTemplate(string $slug): ?TemplateData
    {
        return $this->storage->loadTemplate($slug);
    }
    
    /**
     * Lista todos os templates de um tipo específico
     */
    public function listTemplates(string $type = 'template'): array
    {
        return $this->storage->listTemplates($type);
    }
    
    /**
     * Define um template como padrão
     */
    public function setDefaultTemplate(string $slug, string $type): bool
    {
        return $this->storage->setDefaultTemplate($slug, $type);
    }
    
    /**
     * Cria uma versão da página
     */
    public function createVersion(string $slug, string $type = 'revision', ?string $userId = null, ?string $note = null): bool
    {
        return $this->storage->createVersion($slug, $type, $userId, $note);
    }
    
    /**
     * Lista versões de uma página
     */
    public function listVersions(string $slug): array
    {
        return $this->storage->listVersions($slug);
    }
    
    /**
     * Restaura uma versão específica
     */
    public function restoreVersion(string $slug, string $versionId, ?string $userId = null): bool
    {
        return $this->storage->restoreVersion($slug, $versionId, $userId);
    }
    
    /**
     * Obtém estatísticas de armazenamento
     */
    public function getStorageStats(): array
    {
        return $this->storage->getStorageStats();
    }
    
    /**
     * Realiza backup do armazenamento
     */
    public function backup(string $reason = 'manual'): bool
    {
        return $this->storage->backup($reason);
    }
    
    /**
     * Verifica se uma página existe
     */
    public function pageExists(string $slug): bool
    {
        return $this->storage->pageExists($slug);
    }
    
    /**
     * Verifica se um template existe
     */
    public function templateExists(string $slug): bool
    {
        return $this->storage->templateExists($slug);
    }
    
    /**
     * Limpa o cache de uma página
     */
    public function clearCache(string $slug): void
    {
        $this->storage->clearCache($slug);
    }
    
    /**
     * Obtém o gerenciador de blocos
     */
    public function getBlockManager(): BlockManager
    {
        return $this->blockManager;
    }
    
    /**
     * Valida conteúdo de blocos
     */
    public function validateBlockContent(array $content): array
    {
        $validated = [];
        
        foreach ($content as $block) {
            if (!isset($block['type'])) {
                continue;
            }
            
            $blockClass = $this->blockManager->getBlockClass($block['type']);
            
            if ($blockClass) {
                // Validar dados do bloco conforme schema
                $validated[] = $this->validateBlockData($block, $blockClass::schema());
            }
        }
        
        return $validated;
    }
    
    /**
     * Valida dados de um bloco específico
     */
    protected function validateBlockData(array $blockData, array $schema): array
    {
        $validated = ['type' => $blockData['type']];
        
        if (isset($blockData['data'])) {
            $validated['data'] = $this->validateBlockFields($blockData['data'], $schema);
        }
        
        if (isset($blockData['styles'])) {
            $validated['styles'] = $blockData['styles'];
        }
        
        return $validated;
    }
    
    /**
     * Valida campos individuais do bloco
     */
    protected function validateBlockFields(array $data, array $schema): array
    {
        $validated = [];
        
        foreach ($schema as $fieldName => $fieldConfig) {
            $value = $data[$fieldName] ?? ($fieldConfig['default'] ?? null);
            
            // Validar conforme tipo do campo
            switch ($fieldConfig['type'] ?? 'text') {
                case 'repeater':
                    if (is_array($value)) {
                        $validated[$fieldName] = array_map(
                            fn($item) => $this->validateBlockFields($item, $fieldConfig['fields'] ?? []),
                            $value
                        );
                    }
                    break;
                    
                case 'number':
                    $validated[$fieldName] = is_numeric($value) ? (float)$value : ($fieldConfig['default'] ?? 0);
                    break;
                    
                case 'boolean':
                    $validated[$fieldName] = (bool)($value ?? ($fieldConfig['default'] ?? false));
                    break;
                    
                case 'select':
                    $options = $fieldConfig['options'] ?? [];
                    $validated[$fieldName] = in_array($value, $options) ? $value : ($fieldConfig['default'] ?? null);
                    break;
                    
                default: // text, textarea, color, etc.
                    $validated[$fieldName] = is_string($value) ? $value : ($fieldConfig['default'] ?? '');
                    break;
            }
        }
        
        return $validated;
    }
}