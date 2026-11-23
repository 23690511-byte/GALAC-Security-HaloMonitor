<?php
/**
 * HaloMonitor - Endpoint: Listado de Usuarios y Catálogos de Soporte
 * GET /backend/api/users/index.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireAuth();
$pdo = getDatabaseConnection();

// Usuarios activos para asignación
$stmtUsers = $pdo->query("
    SELECT u.id, u.nombre, u.email, u.departamento, r.nombre AS rol
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    WHERE u.activo = 1
    ORDER BY u.nombre ASC
");
$usuarios = $stmtUsers->fetchAll();

// Sistemas Monitoreados para vincular a tickets
$stmtSistemas = $pdo->query("
    SELECT id, nombre, ip_host, tipo, estado, tiempo_respuesta_ms
    FROM sistemas_monitoreados
    ORDER BY nombre ASC
");
$sistemas = $stmtSistemas->fetchAll();

jsonResponse(200, [
    'ok' => true,
    'message' => 'Catálogos recuperados.',
    'data' => [
        'usuarios' => $usuarios,
        'sistemas' => $sistemas
    ]
]);
