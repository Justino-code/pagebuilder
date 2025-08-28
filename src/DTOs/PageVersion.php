<?php

namespace Justino\PageBuilder\DTOs;

use Illuminate\Support\Carbon;

class PageVersion
{
    public function __construct(
        public string $versionId,
        public string $slug,
        public array $data,
        public string $createdBy,
        public string $createdAt,
        public string $type = 'draft',
        public ?string $note = null,
        public string $versionNumber = '1.0.0'
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            versionId: $data['version_id'] ?? uniqid(),
            slug: $data['slug'],
            data: $data['data'],
            createdBy: $data['created_by'] ?? 'system',
            createdAt: $data['created_at'] ?? Carbon::now()->toISOString(),
            type: $data['type'] ?? 'draft',
            note: $data['note'] ?? null,
            versionNumber: $data['version_number'] ?? '1.0.0'
        );
    }
    
    public function toArray(): array
    {
        return [
            'version_id' => $this->versionId,
            'slug' => $this->slug,
            'data' => $this->data,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'type' => $this->type,
            'note' => $this->note,
            'version_number' => $this->versionNumber
        ];
    }
    
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    public function getDisplayType(): string
    {
        $types = [
            'draft' => 'Rascunho',
            'published' => 'Publicado',
            'revision' => 'Revisão'
        ];
        
        return $types[$this->type] ?? $this->type;
    }
    
    public function getFormattedDate(): string
    {
        return Carbon::parse($this->createdAt)->format('d/m/Y H:i:s');
    }
}