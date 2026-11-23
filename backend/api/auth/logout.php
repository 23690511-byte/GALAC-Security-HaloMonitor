<?php
/**
 * HaloMonitor - Endpoint: Cierre de Sesión
 * POST /backend/api/auth/logout.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

logoutUserSession();

jsonResponse(200, [
    'ok' => true,
    'message' => 'Sesión cerrada correctamente.',
    'data' => null
]);
