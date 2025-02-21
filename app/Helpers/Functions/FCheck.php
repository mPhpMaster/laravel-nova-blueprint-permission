<?php

if( !function_exists('isValidUrl') ) {
    function isValidUrl($url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }
}
