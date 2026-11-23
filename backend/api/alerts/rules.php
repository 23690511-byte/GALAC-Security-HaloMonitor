<?php
/**
 * HaloMonitor - Endpoint: Reglas de Alertas
 * GET / POST /backend/api/alerts/rules.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$pdo = getDatabaseConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    requireAuth();
    $stmt = $pdo->query("
        SELECT r.*, u.nombre AS creador_nombre 
        FROM reglas_alertas r
        LEFT JOIN usuarios u ON r.creado_por = u.id
        ORDER BY r.id ASC
    ");
    $reglas = $stmt->fetchAll();

    jsonResponse(200, [
        'ok' => true,
        'message' => 'Reglas de alertas obtenidas.',
        'data' => $reglas
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = requireRole(['Administrador']);
    $input = readJson();

    $nombre = trim($input['nombre'] ?? '');
    $criterio = trim($input['criterio_gravedad'] ?? 'High');

    if (empty($nombre)) {
        jsonResponse(422, ['ok' => false, 'message' => 'El nombre de la regla es obligatorio.']);
    }

    $stmt = $pdo->prepare("
        INSERT INTO reglas_alertas (nombre, criterio_gravedad, disparar_notificacion, activa, creado_por)
        VALUES (:nombre, :criterio, 1, 1, :creado_por)
    ");
    $stmt->execute([
        ':nombre'   => $nombre,
        ':criterio' => $criterio,
        ':creado_por' => (int)$user['id']
    ]);

    jsonResponse(201, [
        'ok' => true,
        'message' => 'Regla de alerta creada exitosamente.',
        'data' => ['id' => $pdo->lastInsertId()]
    ]);
}

jsonResponse(405, ['ok' => false, 'message' => 'Método no soportado.']);
