<?php

namespace Justino\PageBuilder\Services;

use Justino\PageBuilder\DTOs\BlockData;
use Illuminate\Contracts\View\Factory as ViewFactory;

class BlockRenderer
{
    protected $blockManager;
    protected $view;
    
    public function __construct(BlockManager $blockManager, ViewFactory $view)
    {
        $this->blockManager = $blockManager;
        $this->view = $view;
    }
    
    public function render(BlockData $blockData): string
    {
        try {
            return $this->blockManager->renderBlock(
                $blockData->type,
                array_merge($blockData->data, ['styles' => $blockData->styles])
            );
        } catch (\Exception $e) {
            return $this->renderError($blockData->type, $e->getMessage());
        }
    }
    
    public function renderFromArray(array $blockArray): string
    {
        try {
            $blockData = BlockData::fromArray($blockArray);
            return $this->render($blockData);
        } catch (\Exception $e) {
            return $this->renderError($blockArray['type'] ?? 'unknown', $e->getMessage());
        }
    }
    
    public function renderMultiple(array $blocksArray): string
    {
        $output = '';
        
        foreach ($blocksArray as $index => $blockArray) {
            $output .= $this->renderFromArray($blockArray);
        }
        
        return $output;
    }
    
    public function renderPreview(array $blockArray): string
    {
        try {
            $blockData = BlockData::fromArray($blockArray);
            $blockClass = $this->blockManager->getBlockClass($blockData->type);
            
            if ($blockClass) {
                $previewView = $blockClass::getPreviewComponent();
                if ($this->view->exists($previewView)) {
                    return $this->view->make($previewView, [
                        'block' => $blockArray,
                        'data' => $blockData->data,
                        'styles' => $blockData->styles
                    ])->render();
                }
            }
            
            return $this->renderDefaultPreview($blockArray);
            
        } catch (\Exception $e) {
            return $this->renderError($blockArray['type'] ?? 'unknown', $e->getMessage());
        }
    }
    
    protected function renderDefaultPreview(array $blockArray): string
    {
        $type = $blockArray['type'] ?? 'unknown';
        $label = $this->blockManager->getBlockClass($type)::label() ?? $type;
        
        return "
            <div class='block-preview default-preview p-4 bg-yellow-50 border border-yellow-200 rounded'>
                <div class='flex items-center mb-2'>
                    <span class='text-lg mr-2'>📦</span>
                    <span class='font-medium'>{$label}</span>
                </div>
                <p class='text-sm text-yellow-700'>Preview not available for this block type.</p>
            </div>
        ";
    }
    
    protected function renderError(string $type, string $message): string
    {
        return "
            <div class='block-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>
                <div class='flex items-center'>
                    <span class='text-lg mr-2'>❌</span>
                    <strong class='font-bold'>Error rendering block</strong>
                </div>
                <div class='mt-1 text-sm'>
                    <p>Type: {$type}</p>
                    <p class='text-xs opacity-75'>{$message}</p>
                </div>
            </div>
        ";
    }
}