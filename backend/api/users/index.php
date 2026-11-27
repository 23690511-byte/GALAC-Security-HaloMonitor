<?php
/**
 * HaloMonitor - Endpoint: Listado de Usuarios, Catálogos y Roles
 * GET /backend/api/users/index.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireRole(['Administrador']);
$pdo = getDatabaseConnection();

// Listado de Usuarios
$stmtUsers = $pdo->query("
    SELECT u.id, u.nombre, u.email, u.departamento, u.activo, u.rol_id, r.nombre AS rol, u.creado_en
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    ORDER BY u.id ASC
");
$usuarios = $stmtUsers->fetchAll();

// Roles disponibles
$stmtRoles = $pdo->query("SELECT id, nombre, descripcion FROM roles ORDER BY id ASC");
$roles = $stmtRoles->fetchAll();

// Sistemas Monitoreados
$stmtSistemas = $pdo->query("
    SELECT id, nombre, ip_host, tipo, estado, tiempo_respuesta_ms
    FROM sistemas_monitoreados
    ORDER BY nombre ASC
");
$sistemas = $stmtSistemas->fetchAll();

jsonResponse(200, [
    'ok' => true,
    'message' => 'Catálogos y usuarios recuperados.',
    'data' => [
        'usuarios' => $usuarios,
        'roles'    => $roles,
        'sistemas' => $sistemas
    ]
]);
