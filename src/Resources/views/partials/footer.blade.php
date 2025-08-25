<footer class="bg-gray-800 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold mb-4">Company</h3>
                <ul class="space-y-2">
                    <li><a href="/about" class="hover:text-gray-300">About Us</a></li>
                    <li><a href="/contact" class="hover:text-gray-300">Contact</a></li>
                    <li><a href="/privacy" class="hover:text-gray-300">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-bold mb-4">Connect</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-gray-300">Twitter</a></li>
                    <li><a href="#" class="hover:text-gray-300">Facebook</a></li>
                    <li><a href="#" class="hover:text-gray-300">Instagram</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-bold mb-4">Newsletter</h3>
                <form class="space-y-2">
                    <input type="email" placeholder="Your email" class="w-full p-2 rounded text-gray-800">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Page Builder') }}. All rights reserved.</p>
        </div>
    </div>
</footer>