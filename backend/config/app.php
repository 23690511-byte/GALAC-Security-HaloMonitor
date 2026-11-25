<?php
/**
 * HaloMonitor - Configuración Centralizada de la Aplicación
 * GALAC Security - Proyecto Web Final
 */

// 1. Configuración Centralizada de Zona Horaria (Requisito 1) y Codificación UTF-8
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'America/Mexico_City');
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// 2. Manejo de Errores y Depuración
$isDev = (getenv('APP_ENV') ?: 'development') === 'development';
if ($isDev) {
    ini_set('display_errors', '0'); // Mantenemos 0 para no romper respuestas JSON
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// 3. Configuración Segura de Sesión PHP
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    // Si corre sobre HTTPS, habilitar cookie_secure
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// 4. Configuración Centralizada de CORS para SPA (Vite / Vue 3)
function handleCors() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
    
    // Responder inmediatamente a peticiones Preflight OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

handleCors();
