<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/logosmall.png" sizes="192x192" />
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-M2H45JZT88"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-M2H45JZT88');
        </script>
        <title>Room4me</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-900 sm:items-center py-4 sm:pt-0">
            <x-language-switcher class="absolute top-10 right-10" />
            <div {{ $attributes->merge(['class' => 'w-full max-w-sm mx-auto sm:px-6 lg:px-8']) }}>
                <div class="flex justify-center items-center pt-8 sm:pt-0">
                    <a href="/">
                        <img src="/logo.png" alt="Room4me" />
                    </a>
                </div>

                <div class="mt-8 bg-white shadow rounded-lg">
                    <div class="mx-auto">
                        @include('templates.flash-message')
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
        <script src="https://unpkg.com/flowbite@1.4.7/dist/flowbite.js"></script>
        <script src="https://unpkg.com/flowbite@1.4.7/dist/datepicker.js"></script>
    </body>
</html>
