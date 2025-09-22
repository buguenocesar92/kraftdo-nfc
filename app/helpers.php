<?php

// Override the default route() helper to always generate clean URLs
if (!function_exists('route')) {
    /**
     * Generate a URL to a named route (clean version without index.php)
     */
    function route($name, $parameters = [], $absolute = true)
    {
        $url = app('url')->route($name, $parameters, $absolute);
        
        // Remove index.php from the URL if present
        return str_replace('/index.php', '', $url);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a url for the application (clean version without index.php)
     */
    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return app('url');
        }

        $url = app('url')->to($path, $parameters, $secure);
        
        // Remove index.php from the URL if present
        return str_replace('/index.php', '', $url);
    }
}