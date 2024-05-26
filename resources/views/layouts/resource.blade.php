<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head> 
    <body class="bg-gray-100">
        <div class="container mx-auto p-4">
            <div class="flex flex-wrap md:flex-nowrap">
                <!-- Left Box for Image -->
                <div class="w-full md:w-1/2 p-2">
                    <img src="{{ $resource-url }}" alt="{{ $description }}" class="w-full h-auto">
                </div>

                <!-- Right Box for Content -->
                <div class="w-full md:w-1/2 p-2">
                    <h1 class="text-2xl font-bold mb-2">{{ $title }}</h1>
                    <p class="mb-4">{{ $description }}</p>
                    <div class="mb-4">
                        <h2 class="font-bold">Features</h2>
                        <ul class="list-disc pl-5">
                            @foreach($features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h2 class="font-bold">Limitations</h2>
                        <ul class="list-disc pl-5">
                            @foreach($limitations as $limitation)
                                <li>{{ $limitation }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Metadata Box -->
                <div class="w-full md:w-1/2 p-2 md:pl-8">
                    <div class="mb-4">
                        <h2 class="font-bold">Topics</h2>
                        <ul class="list-disc pl-5">
                            @foreach($topics as $topic)
                                <li>{{ $topic }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h2 class="font-bold">Difficulty</h2>
                        <p>{{ $difficulty }}</p>
                    </div>
                    <div class="mb-4">
                        <h2 class="font-bold">Cost</h2>
                        <p>{{ $cost }}</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
