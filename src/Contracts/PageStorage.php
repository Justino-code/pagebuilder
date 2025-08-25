<?php

namespace Justino\PageBuilder\Contracts;

interface PageStorage
{
    public function all(): array;
    public function find(string $slug): ?array;
    public function save(array $data): bool;
    public function delete(string $slug): bool;
}