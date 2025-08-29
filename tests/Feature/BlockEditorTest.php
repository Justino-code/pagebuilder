<?php

namespace Tests\Feature;

use Tests\TestCase;
use Justino\PageBuilder\Http\Livewire\BlockEditor;
use Justino\PageBuilder\Services\BlockManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockEditorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_invalid_block_type_gracefully()
    {
        $this->expectNotToPerformAssertions();
        
        try {
            $component = new BlockEditor();
            $component->mount(1, 'invalid_block_type', [], []);
            
            // Não deve lançar exceção, apenas definir erro
            $this->assertTrue($component->hasError);
            
        } catch (\Exception $e) {
            $this->fail("BlockEditor should handle invalid block types gracefully");
        }
    }

    /** @test */
    public function it_validates_input_parameters()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $component = new BlockEditor();
        $component->mount(null, '', 'not_array', 'not_array');
    }

    /** @test */
    public function it_renders_error_view_when_in_error_state()
    {
        $component = new BlockEditor();
        $component->mount(1, 'invalid_type', [], []);
        $component->hasError = true;
        $component->errorMessage = 'Test error';
        
        $view = $component->render();
        
        $this->assertStringContainsString('block-editor-error', $view->getName());
    }
}