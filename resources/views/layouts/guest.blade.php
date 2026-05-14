<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Trickle Up Drive') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('asset/img/trickleup-favicon.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-100 antialiased bg-dark-bg relative overflow-hidden">
    <!-- Abstract Background Elements -->
    <div
        class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary-500/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 animate-blob">
    </div>
    <div
        class="absolute top-[20%] right-[-10%] w-96 h-96 bg-secondary-500/20 rounded-full mix-blend-screen filter blur-3xl opacity-50 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-primary-500/10 rounded-full mix-blend-screen filter blur-3xl opacity-50 animate-blob animation-delay-4000">
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
        <div
            class="w-full sm:max-w-md px-10 py-10 bg-dark-card/80 backdrop-blur-xl shadow-2xl border border-white/10 overflow-hidden sm:rounded-3xl transition-all duration-300">
            {{ $slot }}
        </div>

        <p class="mt-8 text-sm text-gray-500 font-medium">© {{ date('Y') }} Trickle Up Drive. All rights reserved.</p>
    </div>
</body>

</html>