<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? \App\Models\Setting::get('seo_default_title', 'THILOR DESIGN') }}</title>
    <meta name="description" content="{{ $metaDescription ?? \App\Models\Setting::get('seo_default_description') }}">
    @if(isset($canonical))
        <link rel="canonical" href="{{ $canonical }}">
    @endif

    {{-- Open Graph / Twitter Cards --}}
    <meta property="og:title" content="{{ $title ?? 'THILOR DESIGN' }}">
    <meta property="og:description" content="{{ $metaDescription ?? \App\Models\Setting::get('seo_default_description') }}">
    <meta property="og:type" content="website">
    @if(isset($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    @stack('head')

    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-cream font-sans text-ink">
    <x-site-header />

    <main class="flex-1">
        @if (session('status'))
            <div class="container-shop mt-4">
                <div class="rounded-sm border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container-shop mt-4">
                <div class="rounded-sm border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <x-site-footer />
</body>
</html>
