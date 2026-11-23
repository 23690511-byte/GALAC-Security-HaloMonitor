<?php
/**
 * HaloMonitor - Endpoint: Sesión Actual del Usuario
 * GET /backend/api/auth/me.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = getCurrentUser();

if (!$user) {
    jsonResponse(401, [
        'ok' => false,
        'message' => 'No hay una sesión activa.',
        'data' => null
    ]);
}

jsonResponse(200, [
    'ok' => true,
    'message' => 'Sesión activa verificada.',
    'data' => [
        'user' => $user
    ]
]);
