<?php
/**
 * HaloMonitor - Endpoint: Listado de Alertas del Sistema
 * GET /backend/api/alerts/index.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireRole(['Administrador', 'Analista']);
$pdo = getDatabaseConnection();

$leida = getQueryParam('leida', '');
$gravedad = getQueryParam('gravedad', '');

$sql = "
    SELECT a.id, a.regla_id, a.incidente_id, a.titulo, a.mensaje, a.gravedad, a.leida, a.fecha_emision,
           i.folio AS incidente_folio, i.estado AS incidente_estado,
           r.nombre AS regla_nombre
    FROM alertas a
    LEFT JOIN incidentes i ON a.incidente_id = i.id
    LEFT JOIN reglas_alertas r ON a.regla_id = r.id
    WHERE 1=1
";
$params = [];

if ($leida !== '') {
    $sql .= " AND a.leida = :leida";
    $params[':leida'] = (int)$leida;
}

if (!empty($gravedad)) {
    $sql .= " AND a.gravedad = :gravedad";
    $params[':gravedad'] = $gravedad;
}

$sql .= " ORDER BY a.fecha_emision DESC, a.id DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$alertas = $stmt->fetchAll();

// Conteo de no leídas
$stmtCount = $pdo->query("SELECT COUNT(*) AS unread FROM alertas WHERE leida = 0");
$unreadCount = (int)$stmtCount->fetch()['unread'];

jsonResponse(200, [
    'ok' => true,
    'message' => 'Alertas recuperadas con éxito.',
    'data' => [
        'unread_count' => $unreadCount,
        'total' => count($alertas),
        'alertas' => $alertas
    ]
]);
