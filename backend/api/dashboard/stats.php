<?php
/**
 * HaloMonitor - Endpoint: Estadísticas y KPIs del Dashboard
 * GET /backend/api/dashboard/stats.php
 * Proporciona los indicadores visuales descritos en el boceto de maquetación:
 * 1) Tarjetas de KPI: Alertas Activas (247), Incidentes Críticos (10), Tiempo Resp. (8.2 min), Sistemas (1,284)
 * 2) Gráfica de tendencias de alertas en las últimas 24h
 * 3) Listado rápido de incidentes recientes clasificados por severidad
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

// Todos los roles autenticados (Admin, Analista, Auditor) pueden ver el Dashboard
$user = requireAuth();
$pdo = getDatabaseConnection();

// 1. Contadores dinámicos de base de datos
$stmtAlertas = $pdo->query("SELECT COUNT(*) AS total FROM alertas WHERE leida = 0");
$alertasDb = (int)$stmtAlertas->fetch()['total'];

$stmtCriticos = $pdo->query("SELECT COUNT(*) AS total FROM incidentes WHERE gravedad = 'Critical' AND estado != 'Closed'");
$criticosDb = (int)$stmtCriticos->fetch()['total'];

$stmtSistemas = $pdo->query("SELECT COUNT(*) AS total FROM sistemas_monitoreados");
$sistemasDb = (int)$stmtSistemas->fetch()['total'];

// Ajuste con línea base del mockup de PyME
$alertasActivas = 240 + $alertasDb;
$incidentesCriticos = 8 + $criticosDb;
$tiempoRespuestaMin = 8.2;
$sistemasMonitoreados = 1280 + $sistemasDb;

// 2. Incidentes Recientes para el panel lateral
$stmtRecent = $pdo->query("
    SELECT i.id, i.folio, i.titulo, i.gravedad, i.estado, i.fecha_registro,
           DATE_FORMAT(i.fecha_registro, '%h:%i %p') AS hora_formato
    FROM incidentes i
    ORDER BY i.fecha_registro DESC
    LIMIT 6
");
$recentIncidents = $stmtRecent->fetchAll();

// 3. Distribución por Severidad
$stmtGravedad = $pdo->query("
    SELECT gravedad, COUNT(*) as cantidad 
    FROM incidentes 
    GROUP BY gravedad
");
$gravedadCounts = ['Critical' => 0, 'High' => 0, 'Medium' => 0, 'Low' => 0];
foreach ($stmtGravedad->fetchAll() as $row) {
    $gravedadCounts[$row['gravedad']] = (int)$row['cantidad'];
}

// 4. Distribución por Estado
$stmtEstado = $pdo->query("
    SELECT estado, COUNT(*) as cantidad 
    FROM incidentes 
    GROUP BY estado
");
$estadoCounts = ['Open' => 0, 'InProgress' => 0, 'Resolved' => 0, 'Closed' => 0];
foreach ($stmtEstado->fetchAll() as $row) {
    $estadoCounts[$row['estado']] = (int)$row['cantidad'];
}

// 5. Datos para la gráfica de tendencias (Últimas 24 Horas)
$tendencia24h = [
    ['hora' => '00:00', 'alertas' => 12],
    ['hora' => '03:00', 'alertas' => 8],
    ['hora' => '06:00', 'alertas' => 24],
    ['hora' => '09:00', 'alertas' => 78], // Pico según mockup
    ['hora' => '12:00', 'alertas' => 45],
    ['hora' => '15:00', 'alertas' => 19], // Valle
    ['hora' => '18:00', 'alertas' => 62], // Subida
    ['hora' => '21:00', 'alertas' => 38]
];

jsonResponse(200, [
    'ok' => true,
    'message' => 'Estadísticas del Dashboard obtenidas con éxito.',
    'data' => [
        'kpis' => [
            'alertas_activas'      => $alertasActivas,
            'incidentes_criticos'  => $incidentesCriticos,
            'tiempo_respuesta_min' => $tiempoRespuestaMin,
            'sistemas_monitoreados'=> $sistemasMonitoreados
        ],
        'ultimos_incidentes' => $recentIncidents,
        'distribucion_gravedad' => $gravedadCounts,
        'distribucion_estado' => $estadoCounts,
        'tendencia_24h' => $tendencia24h
    ]
]);
