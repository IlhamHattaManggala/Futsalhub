<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::get('web_name', 'FutsalHub') }} - Platform Taktik & Manajemen Futsal Modern</title>
    <meta name="description" content="{{ \App\Models\Setting::get('web_description', 'Sistem informasi manajemen tim futsal modern terintegrasi.') }}">
    <meta name="keywords" content="{{ \App\Models\Setting::get('web_keywords', 'futsal, tim futsal, manajemen futsal, papan taktik') }}">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ \App\Models\Setting::get('web_name', 'FutsalHub') }} - Platform Taktik & Manajemen Futsal Modern">
    <meta property="og:description" content="{{ \App\Models\Setting::get('web_description', 'Sistem informasi manajemen tim futsal modern terintegrasi.') }}">
    <meta property="og:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ \App\Models\Setting::get('web_name', 'FutsalHub') }} - Platform Taktik & Manajemen Futsal Modern">
    <meta property="twitter:description" content="{{ \App\Models\Setting::get('web_description', 'Sistem informasi manajemen tim futsal modern terintegrasi.') }}">
    <meta property="twitter:image" content="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(\App\Models\Setting::get('web_favicon', 'favicon.ico')) }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Custom Landing Page Styles -->
    <link class="landing-style" rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FutsalHub">
    <link rel="apple-touch-icon" href="{{ asset('images/web_logo_1780410241.webp') }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('serviceworker.js') }}")
                    .then(reg => console.log('PWA Service Worker registered successfully.'))
                    .catch(err => console.error('PWA Service Worker registration failed.', err));
            });
        }
    </script>
</head>
<body class="antialiased hero-gradient">

    @include('landing.partials.navbar')

    @include('landing.partials.hero')

    @include('landing.partials.features')

    @include('landing.partials.advantages')

    @include('landing.partials.pricing')

    @include('landing.partials.statistics')

    @include('landing.partials.cta')

    @include('landing.partials.footer')


    <!-- Custom Landing Page Scripts -->
    <script src="{{ asset('js/landing.js') }}"></script>

</body>
</html>
