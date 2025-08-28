<?php

namespace Justino\PageBuilder\DTOs;

use Illuminate\Support\Carbon;

class PageData
{
    public function __construct(
        public string $title,
        public string $slug,
        public array $content = [],
        public bool $published = false,
        public bool $headerEnabled = true,
        public bool $footerEnabled = true,
        public string $customCss = '',
        public string $customJs = '',
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public string $type = 'page',
        public string $theme = 'system',
        public array $styles = [],
        public string $version = '1.0.0',
        public ?string $publishedAt = null,
        public ?string $lastModifiedBy = null
    ) {
        $this->createdAt = $createdAt ?? Carbon::now()->toISOString();
        $this->updatedAt = $updatedAt ?? Carbon::now()->toISOString();
        
        if ($published && !$publishedAt) {
            $this->publishedAt = Carbon::now()->toISOString();
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? '',
            content: $data['content'] ?? [],
            published: $data['published'] ?? false,
            headerEnabled: $data['header_enabled'] ?? true,
            footerEnabled: $data['footer_enabled'] ?? true,
            customCss: $data['custom_css'] ?? '',
            customJs: $data['custom_js'] ?? '',
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            type: $data['type'] ?? 'page',
            theme: $data['theme'] ?? 'system',
            styles: $data['styles'] ?? [],
            version: $data['version'] ?? '1.0.0',
            publishedAt: $data['published_at'] ?? null,
            lastModifiedBy: $data['last_modified_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'published' => $this->published,
            'header_enabled' => $this->headerEnabled,
            'footer_enabled' => $this->footerEnabled,
            'custom_css' => $this->customCss,
            'custom_js' => $this->customJs,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'theme' => $this->theme,
            'styles' => $this->styles,
            'version' => $this->version,
            'published_at' => $this->publishedAt,
            'last_modified_by' => $this->lastModifiedBy
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    public function markAsDraft(): void
    {
        $this->published = false;
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function markAsPublished(): void
    {
        $this->published = true;
        $this->publishedAt = $this->publishedAt ?? Carbon::now()->toISOString();
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function updateVersion(): void
    {
        $versionParts = explode('.', $this->version);
        $patch = (int)($versionParts[2] ?? 0) + 1;
        $this->version = "{$versionParts[0]}.{$versionParts[1]}.{$patch}";
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function setModifiedBy(string $userId): void
    {
        $this->lastModifiedBy = $userId;
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function applyTheme(string $theme): void
    {
        $this->theme = $theme;
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function addStyles(array $styles): void
    {
        $this->styles = array_merge($this->styles, $styles);
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function getThemeDisplayName(): string
    {
        $themes = [
            'system' => 'Sistema',
            'light' => 'Claro',
            'dark' => 'Escuro'
        ];
        
        return $themes[$this->theme] ?? $this->theme;
    }
}