<?php
/**
 * HaloMonitor - Endpoint: Listado de Incidentes
 * GET /backend/api/incidents/index.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireAuth();
$pdo = getDatabaseConnection();

$search = getQueryParam('q', '');
$gravedad = getQueryParam('gravedad', '');
$estado = getQueryParam('estado', '');

$sql = "
    SELECT i.id, i.folio, i.titulo, i.descripcion, i.categoria, i.gravedad, i.estado,
           i.sistema_id, i.usuario_reporta_id, i.usuario_asignado_id,
           i.costo_estimado, i.precio_contratado, i.datos_congelados,
           i.fecha_limite_atencion, i.fecha_registro, i.fecha_cierre,
           u_rep.nombre AS reporta_nombre, u_rep.email AS reporta_email,
           u_asig.nombre AS asignado_nombre, u_asig.email AS asignado_email,
           sm.nombre AS sistema_nombre, sm.ip_host AS sistema_ip
    FROM incidentes i
    LEFT JOIN usuarios u_rep ON i.usuario_reporta_id = u_rep.id
    LEFT JOIN usuarios u_asig ON i.usuario_asignado_id = u_asig.id
    LEFT JOIN sistemas_monitoreados sm ON i.sistema_id = sm.id
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $sql .= " AND (i.folio LIKE :search OR i.titulo LIKE :search OR i.categoria LIKE :search OR sm.nombre LIKE :search)";
    $params[':search'] = "%{$search}%";
}

if (!empty($gravedad)) {
    $sql .= " AND i.gravedad = :gravedad";
    $params[':gravedad'] = $gravedad;
}

if (!empty($estado)) {
    $sql .= " AND i.estado = :estado";
    $params[':estado'] = $estado;
}

$sql .= " ORDER BY i.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$incidentes = $stmt->fetchAll();

// Decodificar JSON de datos_congelados si viene como string
foreach ($incidentes as &$inc) {
    if (!empty($inc['datos_congelados']) && is_string($inc['datos_congelados'])) {
        $inc['datos_congelados'] = json_decode($inc['datos_congelados'], true);
    }
}
unset($inc);

jsonResponse(200, [
    'ok' => true,
    'message' => 'Incidentes recuperados exitosamente.',
    'data' => [
        'total' => count($incidentes),
        'incidentes' => $incidentes
    ]
]);
