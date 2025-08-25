<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\BlockManager;

class PageBuilderBlock extends Component
{
    public $index;
    public $block;
    public $isSelected = false;
    public $editing = false;
    
    protected $listeners = ['mediaSelected' => 'handleMediaSelected'];
    
    public function mount($index, $block, $isSelected = false)
    {
        $this->index = $index;
        $this->block = $block;
        $this->isSelected = $isSelected;
    }
    
    public function render()
    {
        $blockManager = app(BlockManager::class);
        $blockClass = $blockManager->getBlockClass($this->block['type']);
        
        return view('pagebuilder::livewire.page-builder-block', [
            'blockSchema' => $blockClass ? $blockClass::schema() : [],
            'blockData' => $this->block['data'] ?? []
        ]);
    }
    
    public function edit()
    {
        $this->editing = true;
    }
    
    public function save()
    {
        $this->emit('blockUpdated', $this->index, $this->block['data']);
        $this->editing = false;
    }
    
    public function remove()
    {
        $this->emit('blockRemoved', $this->index);
    }
    
    public function moveUp()
    {
        if ($this->index > 0) {
            $this->emit('blockMoved', $this->index, $this->index - 1);
        }
    }
    
    public function moveDown()
    {
        $this->emit('blockMoved', $this->index, $this->index + 1);
    }
    
    public function handleMediaSelected($url)
    {
        // Este método será implementado para lidar com a seleção de mídia
        // em campos específicos do bloco
    }
    
    public function updatedBlock($value, $key)
    {
        // Atualiza automaticamente os dados quando os campos são alterados
        $keys = explode('.', $key);
        
        if (count($keys) === 2 && $keys[0] === 'data') {
            $field = $keys[1];
            $this->block['data'][$field] = $value;
            $this->emit('blockUpdated', $this->index, $this->block['data']);
        }
    }
}