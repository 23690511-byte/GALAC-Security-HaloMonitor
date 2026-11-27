<?php
/**
 * HaloMonitor - Endpoint: Reportes Consolidados y Auditoría
 * GET /backend/api/reports/index.php
 * Integra métricas de calidad según los estándares MoProSoft y CMMI (PDF 3)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

$user = requireRole(['Administrador', 'Analista', 'Auditor']);
$pdo = getDatabaseConnection();

// 1. Reporte Completo de Incidentes con tiempos de resolución
$stmt = $pdo->query("
    SELECT i.id, i.folio, i.titulo, i.categoria, i.gravedad, i.estado,
           i.costo_estimado, i.precio_contratado,
           i.fecha_registro, i.fecha_cierre, i.fecha_limite_atencion,
           u_rep.nombre AS reporta_nombre,
           u_asig.nombre AS asignado_nombre,
           sm.nombre AS sistema_nombre,
           TIMESTAMPDIFF(MINUTE, i.fecha_registro, COALESCE(i.fecha_cierre, NOW())) AS duracion_minutos
    FROM incidentes i
    LEFT JOIN usuarios u_rep ON i.usuario_reporta_id = u_rep.id
    LEFT JOIN usuarios u_asig ON i.usuario_asignado_id = u_asig.id
    LEFT JOIN sistemas_monitoreados sm ON i.sistema_id = sm.id
    ORDER BY i.id DESC
");
$incidentesReport = $stmt->fetchAll();

// 2. Historial Global de Auditoría (Trazabilidad estricta) - Solo Administrador y Auditor
$historialAudit = [];
if ($user['rol'] === 'Administrador' || $user['rol'] === 'Auditor') {
    $stmtAudit = $pdo->query("
        SELECT h.id, h.incidente_id, h.estado_anterior, h.estado_nuevo, h.comentario,
               h.ip_origen, h.fecha_registro,
               i.folio AS incidente_folio,
               u.nombre AS usuario_nombre,
               r.nombre AS usuario_rol
        FROM historial_solicitud h
        JOIN incidentes i ON h.incidente_id = i.id
        JOIN usuarios u ON h.usuario_id = u.id
        JOIN roles r ON u.rol_id = r.id
        ORDER BY h.fecha_registro DESC
        LIMIT 200
    ");
    $historialAudit = $stmtAudit->fetchAll();
}

// 3. Indicadores de Calidad MoProSoft / CMMI Nivel 2 y 3 (Gestión & Medición Cuantitativa)
$totalIncidentes = count($incidentesReport);
$resueltos = 0;
$cumplimientoSLA = 0;
$tiempoTotalMinutos = 0;

foreach ($incidentesReport as $inc) {
    if (in_array($inc['estado'], ['Resolved', 'Closed'], true)) {
        $resueltos++;
        $tiempoTotalMinutos += (int)$inc['duracion_minutos'];
        if (!empty($inc['fecha_limite_atencion']) && !empty($inc['fecha_cierre'])) {
            if (strtotime($inc['fecha_cierre']) <= strtotime($inc['fecha_limite_atencion'])) {
                $cumplimientoSLA++;
            }
        } else {
            $cumplimientoSLA++;
        }
    }
}

$slaRate = ($resueltos > 0) ? round(($cumplimientoSLA / $resueltos) * 100, 1) : 100.0;
$tiempoPromedioResolucionMin = ($resueltos > 0) ? round($tiempoTotalMinutos / $resueltos, 1) : 45.0;

jsonResponse(200, [
    'ok' => true,
    'message' => 'Reporte consolidado generado con éxito.',
    'data' => [
        'resumen_calidad' => [
            'total_incidentes'     => $totalIncidentes,
            'incidentes_resueltos' => $resueltos,
            'sla_cumplimiento_pct' => $slaRate,
            'tiempo_promedio_min'  => $tiempoPromedioResolucionMin,
            'estandar_referencia'  => 'MoProSoft Nivel 2 / CMMI Nivel 3 - Métricas de Proceso de Software'
        ],
        'incidentes' => $incidentesReport,
        'auditoria'  => $historialAudit
    ]
]);
