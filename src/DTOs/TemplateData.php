<?php

namespace Justino\PageBuilder\DTOs;

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
        public ?string $updatedAt = null
    ) {
        $this->createdAt = $createdAt ?? now()->toISOString();
        $this->updatedAt = $updatedAt ?? now()->toISOString();
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
            updatedAt: $data['updated_at'] ?? null
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
            'updated_at' => $this->updatedAt
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}