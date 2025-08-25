<nav class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a 
                    href="{{ route('pagebuilder.pages.index') }}"
                    class="text-xl font-bold text-gray-800"
                >
                    Page Builder
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                <a 
                    href="{{ route('pagebuilder.pages.index') }}"
                    class="text-gray-600 hover:text-gray-800"
                >
                    Pages
                </a>
                <a 
                    href="{{ url('/') }}"
                    class="text-gray-600 hover:text-gray-800"
                >
                    View Site
                </a>
            </div>
        </div>
    </div>
</nav>