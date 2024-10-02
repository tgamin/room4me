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
    <body class="antialiased min-h-screen">
        <div class="min-h-screen bg-gray-50 pb-10">
            @include('templates.navigation')

            <!-- Page Heading -->
            @if(isset($header))
                <header>
                    <div class="mx-auto pt-8 px-6 sm:px-4 lg:px-8 flex items-center">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="min-h-screen p-4">
                <div class="mx-auto pt-6 mb-5">
                    @include('templates.flash-message')
                </div>

                {{ $slot }}
            </main>
        </div>
        <script src="https://unpkg.com/flowbite@1.6.3/dist/flowbite.js"></script>
        <script src="https://unpkg.com/flowbite@1.6.3/dist/datepicker.js"></script>
        <script src="{!! mix('js/app.js') !!}"></script>
    </body>
</html>
