<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Helpers\Translator;
use Justino\PageBuilder\DTOs\PageData;

class PageManager extends Component
{
    public $pages = [];
    public $showDeleteModal = false;
    public $pageToDelete = null;
    
    public function mount()
    {
        $this->loadPages();
    }
    
    public function loadPages()
    {
        $storage = app(JsonPageStorage::class);
        $this->pages = $storage->all('page');
    }
    
    public function createPage()
    {
        return redirect()->route('pagebuilder.pages.create');
    }
    
    public function editPage($slug)
    {
        return redirect()->route('pagebuilder.pages.edit', $slug);
    }
    
    public function confirmDelete($slug)
    {
        $this->pageToDelete = $slug;
        $this->showDeleteModal = true;
    }
    
    public function deletePage()
    {
        if ($this->pageToDelete) {
            $storage = app(JsonPageStorage::class);
            $storage->delete($this->pageToDelete);
            
            $this->showDeleteModal = false;
            $this->pageToDelete = null;
            $this->loadPages();
            
            session()->flash('message', Translator::trans('page_deleted'));
        }
    }
    
    
public function togglePublish($slug)
{
    $storage = app(JsonPageStorage::class);
    $page = $storage->find($slug, 'page');
    
    if ($page instanceof PageData) {
        // Criar novo DTO com dados atualizados
        $updatedPage = new PageData(
            title: $page->title,
            slug: $page->slug,
            content: $page->content,
            published: !$page->published,
            headerEnabled: $page->headerEnabled,
            footerEnabled: $page->footerEnabled,
            customCss: $page->customCss,
            customJs: $page->customJs,
            createdAt: $page->createdAt
        );
        
        $storage->savePage($updatedPage);
        $this->loadPages();
        
        $message = $updatedPage->published 
            ? Translator::trans('page_published')
            : Translator::trans('page_unpublished');
            
        session()->flash('message', $message);
    }
}
    
    public function render()
    {
        return view('pagebuilder::livewire.page-manager');
    }
}