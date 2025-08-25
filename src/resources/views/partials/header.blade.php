<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="text-xl font-bold text-gray-800">
                    {{ config('app.name', 'Page Builder') }}
                </a>
            </div>
            
            <nav class="hidden md:flex space-x-4">
                <a href="/" class="text-gray-600 hover:text-gray-800">Home</a>
                <a href="/about" class="text-gray-600 hover:text-gray-800">About</a>
                <a href="/contact" class="text-gray-600 hover:text-gray-800">Contact</a>
            </nav>
            
            <button class="md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</header>