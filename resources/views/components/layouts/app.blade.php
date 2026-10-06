<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>neriahpro.com - {{ $title ?? 'Digital Contract & Scope Lock' }}</title>
        @filamentStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 dark:bg-zinc-950 text-gray-900 dark:text-zinc-100 font-sans antialiased min-h-screen flex items-center justify-center p-0 sm:p-4 print:block print:p-0 print:m-0 print:bg-white">
        {{ $slot }}

        @filamentScripts
    </body>
</html>
