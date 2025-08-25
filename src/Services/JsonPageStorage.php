<?php

namespace Justino\PageBuilder\Services;

use Illuminate\Support\Facades\File;

class JsonPageStorage
{
    protected $storagePath;
    
    public function __construct()
    {
        $this->storagePath = config('pagebuilder.storage.path', storage_path('app/pagebuilder'));
        
        if (!File::exists($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true);
        }
    }
    
    public function all(string $type = 'page'): array
    {
        $items = [];
        $files = File::files($this->storagePath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $content = File::get($file->getPathname());
                $data = json_decode($content, true);
                
                // Filtrar por tipo se especificado
                if (!$type || ($data['type'] ?? 'page') === $type) {
                    $items[] = $data;
                }
            }
        }
        
        return $items;
    }
    
    public function find(string $slug, string $type = null): ?array
    {
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        if (!File::exists($filePath)) {
            return null;
        }
        
        $data = json_decode(File::get($filePath), true);
        
        // Verificar tipo se especificado
        if ($type && ($data['type'] ?? 'page') !== $type) {
            return null;
        }
        
        return $data;
    }
    
    public function save(array $data): bool
    {
        $type = $data['type'] ?? 'page';
        $slug = $data['slug'] ?? $this->generateSlug($data, $type);
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        return File::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function delete(string $slug): bool
    {
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        
        return false;
    }
    
    public function getDefault(string $type): ?array
    {
        $items = $this->all($type);
        
        foreach ($items as $item) {
            if ($item['is_default'] ?? false) {
                return $item;
            }
        }
        
        return count($items) > 0 ? $items[0] : null;
    }
    
    protected function generateSlug(array $data, string $type): string
    {
        if ($type === 'page') {
            return \Illuminate\Support\Str::slug($data['title']);
        }
        
        return $type . '_' . ($data['name'] ? \Illuminate\Support\Str::slug($data['name']) : uniqid());
    }
}