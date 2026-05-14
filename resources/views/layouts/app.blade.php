<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-100 antialiased bg-dark-bg overflow-x-hidden">
        <div class="min-h-screen">

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-dark-card/80 backdrop-blur-md border-b border-white/10 sticky top-0 z-30">
                    <div class="w-full mx-auto py-4 px-6 sm:px-8 lg:px-10">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="relative overflow-x-hidden">
                <!-- Abstract Background Elements for Dashboard -->
                <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-primary-500/5 to-transparent -z-10"></div>
                <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-primary-500/10 rounded-full mix-blend-screen filter blur-3xl opacity-30 -z-10"></div>
                
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
