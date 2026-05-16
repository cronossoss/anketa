<?php

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

function request($key, $default = null)
{
    return $_REQUEST[$key] ?? $default;
}

function post($key, $default = null)
{
    return $_POST[$key] ?? $default;
}

function get($key, $default = null)
{
    return $_GET[$key] ?? $default;
}
