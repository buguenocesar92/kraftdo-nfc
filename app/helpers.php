<?php

if (!function_exists('clean_route')) {
    /**
     * Generate a clean URL for the given route without index.php
     */
    function clean_route(string $name, $parameters = [], bool $absolute = true): string
    {
        $url = route($name, $parameters, $absolute);
        
        // Remove index.php from the URL if present
        return str_replace('/index.php', '', $url);
    }
}

if (!function_exists('clean_url')) {
    /**
     * Generate a clean URL without index.php
     */
    function clean_url(string $path = '', bool $secure = null): string
    {
        $url = url($path, [], $secure);
        
        // Remove index.php from the URL if present
        return str_replace('/index.php', '', $url);
    }
}