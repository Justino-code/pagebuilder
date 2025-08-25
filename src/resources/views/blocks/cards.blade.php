<section class="cards-section py-16">
    <div class="container mx-auto px-4">
        @if(!empty($title))
            <h2 class="text-3xl font-bold text-center mb-12">{{ $title }}</h2>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($cards as $card)
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    @if(!empty($card['image']))
                        <img src="{{ $card['image'] }}" alt="{{ $card['title'] }}" class="w-16 h-16 mx-auto mb-4 rounded-full">
                    @elseif(!empty($card['icon']))
                        <div class="text-4xl mb-4">{{ $card['icon'] }}</div>
                    @endif
                    
                    <h3 class="text-xl font-bold mb-2">{{ $card['title'] }}</h3>
                    <p class="text-gray-600">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>