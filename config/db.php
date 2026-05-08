<?php

// ================= SECURITY HEADERS =================
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer-when-downgrade");

// CSP (možeš kasnije pooštriti)
header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:;");

// ================= SESSION HARDENING =================
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// ================= CONFIG =================
define('BASE_URL', '/anketa/'); // prilagodi prema potrebi

// ================= DB =================
$conn = new mysqli("localhost", "dedamraz_anketa", "a9^g23KqPfZTd;0.", "dedamraz_anketa");
if ($conn->connect_error) {
    die("DB error");
}

// ================= HELPERS =================

// XSS escape
function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// CSRF
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf($token)
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

// AUTH CHECK
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "index.php");
        exit;
    }
}

function require_admin()
{
    if ($_SESSION['role'] !== 'admin') {
        die("Zabranjen pristup");
    }
}
