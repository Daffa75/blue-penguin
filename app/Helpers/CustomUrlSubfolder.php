<?php

if (!function_exists('route')) {
    function route($name, $parameters = [], $absolute = true)
    {
        $appUrl = 'https://eng.unhas.ac.id'; // in your case: http://app.dev
        $appUrlSuffix = 'siminformatika'; // in your case: subdirectory

        // Additional check, do the workaround only when a suffix is present and only when urls are absolute
        if ($appUrlSuffix && $absolute) {
            // Add the relative path to the app root url
            $relativePath = app('url')->route($name, $parameters, false);
            $url = $appUrl.'/'.$appUrlSuffix.$relativePath;
        } else {
            // This is the default behavior of route() you can find in laravel\vendor\laravel\framework\src\Illuminate\Foundation\helpers.php
            $url = app('url')->route($name, $parameters, $absolute);
        }

        return $url;
    }
}
