<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
{{-- Body: Manrope · Display/headings: Space Grotesk. display=swap avoids a blank
     first paint (text shows in the fallback, then swaps in once the webfont loads). --}}
<link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700|space-grotesk:500,600,700&display=swap"
    rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

<script>
    // Default new visitors to dark mode; Flux reads this and applies it before paint.
    if (! window.localStorage.getItem('flux.appearance')) {
        window.localStorage.setItem('flux.appearance', 'dark');
    }
</script>

@fluxAppearance
