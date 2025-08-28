<?php

namespace Justino\PageBuilder\Services\Storage;

use Justino\PageBuilder\Contracts\StorageInterface;
use Justino\PageBuilder\DTOs\{PageData, TemplateData, PageVersion};
use Justino\PageBuilder\Exceptions\{PageValidationException, PageNotFoundException, StorageException};
use Illuminate\Support\Facades\{Cache, Event, DB};
use Illuminate\Support\Str;

abstract class BaseStorage implements StorageInterface
{
    protected $maxRevisions = 10;
    protected $cacheEnabled = true;
    protected $cacheDuration = 3600;

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
                    if ($this->isSlugInUse($value, $pageData->slug)) {
                        $fail('O slug já está em uso por outra página.');
                    }
                    
                    if (preg_match('/[^a-z0-9_-]/i', $value)) {
                        $fail('O slug contém caracteres inválidos. Use apenas letras, números, hífens e underscores.');
                    }
                },
            ],
            'type' => 'required|in:page,template,header,footer',
            'theme' => 'required|in:system,light,dark'
        ], [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.alpha_dash' => 'O slug deve conter apenas letras, números, hífens e underscores.',
            'slug.max' => 'O slug não pode ter mais de 100 caracteres.',
        ]);
        
        if ($validator->fails()) {
            throw new PageValidationException(
                'Validação falhou para os dados da página.',
                $validator->errors()->all(),
                $pageData->toArray()
            );
        }
    }

    /**
     * Verifica se um slug está em uso
     */
    abstract protected function isSlugInUse(string $slug, string $currentSlug = null): bool;

    /**
     * Limpa o cache da página
     */
    public function clearCache(string $slug): void
    {
        Cache::forget("pagebuilder.page.{$slug}");
        Cache::forget("pagebuilder.styles.{$slug}");
        Cache::forget("pagebuilder.versions.{$slug}");
    }

    /**
     * Obtém a chave de cache para uma página
     */
    protected function getCacheKey(string $slug): string
    {
        return "pagebuilder.page.{$slug}";
    }

    /**
     * Armazena dados em cache
     */
    protected function cachePut(string $key, $data, int $duration = null): bool
    {
        if (!$this->cacheEnabled) {
            return false;
        }

        return Cache::put($key, $data, $duration ?? $this->cacheDuration);
    }

    /**
     * Recupera dados do cache
     */
    protected function cacheGet(string $key)
    {
        if (!$this->cacheEnabled) {
            return null;
        }

        return Cache::get($key);
    }

    /**
     * Formata bytes para formato legível
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Gera um ID único para versões
     */
    protected function generateVersionId(): string
    {
        return Str::uuid();
    }

    /**
     * Processa transação com tratamento de erro
     */
    protected function transaction(callable $callback)
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}