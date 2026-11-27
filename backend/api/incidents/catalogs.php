<?php
/**
 * HaloMonitor - Endpoint: Catálogo de Sistemas Monitoreados y Operadores SOC
 * GET /backend/api/incidents/catalogs.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireAuth();
$pdo = getDatabaseConnection();

// Sistemas Monitoreados
$stmtSistemas = $pdo->query("
    SELECT id, nombre, ip_host, tipo, estado, tiempo_respuesta_ms
    FROM sistemas_monitoreados
    ORDER BY nombre ASC
");
$sistemas = $stmtSistemas->fetchAll();

// Usuarios Analistas / Administradores para asignación
$stmtUsers = $pdo->query("
    SELECT u.id, u.nombre, u.email, r.nombre AS rol
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    WHERE r.nombre IN ('Administrador', 'Analista') AND u.activo = 1
    ORDER BY u.id ASC
");
$usuarios = $stmtUsers->fetchAll();

jsonResponse(200, [
    'ok' => true,
    'message' => 'Catálogos de incidentes recuperados con éxito.',
    'data' => [
        'sistemas' => $sistemas,
        'usuarios' => $usuarios
    ]
]);
