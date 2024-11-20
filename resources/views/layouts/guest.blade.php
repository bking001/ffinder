<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
        <title>Finder</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/nprogress.css') }}" rel="stylesheet">
        <script src="{{ asset('vendor/bladewind/js/jquery.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/nprogress.js') }}"></script>
        <style>
            body {cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48px' height='36px' viewBox='-1 -1 65 49'%3E%3Cpath d='M0.137,2.241L7.331,18.464C7.509,18.867 7.98,19.048 8.383,18.87C8.592,18.777 8.752,18.598 8.82,18.379L11.103,11.103L18.379,8.82C18.799,8.689 19.033,8.241 18.901,7.821C18.833,7.602 18.673,7.424 18.464,7.331L2.241,0.137C1.436,-0.22 0.494,0.144 0.137,0.949C-0.046,1.36 -0.046,1.83 0.137,2.241Z' fill='%23818cf8'/%3E%3C/svg%3E") 0 0, pointer;}
        </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-indigo-400" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

        <script>         
            // Start progressing when link clicked
             jQuery('a').on('click', function() {
                 NProgress.start();
             });
 
             // Stop progressing when page loaded
             jQuery(window).on('load', function() {
                 NProgress.start();
                 NProgress.done(true);
             });
 
             // Start progressing when exiting the page
             jQuery(window).on('beforeunload', function() {
                 NProgress.start();
             });
         </script>
    </body>
</html>
