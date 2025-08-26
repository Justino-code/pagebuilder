<?php

namespace Justino\PageBuilder\Validation;

use Illuminate\Validation\Validator;
use Justino\PageBuilder\Services\JsonPageStorage;

class PageBuilderValidator
{
    public function validateUniquePageSlug($attribute, $value, $parameters, Validator $validator)
    {
        $storage = app(JsonPageStorage::class);
        
        // O primeiro parâmetro é o tipo (page, header, footer)
        $type = $parameters[0] ?? 'page';
        
        // O segundo parâmetro é o slug atual (para edição)
        $currentSlug = $parameters[1] ?? null;
        
        // Se estiver editando e o slug não mudou, é válido
        if ($currentSlug && $value === $currentSlug) {
            return true;
        }
        
        // Verificar se o slug já existe
        $existingItem = $storage->find($value, $type);
        
        return $existingItem === null;
    }
    
    public function validateUniqueTemplateSlug($attribute, $value, $parameters, Validator $validator)
    {
        $storage = app(JsonPageStorage::class);
        
        if (count($parameters) < 1) {
            return false;
        }
        
        $type = $parameters[0];
        $currentSlug = $parameters[1] ?? null;
        
        // Se estiver editando e o slug não mudou, é válido
        if ($currentSlug && $value === $currentSlug) {
            return true;
        }
        
        // Verificar se o slug já existe
        $existingTemplate = $storage->find($value, $type);
        
        return $existingTemplate === null;
    }
}