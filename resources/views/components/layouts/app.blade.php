<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>{{ $title ?? 'SIM Informatika' }}</title>
        @filamentScripts()
        @filamentStyles()
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
