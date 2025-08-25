<?php

namespace Justino\PageBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Meu\PageBuilder\Contracts\PageStorage;

class Page extends Model
{
    protected $fillable = [
        'title', 
        'slug', 
        'content', 
        'layout', 
        'meta', 
        'published', 
        'header_enabled',
        'footer_enabled',
        'custom_css',
        'custom_js'
    ];
    
    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
        'published' => 'boolean',
        'header_enabled' => 'boolean',
        'footer_enabled' => 'boolean',
    ];
    
    public static function boot()
    {
        parent::boot();
        
        static::saving(function ($page) {
            if (empty($page->slug)) {
                $page->slug = \Illuminate\Support\Str::slug($page->title);
            }
        });
    }
    
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    public function render()
    {
        return view('pagebuilder::page', ['page' => $this])->render();
    }
    
    public function getUrlAttribute()
    {
        return route('pagebuilder.page.show', $this->slug);
    }
}