<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {!! \App\Meta\Meta::render() !!}
        <title inertia>Parcels Mart</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="shortcut icon" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png" >
        <link rel="icon" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png" >
        <link rel="apple-touch-icon" sizes="152x152" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png">
        <link rel="apple-touch-icon" sizes="120x120" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png">
        <link rel="apple-touch-icon" sizes="76x76" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png">
        <link rel="apple-touch-icon" href="https://planetnium.com/parcel/wp-content/uploads/2023/07/fav.png">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
    <!-- Google tag (gtag.js) --> <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q2584X5G5E"></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-Q2584X5G5E'); </script>
</html>
