<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

abstract class BaseBlock implements Block
{
    public static function getEditorComponent(): string
    {
        return 'pagebuilder::blocks.editors.' . static::type();
    }
    
    public static function getPreviewComponent(): string
    {
        return 'pagebuilder::blocks.previews.' . static::type();
    }
}