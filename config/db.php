<?php

session_start();

require_once __DIR__ . '/local.php';

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if ($conn->connect_error) {
    die("DB Error");
}

$conn->set_charset("utf8mb4");