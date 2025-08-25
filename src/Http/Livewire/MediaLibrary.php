<?php

namespace Justino\PageBuilder\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class MediaLibrary extends Component
{
    use WithFileUploads;
    
    public $images = [];
    public $uploadedImages = [];
    public $selectedImage = null;
    public $showUploadModal = false;
    public $searchTerm = '';
    
    public function mount()
    {
        $this->loadImages();
    }
    
    public function loadImages()
    {
        $disk = config('pagebuilder.media.disk', 'public');
        $path = config('pagebuilder.media.path', 'pagebuilder/media');
        
        $files = Storage::disk($disk)->files($path);
        
        $this->images = array_filter($files, function($file) {
            return preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $file);
        });
    }
    
    public function selectImage($imagePath)
    {
        $disk = config('pagebuilder.media.disk', 'public');
        $this->selectedImage = Storage::disk($disk)->url($imagePath);
    }
    
    public function confirmSelection()
    {
        if ($this->selectedImage) {
            $this->emit('mediaSelected', $this->selectedImage);
            $this->selectedImage = null;
            $this->dispatchBrowserEvent('close-media-library');
        }
    }
    
    public function uploadImages()
    {
        $this->validate([
            'uploadedImages.*' => 'image|max:2048',
        ]);
        
        $disk = config('pagebuilder.media.disk', 'public');
        $path = config('pagebuilder.media.path', 'pagebuilder/media');
        
        foreach ($this->uploadedImages as $image) {
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs($path, $filename, $disk);
        }
        
        $this->uploadedImages = [];
        $this->showUploadModal = false;
        $this->loadImages();
        
        session()->flash('message', 'Images uploaded successfully.');
    }
    
    public function getFilteredImages()
    {
        if (empty($this->searchTerm)) {
            return $this->images;
        }
        
        return array_filter($this->images, function($image) {
            return stripos($image, $this->searchTerm) !== false;
        });
    }
    
    public function render()
    {
        return view('pagebuilder::livewire.media-library', [
            'filteredImages' => $this->getFilteredImages()
        ]);
    }
}