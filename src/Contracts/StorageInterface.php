<?php

namespace Justino\PageBuilder\Contracts;

use Justino\PageBuilder\DTOs\PageData;
use Justino\PageBuilder\DTOs\TemplateData;

interface StorageInterface
{
    /**
     * Salva uma página
     */
    public function savePage(PageData $pageData, bool $publish = false, ?string $userId = null): bool;

    /**
     * Carrega uma página pelo slug
     */
    public function loadPage(string $slug, string $type = null): ?PageData;

    /**
     * Carrega todas as páginas de um tipo específico
     */
    public function listPages(string $type = 'page'): array;

    /**
     * Deleta uma página
     */
    public function deletePage(string $slug): bool;

    /**
     * Publica uma página
     */
    public function publishPage(string $slug, ?string $userId = null): bool;

    /**
     * Despublica uma página
     */
    public function unpublishPage(string $slug, ?string $userId = null): bool;

    /**
     * Cria uma versão da página
     */
    public function createVersion(string $slug, string $type = 'revision', ?string $userId = null, ?string $note = null): bool;

    /**
     * Lista versões de uma página
     */
    public function listVersions(string $slug): array;

    /**
     * Restaura uma versão específica
     */
    public function restoreVersion(string $slug, string $versionId, ?string $userId = null): bool;

    /**
     * Salva um template
     */
    public function saveTemplate(TemplateData $templateData, ?string $userId = null): bool;

    /**
     * Carrega um template pelo slug
     */
    public function loadTemplate(string $slug): ?TemplateData;

    /**
     * Carrega todos os templates de um tipo específico
     */
    public function listTemplates(string $type = 'template'): array;

    /**
     * Define um template como padrão
     */
    public function setDefaultTemplate(string $slug, string $type): bool;

    /**
     * Obtém estatísticas de armazenamento
     */
    public function getStorageStats(): array;

    /**
     * Limpa o cache de uma página específica
     */
    public function clearCache(string $slug): void;

    /**
     * Realiza backup do armazenamento
     */
    public function backup(string $reason = 'manual'): bool;

    /**
     * Verifica se uma página existe
     */
    public function pageExists(string $slug): bool;

    /**
     * Verifica se um template existe
     */
    public function templateExists(string $slug): bool;
}