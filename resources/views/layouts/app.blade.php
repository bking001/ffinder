<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
        <title>Finder</title>

        <!-- Fonts  -->
         <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        
        <link href="{{ asset('vendor/bladewind/css/main.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/bladewind/css/select2.min.css') }}" rel="stylesheet" />
       
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
        <link href="{{ asset('vendor/bladewind/css/nprogress.css') }}" rel="stylesheet">
  
        <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/filter.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/airsoft.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/mikrotik.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/prtg_clone.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/type.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/portforward.js') }}"></script>

        <script src="{{ asset('vendor/bladewind/js/ssh.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/tms.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/bdcom.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/huawei.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/zte.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/vsolution.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/hsgq.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/antenna.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/zyxel.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/cisco.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/mikrotik_router.js') }}"></script>


        <script src="{{ asset('vendor/bladewind/js/OLT_BDCOM.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_HUAWEI.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_VSOLUTION.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_ZTE.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_HSGQ.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_ZYXEL.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/OLT_CISCO.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/install.js') }}"></script>
        

        <script src="{{ asset('vendor/bladewind/js/jquery.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/highcharts.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/highcharts-more.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/exporting.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/export-data.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/accessibility.js') }}"></script>

        <script src="{{ asset('vendor/bladewind/js/install.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/wialon.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/select2.min.js') }}"></script>
        <script src="{{ asset('vendor/bladewind/js/leaflet.js') }}"></script> 
        <script src="{{ asset('vendor/bladewind/js/nprogress.js') }}"></script>  
    
    

        <style>
            body, .navbar , x-nav-link{cursor: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48px' height='36px' viewBox='-1 -1 65 49'%3E%3Cpath d='M0.137,2.241L7.331,18.464C7.509,18.867 7.98,19.048 8.383,18.87C8.592,18.777 8.752,18.598 8.82,18.379L11.103,11.103L18.379,8.82C18.799,8.689 19.033,8.241 18.901,7.821C18.833,7.602 18.673,7.424 18.464,7.331L2.241,0.137C1.436,-0.22 0.494,0.144 0.137,0.949C-0.046,1.36 -0.046,1.83 0.137,2.241Z' fill='%23818cf8'/%3E%3C/svg%3E") 0 0, pointer;}
        </style>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>

            function toggleDarkMode() 
            {
                const htmlElement = document.querySelector('html');
                htmlElement.classList.toggle('dark');
                const darkModeEnabled = htmlElement.classList.contains('dark');
                localStorage.setItem('darkMode', darkModeEnabled);
            }

            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage))) 
            {
                document.querySelector('html').classList.add('dark');
            } 
            else 
            {
                document.querySelector('html').classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased">
 
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')
 
            <!-- Page Heading -->

            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>              
                {{ $slot }}
            </main>
        </div>

     
 
     
    </body>
</html>
