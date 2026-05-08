<?php

function require_login()
{
    if (!isset($_SESSION['user_id'])) {

        header("Location: " . BASE_URL);
        exit;
    }
}


function require_admin()
{
    if (
        !isset($_SESSION['role']) ||
        strtolower($_SESSION['role']) !== 'admin'
    ) {

        die("Access denied");
    }
}


function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}


function is_admin()
{
    return (
        isset($_SESSION['role']) &&
        strtolower($_SESSION['role']) === 'admin'
    );
}