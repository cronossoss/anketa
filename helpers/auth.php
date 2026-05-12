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

function has_role($roles)
{
    if (!isset($_SESSION['role'])) {

        return false;
    }

    if (!is_array($roles)) {

        $roles = [$roles];
    }

    return in_array(
        $_SESSION['role'],
        $roles
    );
}

function require_role($roles)
{
    if (!has_role($roles)) {

        http_response_code(403);

        exit('403 Forbidden');
    }
}

function is_admin_panel_role()
{
    $roles = [
        'admin',
        'hr',
        'it',
        'manager'
    ];

    return in_array(
        $_SESSION['role'] ?? '',
        $roles
    );
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
