<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KILAT' }}</title>

    <script>
        if (localStorage.getItem('kilat-theme') === 'light') {
            document.documentElement.classList.add('light');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Public+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[rgb(var(--c-bg))] font-sans text-[rgb(var(--c-body))] antialiased transition-colors duration-300">

    @include('partials.navbar')

    <main>
        {{ $slot }}
    </main>

    @include('partials.footer')

    @livewireScripts
</body>
</html>
