@php
    $headerId = 'header-' . uniqid();
@endphp

<header id="{{ $headerId }}" class="header-template" 
        style="background-color: {{ $styles['background_color'] ?? '#ffffff' }};
               color: {{ $styles['text_color'] ?? '#000000' }};
               padding: {{ $styles['padding'] ?? '1rem 0' }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="logo">
                @if(($logo['type'] ?? 'text') === 'text')
                    <a href="/" class="logo-text" 
                       style="color: {{ $logo['styles']['color'] ?? '#000000' }};
                              font-size: {{ $logo['styles']['font_size'] ?? '24px' }};">
                        {{ $logo['text'] ?? 'My Website' }}
                    </a>
                @else
                    <a href="/" class="logo-image">
                        <img src="{{ $logo['image'] }}" alt="{{ $logo['text'] ?? 'Logo' }}" 
                             style="max-height: 50px;">
                    </a>
                @endif
            </div>

            <!-- Menu -->
            <nav class="menu">
                <ul class="flex space-x-6">
                    @foreach($menu_items as $item)
                        <li>
                            <a href="{{ $item['url'] }}" 
                               class="menu-item hover:opacity-75 transition"
                               style="color: {{ $item['styles']['color'] ?? '#333333' }};
                                      hover-color: {{ $item['styles']['hover_color'] ?? '#007bff' }};">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Mobile Menu Button -->
            <button class="mobile-menu-button md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</header>

<style>
    #{{ $headerId }} .menu-item:hover {
        color: {{ $item['styles']['hover_color'] ?? '#007bff' }} !important;
    }
</style>