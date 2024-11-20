const mix = require('laravel-mix');

mix.setResourceRoot('http://finder.airlink.ge/')
   .js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
      require('tailwindcss'),
   ]);
