<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Xtrapay Business') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="description" content="Business payment management and transaction processing platform">
        <meta name="theme-color" content="#3b82f6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="XtraPay">
        <meta name="mobile-web-app-capable" content="yes">
        
        <!-- PWA Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
        <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
        <link rel="manifest" href="/manifest.json">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/pwa.css" rel="stylesheet">
        <link href="/css/dashboard.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>body { font-family: 'Inter', sans-serif; }</style>
        
        @stack('styles')
    </head>
    <body>
        <div class="min-vh-100 bg-light">
            @include('layouts.navigation')
            <main>
                @isset($slot)
                    {{ $slot }}
                @else
                    {{ $content ?? '' }}
                @endisset
            </main>
        </div>
        
        <!-- PWA Install Prompt -->
        @include('components.pwa-install-prompt')
        
        <!-- PWA Scripts -->
        <script src="/js/pwa.js"></script>
        @stack('scripts')
    </body>
</html> 