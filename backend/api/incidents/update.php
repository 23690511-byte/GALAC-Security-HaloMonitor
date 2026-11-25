<?php
/**
 * HaloMonitor - Endpoint: Actualización de Estado y Asignación de Incidente
 * POST /backend/api/incidents/update.php (o PUT)
 * Aplica:
 * - RBAC: Administrador y Analista (Auditor bloqueado con 403)
 * - Identidad estricta por sesión: $user['id']
 * - Bloqueo de concurrencia: SELECT ... FOR UPDATE (obtenerSolicitudBloqueada)
 * - Transacciones ACID (beginTransaction, commit, rollBack)
 * - Validación de propiedad del recurso si no es admin/analista
 * - Máquina de estados estricta con HTTP 409
 * - Trazabilidad inmutable en historial_solicitud
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/solicitudes.php';

// Aceptamos POST o PUT
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'PATCH'], true)) {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST, PUT o PATCH.']);
}

$user = requireRole(['Administrador', 'Analista', 'Cliente']);
$userId = (int)$user['id'];

$input = readJson();
$incidenteId = isset($input['id']) ? (int)$input['id'] : 0;
$nuevoEstado = isset($input['estado']) ? trim($input['estado']) : null;
$nuevoAsignadoId = isset($input['usuario_asignado_id']) ? (int)$input['usuario_asignado_id'] : null;
$comentario = trim($input['comentario'] ?? '');
$nuevaGravedad = isset($input['gravedad']) ? trim($input['gravedad']) : null;

// Validación de privilegios de Cliente: Solo puede cancelar sus tickets
if ($user['rol'] === 'Cliente') {
    if ($nuevoEstado !== 'Closed') {
        jsonResponse(403, [
            'ok' => false,
            'message' => 'Acceso denegado: El cliente únicamente puede solicitar la cancelación de sus propios tickets.',
            'data' => null
        ]);
    }
}

if ($incidenteId <= 0) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Identificador de incidente requerido y debe ser mayor a 0.',
        'data' => null
    ]);
}

if (empty($comentario)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Por motivos de auditoría y trazabilidad, el comentario de la acción es obligatorio.',
        'data' => null
    ]);
}

$pdo = getDatabaseConnection();

try {
    // Iniciar Transacción ACID
    $pdo->beginTransaction();

    // Bloqueo pesimista de concurrencia con SELECT ... FOR UPDATE
    $incidente = obtenerSolicitudBloqueada($pdo, $incidenteId);

    if (!$incidente) {
        rollbackJson($pdo, 404, "El incidente con ID {$incidenteId} no fue encontrado.");
    }

    // Validación de Propiedad del Recurso
    verificarPropiedadRecurso($incidente, $user);

    $estadoActual = $incidente['estado'];
    $estadoFinal = $nuevoEstado ?: $estadoActual;

    // Validación de Máquina de Estados Estricta (HTTP 409 si es inválida)
    if (!validarTransicionEstado($estadoActual, $estadoFinal)) {
        rollbackJson($pdo, 409, "Conflicto en máquina de estados: La transición de '{$estadoActual}' a '{$estadoFinal}' no es permitida.");
    }

    $asignadoFinal = ($nuevoAsignadoId !== null) ? $nuevoAsignadoId : $incidente['usuario_asignado_id'];
    $gravedadFinal = ($nuevaGravedad !== null) ? $nuevaGravedad : $incidente['gravedad'];
    $fechaCierre = ($estadoFinal === 'Closed') ? date('Y-m-d H:i:s') : $incidente['fecha_cierre'];

    // Snapshot inmutable congelado para esta actualización
    $snapshotActualizacion = [
        'estado_previo'   => $estadoActual,
        'estado_result'   => $estadoFinal,
        'actualizado_por' => $user['nombre'],
        'rol_operador'    => $user['rol'],
        'motivo_cambio'   => $comentario,
        'timestamp'       => date('Y-m-d H:i:s')
    ];

    // Actualizar registro del incidente
    $stmtUpdate = $pdo->prepare("
        UPDATE incidentes
        SET estado = :estado,
            usuario_asignado_id = :asignado,
            gravedad = :gravedad,
            fecha_cierre = :cierre
        WHERE id = :id
    ");

    $stmtUpdate->execute([
        ':estado'   => $estadoFinal,
        ':asignado' => $asignadoFinal,
        ':gravedad' => $gravedadFinal,
        ':cierre'   => $fechaCierre,
        ':id'       => $incidenteId
    ]);

    // Registro Inmutable en Auditoría (historial_solicitud)
    registrarHistorial(
        $pdo,
        $incidenteId,
        $userId,
        $estadoActual,
        $estadoFinal,
        $comentario,
        $snapshotActualizacion
    );

    // Confirmar transacción
    $pdo->commit();

    jsonResponse(200, [
        'ok' => true,
        'message' => "Incidente {$incidente['folio']} actualizado correctamente a estado '{$estadoFinal}'.",
        'data' => [
            'id' => $incidenteId,
            'folio' => $incidente['folio'],
            'estado_anterior' => $estadoActual,
            'estado_nuevo' => $estadoFinal,
            'usuario_asignado_id' => $asignadoFinal
        ]
    ]);

} catch (Exception $e) {
    rollbackJson($pdo, 500, 'Error crítico al procesar la actualización del incidente: ' . $e->getMessage());
}
