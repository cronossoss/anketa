<?php

function url($path = '')
{
    return rtrim(BASE_URL, '/')
        . '/'
        . ltrim($path, '/');
}

function app_path($path = '')
{
    $basePath = parse_url(
        BASE_URL,
        PHP_URL_PATH
    );

    return rtrim($basePath, '/')
        . '/'
        . ltrim($path, '/');
}
