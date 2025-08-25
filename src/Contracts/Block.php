<?php

namespace Justino\PageBuilder\Contracts;

interface Block
{
    public static function type(): string;
    public static function label(): string;
    public static function icon(): string;
    public static function schema(): array;
    public static function defaults(): array;
    public function render(array $data): string;
}