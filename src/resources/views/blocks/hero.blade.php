<section class="hero-section bg-cover bg-center py-20" 
         @if(!empty($background_image)) style="background-image: url('{{ $background_image }}')" @endif>
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">{{ $title }}</h1>
        <p class="text-xl text-white mb-8">{{ $subtitle }}</p>
        @if(!empty($cta_text))
            <a href="{{ $cta_link }}" class="bg-blue-500 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-600">
                {{ $cta_text }}
            </a>
        @endif
    </div>
</section>

<style>
    .hero-section {
        @if(!empty($background_image))
            background-image: url('{{ $background_image }}');
        @else
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        @endif
    }
</style>