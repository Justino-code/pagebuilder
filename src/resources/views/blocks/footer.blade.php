@php
    $footerId = 'footer-' . uniqid();
@endphp

<footer id="{{ $footerId }}" class="footer-template"
        style="background-color: {{ $styles['background_color'] ?? '#f8f9fa' }};
               color: {{ $styles['text_color'] ?? '#212529' }};">
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-{{ count($sections) }} gap-8 mb-8">
            @foreach($sections as $section)
                <div class="footer-section">
                    <h4 class="font-semibold mb-4">{{ $section['title'] }}</h4>
                    <ul class="space-y-2">
                        @foreach($section['links'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" 
                                   class="footer-link hover:underline"
                                   style="color: {{ $styles['link_color'] ?? '#007bff' }};">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        
        <div class="border-t border-gray-300 pt-6 text-center">
            <p class="copyright-text">{{ $copyright }}</p>
        </div>
    </div>
</footer>