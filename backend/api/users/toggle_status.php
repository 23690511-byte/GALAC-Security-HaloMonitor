<?php
/**
 * HaloMonitor - Endpoint: Alternar Estado de Usuario (Activar / Desactivar)
 * POST /backend/api/users/toggle_status.php
 * Exclusivo para Administrador
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

// Exclusivo Administrador
$adminUser = requireRole(['Administrador']);

$input = readJson();
$targetId = isset($input['id']) ? (int)$input['id'] : 0;

if ($targetId <= 0) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Identificador de usuario inválido o no especificado.',
        'data' => null
    ]);
}

// Prevenir que el administrador se desactive a sí mismo
if ($targetId === (int)$adminUser['id']) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Operación rechazada: No puede desactivar su propia cuenta de administrador en sesión.',
        'data' => null
    ]);
}

$pdo = getDatabaseConnection();

$stmtCheck = $pdo->prepare("SELECT id, nombre, email, activo FROM usuarios WHERE id = :id LIMIT 1");
$stmtCheck->execute([':id' => $targetId]);
$targetUser = $stmtCheck->fetch();

if (!$targetUser) {
    jsonResponse(404, [
        'ok' => false,
        'message' => "Usuario con ID {$targetId} no encontrado.",
        'data' => null
    ]);
}

$nuevoEstado = ((int)$targetUser['activo'] === 1) ? 0 : 1;

$stmtUpdate = $pdo->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
$stmtUpdate->execute([
    ':activo' => $nuevoEstado,
    ':id'     => $targetId
]);

// Registro en auditoría del sistema
$stmtAudit = $pdo->prepare("
    INSERT INTO auditoria_sistema (usuario_id, accion, detalles, ip, user_agent, creado_en)
    VALUES (:uid, 'TOGGLE_USER_STATUS', :detalles, :ip, :ua, NOW())
");
$stmtAudit->execute([
    ':uid'      => (int)$adminUser['id'],
    ':detalles' => json_encode([
        'target_user_id' => $targetId,
        'target_email'   => $targetUser['email'],
        'nuevo_activo'   => $nuevoEstado
    ], JSON_UNESCAPED_UNICODE),
    ':ip'       => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    ':ua'       => $_SERVER['HTTP_USER_AGENT'] ?? 'HaloMonitor-App'
]);

$estadoTexto = ($nuevoEstado === 1) ? 'activado' : 'desactivado';

jsonResponse(200, [
    'ok' => true,
    'message' => "Usuario '{$targetUser['nombre']}' ha sido {$estadoTexto} exitosamente.",
    'data' => [
        'id'     => $targetId,
        'activo' => $nuevoEstado
    ]
]);
