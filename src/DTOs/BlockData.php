<?php

namespace Justino\PageBuilder\DTOs;

class BlockData
{
    public function __construct(
        public string $type,
        public array $data = [],
        public array $styles = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            data: $data['data'] ?? [],
            styles: $data['styles'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'data' => $this->data,
            'styles' => $this->styles
        ];
    }
}