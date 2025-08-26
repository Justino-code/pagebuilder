<?php

namespace Justino\PageBuilder\DTOs;

class PageData
{
    public function __construct(
        public string $type = 'page',
        public string $title,
        public string $slug,
        public array $content = [],
        public bool $published = false,
        public bool $headerEnabled = true,
        public bool $footerEnabled = true,
        public string $customCss = '',
        public string $customJs = '',
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
        $this->createdAt = $createdAt ?? now()->toISOString();
        $this->updatedAt = $updatedAt ?? now()->toISOString();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? 'page',
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? '',
            content: $data['content'] ?? [],
            published: $data['published'] ?? false,
            headerEnabled: $data['header_enabled'] ?? true,
            footerEnabled: $data['footer_enabled'] ?? true,
            customCss: $data['custom_css'] ?? '',
            customJs: $data['custom_js'] ?? '',
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
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
            'updated_at' => $this->updatedAt
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
}