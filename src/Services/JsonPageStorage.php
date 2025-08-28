<?php

namespace Justino\PageBuilder\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Justino\PageBuilder\DTOs\{
    PageData,
    TemplateData,
};

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
                
                if (!$type || ($data['type'] ?? 'page') === $type) {
                    $items[] = $this->createDtoFromData($data);
                }
            }
        }
        
        return $items;
    }
    
    public function find(string $slug, string $type = null)
    {
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        if (!File::exists($filePath)) {
            return null;
        }
        
        $data = json_decode(File::get($filePath), true);
        
        if ($type && ($data['type'] ?? 'page') !== $type) {
            return null;
        }
        
        return $this->createDtoFromData($data);
    }
    
    public function save(array $data): bool
    {
        $dto = $this->createDtoFromData($data);
        $filePath = $this->storagePath . '/' . $dto->slug . '.json';
        
        return File::put($filePath, json_encode($dto->toArray(), JSON_PRETTY_PRINT));
    }
    
    public function savePage(PageData $pageData): bool
    {
        $filePath = $this->storagePath . '/' . $pageData->slug . '.json';
        return File::put($filePath, $pageData->toJson());
    }
    
    public function saveTemplate(TemplateData $templateData): bool
    {
        $filePath = $this->storagePath . '/' . $templateData->slug . '.json';
        return File::put($filePath, $templateData->toJson());
    }
    
    public function delete(string $slug): bool
    {
        $filePath = $this->storagePath . '/' . $slug . '.json';
        
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }
        
        return false;
    }
    
    public function getDefault(string $type)
    {
        $items = $this->all($type);
        
        foreach ($items as $item) {
            if ($item->isDefault ?? false) {
                return $item;
            }
        }
        
        return count($items) > 0 ? $items[0] : null;
    }
    
    protected function createDtoFromData(array $data)
    {
        $type = $data['type'] ?? 'page';
        
        if ($type === 'page') {
            return PageData::fromArray($data);
        }
        
        return TemplateData::fromArray($data);
    }
    
    protected function generateSlug(array $data, string $type): string
    {
        if ($type === 'page') {
            return Str::slug($data['title'] ?? 'untitled');
        }
        
        return $type . '_' . (Str::slug($data['name'] ?? '') ?: uniqid());
    }
}