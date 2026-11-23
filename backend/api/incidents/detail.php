<?php
/**
 * HaloMonitor - Endpoint: Detalle e Historial de un Incidente
 * GET /backend/api/incidents/detail.php?id=1
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireAuth();
$id = (int)getQueryParam('id', 0);

if ($id <= 0) {
    jsonResponse(422, ['ok' => false, 'message' => 'Parámetro de ID inválido.']);
}

$pdo = getDatabaseConnection();

$stmt = $pdo->prepare("
    SELECT i.*, 
           u_rep.nombre AS reporta_nombre, u_rep.email AS reporta_email,
           u_asig.nombre AS asignado_nombre, u_asig.email AS asignado_email,
           sm.nombre AS sistema_nombre, sm.ip_host AS sistema_ip
    FROM incidentes i
    LEFT JOIN usuarios u_rep ON i.usuario_reporta_id = u_rep.id
    LEFT JOIN usuarios u_asig ON i.usuario_asignado_id = u_asig.id
    LEFT JOIN sistemas_monitoreados sm ON i.sistema_id = sm.id
    WHERE i.id = :id
");
$stmt->execute([':id' => $id]);
$incidente = $stmt->fetch();

if (!$incidente) {
    jsonResponse(404, ['ok' => false, 'message' => 'Incidente no encontrado.']);
}

// Historial cronológico de cambios (Auditoría inmutable)
$stmtHist = $pdo->prepare("
    SELECT h.id, h.estado_anterior, h.estado_nuevo, h.comentario, h.datos_congelados,
           h.ip_origen, h.fecha_registro,
           u.nombre AS usuario_nombre, u.email AS usuario_email, r.nombre AS usuario_rol
    FROM historial_solicitud h
    INNER JOIN usuarios u ON h.usuario_id = u.id
    INNER JOIN roles r ON u.rol_id = r.id
    WHERE h.incidente_id = :id
    ORDER BY h.fecha_registro ASC, h.id ASC
");
$stmtHist->execute([':id' => $id]);
$historial = $stmtHist->fetchAll();

foreach ($historial as &$h) {
    if (!empty($h['datos_congelados']) && is_string($h['datos_congelados'])) {
        $h['datos_congelados'] = json_decode($h['datos_congelados'], true);
    }
}
unset($h);

if (!empty($incidente['datos_congelados']) && is_string($incidente['datos_congelados'])) {
    $incidente['datos_congelados'] = json_decode($incidente['datos_congelados'], true);
}

jsonResponse(200, [
    'ok' => true,
    'message' => 'Detalle del incidente recuperado.',
    'data' => [
        'incidente' => $incidente,
        'historial' => $historial
    ]
]);
