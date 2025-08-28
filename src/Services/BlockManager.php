<?php

namespace Justino\PageBuilder\Services;

use Justino\PageBuilder\Contracts\Block;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BlockManager
{
    protected $blocks = [];
    protected $editorComponents = [];
    protected $previewComponents = [];
    
    public function __construct()
    {
        $this->registerCoreBlocks();
    }
    
    protected function registerCoreBlocks(): void
    {
        $coreBlocks = [
            \Justino\PageBuilder\Blocks\TextBlock::class,
            \Justino\PageBuilder\Blocks\HeroBlock::class,
            \Justino\PageBuilder\Blocks\CTABlock::class,
            \Justino\PageBuilder\Blocks\CardsBlock::class,
            \Justino\PageBuilder\Blocks\FormBlock::class,
            \Justino\PageBuilder\Blocks\GalleryBlock::class,
            \Justino\PageBuilder\Blocks\HeaderBlock::class,
            \Justino\PageBuilder\Blocks\FooterBlock::class,
        ];
        
        foreach ($coreBlocks as $blockClass) {
            $this->registerBlock($blockClass);
        }
    }
    
    public function registerBlock(string $blockClass): void
    {
        if (!class_exists($blockClass)) {
            throw new InvalidArgumentException("Block class {$blockClass} does not exist");
        }
        
        if (!in_array(Block::class, class_implements($blockClass))) {
            throw new InvalidArgumentException("Block class {$blockClass} must implement Block interface");
        }
        
        $type = $blockClass::type();
        
        if (isset($this->blocks[$type])) {
            Log::warning("Block type '{$type}' is already registered. Overwriting.");
        }
        
        $this->blocks[$type] = $blockClass;
        $this->editorComponents[$type] = $blockClass::getEditorComponent();
        $this->previewComponents[$type] = $blockClass::getPreviewComponent();
        
        Log::info("Block registered: {$type} ({$blockClass})");
    }
    
    public function getAvailableBlocks(): array
    {
        $blocks = [];
        
        foreach ($this->blocks as $type => $blockClass) {
            $blocks[] = [
                'type' => $type,
                'label' => $blockClass::label(),
                'icon' => $blockClass::icon(),
                'schema' => $blockClass::schema(),
                'editorComponent' => $blockClass::getEditorComponent(),
                'previewComponent' => $blockClass::getPreviewComponent(),
                'defaults' => $blockClass::defaults()
            ];
        }
        
        return $blocks;
    }
    
    public function getBlockClass(string $type): ?string
    {
        return $this->blocks[$type] ?? null;
    }
    
    public function getBlockSchema(string $type): array
    {
        $blockClass = $this->getBlockClass($type);
        return $blockClass ? $blockClass::schema() : [];
    }
    
    public function getBlockDefaults(string $type): array
    {
        $blockClass = $this->getBlockClass($type);
        return $blockClass ? $blockClass::defaults() : [];
    }
    
    public function getEditorComponent(string $type): string
    {
        return $this->editorComponents[$type] ?? 'pagebuilder::blocks.editors.default';
    }
    
    public function getPreviewComponent(string $type): string
    {
        return $this->previewComponents[$type] ?? 'pagebuilder::blocks.previews.default';
    }
    
    public function createBlockInstance(string $type)
    {
        $blockClass = $this->getBlockClass($type);
        
        if (!$blockClass) {
            throw new InvalidArgumentException("Block type '{$type}' not found");
        }
        
        return app($blockClass);
    }
    
    public function renderBlock(string $type, array $data): string
    {
        try {
            $blockInstance = $this->createBlockInstance($type);
            return $blockInstance->render($data);
        } catch (\Exception $e) {
            Log::error("Failed to render block {$type}: " . $e->getMessage());
            
            return "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative'>
                <strong class='font-bold'>Error rendering block!</strong>
                <span class='block sm:inline'>Type: {$type}</span>
                <span class='block text-xs mt-1'>{$e->getMessage()}</span>
            </div>";
        }
    }
    
    public function isValidBlockType(string $type): bool
    {
        return isset($this->blocks[$type]);
    }
    
    public function getRegisteredTypes(): array
    {
        return array_keys($this->blocks);
    }
}