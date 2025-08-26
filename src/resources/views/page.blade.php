<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title ?? '' }}</title>
    
    @if(config('pagebuilder.ui.css_framework') === 'tailwind')
        <script src="https://cdn.tailwindcss.com"></script>
    @elseif(config('pagebuilder.ui.css_framework') === 'bootstrap')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <style>
        {!! $page->customCss !!}
    </style>
</head>
<body class="bg-white">
    @if($page->headerEnabled)
        @php
            $headerStorage = app('Justino\PageBuilder\Services\JsonPageStorage');
            $header = $headerStorage->getDefault('header');
        @endphp
        @if($header)
            @php
                $blockManager = app('Justino\PageBuilder\Services\BlockManager');
                $headerBlock = $blockManager->getBlockClass('header');
            @endphp
            @if($headerBlock)
                {!! (new $headerBlock())->render($header) !!}
            @endif
        @endif
    @endif
    
    <main>
        @foreach($page->content as $block)
            @php
                $blockManager = app('Justino\PageBuilder\Services\BlockManager');
                $blockClass = $blockManager->getBlockClass($block['type']);
            @endphp
            
            @if($blockClass)
                {!! (new $blockClass())->render($block['data']) !!}
            @endif
        @endforeach
    </main>
    
    @if($page->footerEnabled)
        @php
            $footerStorage = app('Justino\PageBuilder\Services\JsonPageStorage');
            $footer = $footerStorage->getDefault('footer');
        @endphp
        @if($footer)
            @php
                $blockManager = app('Justino\PageBuilder\Services\BlockManager');
                $footerBlock = $blockManager->getBlockClass('footer');
            @endphp
            @if($footerBlock)
                {!! (new $footerBlock())->render($footer) !!}
            @endif
        @endif
    @endif
    
    <script>
        {!! $page->customJs !!}
    </script>
</body>
</html>