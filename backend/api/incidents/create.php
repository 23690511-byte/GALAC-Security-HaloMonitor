<?php
/**
 * HaloMonitor - Endpoint: Registro de Nuevo Incidente de Seguridad
 * POST /backend/api/incidents/create.php
 * Aplica:
 * - RBAC: Administrador y Analista
 * - Identidad por sesión: $user['id']
 * - Prevención de duplicados activos (HTTP 409)
 * - Validación de campos obligatorios y fechas futuras (HTTP 422)
 * - Transacciones ACID y registro inmutable en historial_solicitud
 * - Congelamiento de datos operativos (precio_contratado / datos_congelados)
 * - Disparo automático de alertas para criticidad High / Critical (RF-05)
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/solicitudes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

// 1. Verificación de Roles y Sesión Segura (Administrador, Analista y Cliente PyME)
$user = requireRole(['Administrador', 'Analista', 'Cliente']);
$userId = (int)$user['id']; // Tomado estrictamente de la sesión

$input = readJson();

// 2. Validación de Campos Obligatorios (HTTP 422)
$titulo = trim($input['titulo'] ?? '');
$descripcion = trim($input['descripcion'] ?? '');
$categoria = trim($input['categoria'] ?? 'Malware');
$gravedad = trim($input['gravedad'] ?? 'Medium');
$sistemaId = !empty($input['sistema_id']) ? (int)$input['sistema_id'] : null;
$usuarioAsignadoId = !empty($input['usuario_asignado_id']) ? (int)$input['usuario_asignado_id'] : 2; // Analista SOC asignado por defecto
$costoEstimado = isset($input['costo_estimado']) ? (float)$input['costo_estimado'] : 2500.00;
$fechaLimite = !empty($input['fecha_limite_atencion']) ? trim($input['fecha_limite_atencion']) : null;

if (empty($titulo) || empty($descripcion)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Validación fallida: el título y la descripción del incidente son obligatorios.',
        'data' => null
    ]);
}

$gravedadesPermitidas = ['Low', 'Medium', 'High', 'Critical'];
if (!in_array($gravedad, $gravedadesPermitidas, true)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => "Nivel de gravedad inválido. Opciones válidas: [" . implode(', ', $gravedadesPermitidas) . "]",
        'data' => null
    ]);
}

// Validación de fecha futura si se suministra límite de atención (HTTP 422)
if ($fechaLimite) {
    $limiteTimestamp = strtotime($fechaLimite);
    if ($limiteTimestamp === false || $limiteTimestamp <= time()) {
        jsonResponse(422, [
            'ok' => false,
            'message' => 'Validación de fecha fallida: la fecha límite de atención debe ser una fecha/hora futura válida.',
            'data' => null
        ]);
    }
}

$pdo = getDatabaseConnection();

// 3. Prevención de Duplicados Activos (HTTP 409)
if ($sistemaId) {
    $stmtDup = $pdo->prepare("
        SELECT id, folio FROM incidentes 
        WHERE sistema_id = :sistema_id 
          AND titulo = :titulo 
          AND estado IN ('Open', 'InProgress') 
        LIMIT 1
    ");
    $stmtDup->execute([':sistema_id' => $sistemaId, ':titulo' => $titulo]);
    $duplicado = $stmtDup->fetch();
    if ($duplicado) {
        jsonResponse(409, [
            'ok' => false,
            'message' => "Conflicto: ya existe un incidente activo ({$duplicado['folio']}) para este sistema con el mismo título.",
            'data' => ['folio_existente' => $duplicado['folio']]
        ]);
    }
}

// Generación de Folio Único
$anio = date('Y');
$stmtCount = $pdo->query("SELECT COUNT(*) as total FROM incidentes");
$totalInc = (int)$stmtCount->fetch()['total'] + 1;
$folio = sprintf("INC-%s-%03d", $anio, $totalInc);

// Instantánea de Datos Congelados (Snapshot inmutable)
$datosCongelados = [
    'tarifa_hora_aplicada' => ($gravedad === 'Critical') ? 200.00 : 120.00,
    'sla_horas_establecido' => ($gravedad === 'Critical') ? 2 : (($gravedad === 'High') ? 6 : 24),
    'departamento_emisor'  => $user['departamento'],
    'creador_nombre'       => $user['nombre'],
    'creador_email'        => $user['email'],
    'fecha_congelacion'    => date('Y-m-d H:i:s')
];
$precioContratado = $costoEstimado;

// 4. Transacción ACID para asegurar integridad referencial y auditoría
try {
    $pdo->beginTransaction();

    $stmtInsert = $pdo->prepare("
        INSERT INTO incidentes 
        (folio, titulo, descripcion, categoria, gravedad, estado, sistema_id, usuario_reporta_id, usuario_asignado_id, costo_estimado, precio_contratado, datos_congelados, fecha_limite_atencion, fecha_registro)
        VALUES 
        (:folio, :titulo, :descripcion, :categoria, :gravedad, 'Open', :sistema_id, :usuario_reporta, :usuario_asig, :costo_est, :precio_cont, :datos_cong, :fecha_lim, NOW())
    ");

    $stmtInsert->execute([
        ':folio'          => $folio,
        ':titulo'         => $titulo,
        ':descripcion'    => $descripcion,
        ':categoria'      => $categoria,
        ':gravedad'       => $gravedad,
        ':sistema_id'     => $sistemaId,
        ':usuario_reporta'=> $userId,
        ':usuario_asig'   => $usuarioAsignadoId,
        ':costo_est'      => $costoEstimado,
        ':precio_cont'    => $precioContratado,
        ':datos_cong'     => json_encode($datosCongelados, JSON_UNESCAPED_UNICODE),
        ':fecha_lim'      => $fechaLimite
    ]);

    $incidenteId = (int)$pdo->lastInsertId();

    // Registro Inmutable en Auditoría (historial_solicitud)
    registrarHistorial(
        $pdo,
        $incidenteId,
        $userId,
        null,
        'Open',
        'Registro inicial y apertura de ticket de incidente de seguridad.',
        $datosCongelados
    );

    // Motor de Alertas Internas: Disparo automático si es High o Critical
    if (in_array($gravedad, ['High', 'Critical'], true)) {
        $stmtAlert = $pdo->prepare("
            INSERT INTO alertas (regla_id, incidente_id, titulo, mensaje, gravedad, leida, fecha_emision)
            VALUES (1, :incidente_id, :titulo, :mensaje, :gravedad, 0, NOW())
        ");
        $stmtAlert->execute([
            ':incidente_id' => $incidenteId,
            ':titulo'       => "¡Alerta Inmediata: {$titulo}!",
            ':mensaje'      => "Incidente {$folio} registrado con severidad [{$gravedad}]. Requiere atención prioritaria.",
            ':gravedad'     => $gravedad
        ]);
    }

    $pdo->commit();

    jsonResponse(201, [
        'ok' => true,
        'message' => "Incidente {$folio} registrado exitosamente con trazabilidad e historial inmutable.",
        'data' => [
            'id' => $incidenteId,
            'folio' => $folio,
            'estado' => 'Open',
            'gravedad' => $gravedad
        ]
    ]);

} catch (Exception $e) {
    rollbackJson($pdo, 500, 'Error crítico al procesar la transacción del incidente: ' . $e->getMessage());
}
