<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="#4f46e5" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.png" sizes="any">
<link rel="icon" href="/favicon.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">



@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
