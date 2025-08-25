<section class="form-section py-16">
    <div class="container mx-auto px-4 max-w-2xl">
        @if(!empty($title))
            <h2 class="text-3xl font-bold text-center mb-4">{{ $title }}</h2>
        @endif
        
        @if(!empty($description))
            <p class="text-center mb-8">{{ $description }}</p>
        @endif
        
        <form action="{{ route('pagebuilder.form.submit') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
            @csrf
            <input type="hidden" name="form_type" value="contact">
            <input type="hidden" name="recipient" value="{{ $email_recipient }}">
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" id="name" required class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" id="email" required class="w-full p-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label for="message" class="block text-sm font-medium mb-1">Message</label>
                <textarea name="message" id="message" required rows="4" class="w-full p-2 border rounded"></textarea>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                {{ $submit_text }}
            </button>
        </form>
    </div>
</section>