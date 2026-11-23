<?php
/**
 * HaloMonitor - Endpoint: Marcar Alerta(s) como Leída(s)
 * POST /backend/api/alerts/read.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

$user = requireRole(['Administrador', 'Analista']);
$input = readJson();
$id = isset($input['id']) ? (int)$input['id'] : 0;
$markAll = !empty($input['all']);

$pdo = getDatabaseConnection();

if ($markAll) {
    $stmt = $pdo->prepare("UPDATE alertas SET leida = 1 WHERE leida = 0");
    $stmt->execute();
    jsonResponse(200, [
        'ok' => true,
        'message' => 'Todas las alertas fueron marcadas como leídas.',
        'data' => null
    ]);
}

if ($id <= 0) {
    jsonResponse(422, ['ok' => false, 'message' => 'ID de alerta no válido.']);
}

$stmt = $pdo->prepare("UPDATE alertas SET leida = 1 WHERE id = :id");
$stmt->execute([':id' => $id]);

jsonResponse(200, [
    'ok' => true,
    'message' => 'Alerta marcada como leída.',
    'data' => ['id' => $id]
]);
