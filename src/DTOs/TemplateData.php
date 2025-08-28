<?php

namespace Justino\PageBuilder\DTOs;

use Illuminate\Support\Carbon;

class TemplateData
{
    public function __construct(
        public string $type,
        public string $name,
        public string $slug,
        public array $content = [],
        public array $styles = [],
        public bool $isDefault = false,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public string $theme = 'system',
        public string $version = '1.0.0',
        public ?string $lastModifiedBy = null
    ) {
        $this->createdAt = $createdAt ?? Carbon::now()->toISOString();
        $this->updatedAt = $updatedAt ?? Carbon::now()->toISOString();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            content: $data['content'] ?? [],
            styles: $data['styles'] ?? [],
            isDefault: $data['is_default'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            theme: $data['theme'] ?? 'system',
            version: $data['version'] ?? '1.0.0',
            lastModifiedBy: $data['last_modified_by'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'slug' => $this->slug,
            'content' => $this->content,
            'styles' => $this->styles,
            'is_default' => $this->isDefault,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'theme' => $this->theme,
            'version' => $this->version,
            'last_modified_by' => $this->lastModifiedBy
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    public function markAsDefault(): void
    {
        $this->isDefault = true;
        $this->updatedAt = Carbon::now()->toISOString();
    }
    
    public function removeAsDefault(): void
    {
        $this->isDefault = false;
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
}