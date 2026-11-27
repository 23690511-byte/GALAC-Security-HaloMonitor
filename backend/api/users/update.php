<?php
/**
 * HaloMonitor - Endpoint: Edición de Rol y Departamento de Usuario (Exclusivo Administrador)
 * POST /backend/api/users/update.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

$adminUser = requireRole(['Administrador']);

$input = readJson();
$targetId = isset($input['id']) ? (int)$input['id'] : 0;
$rolId = isset($input['rol_id']) ? (int)$input['rol_id'] : 0;
$departamento = trim($input['departamento'] ?? '');
$nombre = trim($input['nombre'] ?? '');

if ($targetId <= 0 || !in_array($rolId, [1, 2, 3, 4], true) || empty($departamento)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Datos inválidos: ID de usuario, Rol válido y Departamento/Empresa son obligatorios.',
        'data' => null
    ]);
}

$pdo = getDatabaseConnection();

$stmtCheck = $pdo->prepare("SELECT id, nombre, email FROM usuarios WHERE id = :id LIMIT 1");
$stmtCheck->execute([':id' => $targetId]);
$targetUser = $stmtCheck->fetch();

if (!$targetUser) {
    jsonResponse(404, [
        'ok' => false,
        'message' => "Usuario con ID {$targetId} no encontrado.",
        'data' => null
    ]);
}

// Prevenir que el administrador se quite su propio rol de Administrador
if ($targetId === (int)$adminUser['id'] && $rolId !== 1) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Operación rechazada: No puede cambiar su propio rol de Administrador mientras está en sesión.',
        'data' => null
    ]);
}

$nombreFinal = !empty($nombre) ? $nombre : $targetUser['nombre'];

$stmtUpdate = $pdo->prepare("
    UPDATE usuarios
    SET nombre = :nombre, rol_id = :rol_id, departamento = :depto
    WHERE id = :id
");
$stmtUpdate->execute([
    ':nombre' => $nombreFinal,
    ':rol_id' => $rolId,
    ':depto'  => $departamento,
    ':id'     => $targetId
]);

jsonResponse(200, [
    'ok' => true,
    'message' => "Usuario '{$nombreFinal}' actualizado exitosamente.",
    'data' => [
        'id'           => $targetId,
        'nombre'       => $nombreFinal,
        'rol_id'       => $rolId,
        'departamento' => $departamento
    ]
]);
