<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Builder - {{ config('app.name', 'Laravel') }}</title>
    
    @if(config('pagebuilder.ui.css_framework') === 'tailwind')
        <script src="https://cdn.tailwindcss.com"></script>
    @elseif(config('pagebuilder.ui.css_framework') === 'bootstrap')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @include('pagebuilder::partials.navigation')
        
        <main>
            @yield('content')
        </main>
    </div>
    
    @livewireScripts
</body>
</html>