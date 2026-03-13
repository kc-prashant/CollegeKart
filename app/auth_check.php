<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';


function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login()
{
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    }
}

function require_admin()
{
    if (!isAdmin()) {
        http_response_code(403);
        exit("Access Denied");
    }
}