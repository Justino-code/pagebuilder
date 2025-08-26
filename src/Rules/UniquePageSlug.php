<?php

namespace Justino\PageBuilder\Rules;

use Illuminate\Contracts\Validation\Rule;
use Justino\PageBuilder\Services\JsonPageStorage;

class UniquePageSlug implements Rule
{
    protected $type;
    protected $currentSlug;
    protected $storage;

    public function __construct($type = 'page', $currentSlug = null)
    {
        $this->type = $type;
        $this->currentSlug = $currentSlug;
        $this->storage = app(JsonPageStorage::class);
    }

    public function passes($attribute, $value)
    {
        // Se estiver editando e o slug não mudou, é válido
        if ($this->currentSlug && $value === $this->currentSlug) {
            return true;
        }

        // Verificar se o slug já existe
        $existingItem = $this->storage->find($value, $this->type);
        
        return $existingItem === null;
    }

    public function message()
    {
        return __('pagebuilder::messages.slug_already_exists');
    }
}