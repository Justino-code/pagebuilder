<?php

namespace Justino\PageBuilder\Services;

class BlockManager
{
    protected $blocks = [];
    
    public function __construct()
    {
        $this->registerDefaultBlocks();
    }
    
    public function registerDefaultBlocks()
{
    $defaultBlocks = config('pagebuilder.blocks.default', []);
    
    // Adicionar blocos de template se não estiverem na configuração
    if (!in_array(\Justino\PageBuilder\Blocks\HeaderBlock::class, $defaultBlocks)) {
        $defaultBlocks[] = \Justino\PageBuilder\Blocks\HeaderBlock::class;
    }
    
    if (!in_array(\Justino\PageBuilder\Blocks\FooterBlock::class, $defaultBlocks)) {
        $defaultBlocks[] = \Justino\PageBuilder\Blocks\FooterBlock::class;
    }
    
    foreach ($defaultBlocks as $blockClass) {
        $this->registerBlock($blockClass);
    }
}
    
    public function registerBlock($blockClass)
    {
        if (class_exists($blockClass) && in_array(\Justino\PageBuilder\Contracts\Block::class, class_implements($blockClass))) {
            $this->blocks[$blockClass::type()] = $blockClass;
        }
    }
    
    public function getAvailableBlocks(): array
    {
        $blocks = [];
        
        foreach ($this->blocks as $type => $class) {
            $blocks[] = [
                'type' => $type,
                'label' => $class::label(),
                'icon' => $class::icon(),
                'schema' => $class::schema(),
            ];
        }
        
        return $blocks;
    }
    
    public function getBlockClass($type)
    {
        return $this->blocks[$type] ?? null;
    }
}