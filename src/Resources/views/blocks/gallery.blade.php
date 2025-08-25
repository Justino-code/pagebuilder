<section class="gallery-section py-16">
    <div class="container mx-auto px-4">
        @if(!empty($title))
            <h2 class="text-3xl font-bold text-center mb-12">{{ $title }}</h2>
        @endif
        
        <div class="grid grid-cols-2 md:grid-cols-{{ $columns }} gap-4">
            @foreach($images as $image)
                <div class="gallery-item">
                    <img src="{{ $image['image'] }}" alt="{{ $image['caption'] }}" class="w-full h-48 object-cover rounded-lg">
                    @if(!empty($image['caption']))
                        <p class="text-sm text-center mt-2">{{ $image['caption'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>