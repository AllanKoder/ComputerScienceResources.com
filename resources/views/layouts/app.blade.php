<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="htmx-config" content='{"refreshOnHistoryMiss":"true"}' />
        
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Font Awesome -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased">
        <div class="max-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif  

            <!-- Page Content -->
            <main class="bg-white">
                {{ $slot }}
            </main>
        </div>
    </body>

    <!-- Global Shared Code -->
    <x-modals.confirm-modal/>
    <x-modals.warning-modal/>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('getURL', () => {
                return window.location.href.split('?')[0];
            });

            Alpine.store('getQueryParameter', (name) => {
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const queryValue = urlParams.get(name) || urlParams.getAll(`${name}[]`);

                return queryValue;
            });
        });
    </script>
</html>