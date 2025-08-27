<section class="cta-section py-16" style="background-color: {{ $background_color }};">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">{{ __($title) }}</h2>
        <p class="text-lg mb-8">{{ __($description) }}</p>
        <a href="{{ $button_link }}" class="bg-blue-500 text-white px-8 py-3 rounded-lg text-lg hover:bg-blue-600">
            {{ __($button_text) }}
        </a>
    </div>
</section>
