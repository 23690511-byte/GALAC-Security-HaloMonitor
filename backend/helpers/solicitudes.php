<?php
/**
 * HaloMonitor - Helper de Transacciones, Bloqueo de Concurrencia y Máquina de Estados
 * Cumple estrictamente con:
 * - Transacciones ACID (beginTransaction, commit, rollBack)
 * - SELECT ... FOR UPDATE (obtenerSolicitudBloqueada)
 * - rollbackJson() ante inconsistencias
 * - Registro inmutable en historial_solicitud
 * - Máquina de estados estricta (HTTP 409)
 * - Propiedad del recurso (HTTP 403)
 */

require_once __DIR__ . '/response.php';

/**
 * Obtiene el registro de un incidente bloqueando la fila con FOR UPDATE
 * evitando race conditions en operaciones concurrentes.
 */
function obtenerSolicitudBloqueada(PDO $pdo, int $id): ?array {
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
        FOR UPDATE
    ");
    $stmt->execute([':id' => $id]);
    $registro = $stmt->fetch();
    return $registro ?: null;
}

/**
 * Revierte la transacción activa de PDO y emite una respuesta JSON de error
 */
function rollbackJson(PDO $pdo, int $status, string $message, array $extra = []): void {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonResponse($status, array_merge([
        'ok' => false,
        'message' => $message,
        'data' => null
    ], $extra));
}

/**
 * Máquina de estados estricta para el ciclo de vida del incidente
 * Estados: 'Open', 'InProgress', 'Resolved', 'Closed'
 */
function validarTransicionEstado(string $estadoActual, string $nuevoEstado): bool {
    if ($estadoActual === $nuevoEstado) {
        return true; // No hay cambio de estado
    }

    $transicionesValidas = [
        'Open' => ['InProgress', 'Closed'],
        'InProgress' => ['Resolved', 'Open', 'Closed'],
        'Resolved' => ['Closed', 'InProgress'], // Reabrir a InProgress o cerrar definitivamente
        'Closed' => ['Open'] // Solo reapertura formal permitida
    ];

    if (!isset($transicionesValidas[$estadoActual])) {
        return false;
    }

    return in_array($nuevoEstado, $transicionesValidas[$estadoActual], true);
}

/**
 * Registra una entrada inmutable de auditoría en la tabla historial_solicitud
 */
function registrarHistorial(
    PDO $pdo,
    int $incidenteId,
    int $usuarioId,
    ?string $estadoAnterior,
    string $estadoNuevo,
    string $comentario,
    $datosCongelados = null
): int {
    $jsonCongelado = is_string($datosCongelados) ? $datosCongelados : json_encode($datosCongelados, JSON_UNESCAPED_UNICODE);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    $stmt = $pdo->prepare("
        INSERT INTO historial_solicitud 
        (incidente_id, usuario_id, estado_anterior, estado_nuevo, comentario, datos_congelados, ip_origen, fecha_registro)
        VALUES (:incidente_id, :usuario_id, :estado_ant, :estado_nue, :comentario, :congelados, :ip, NOW())
    ");
    
    $stmt->execute([
        ':incidente_id' => $incidenteId,
        ':usuario_id'   => $usuarioId,
        ':estado_ant'   => $estadoAnterior,
        ':estado_nue'   => $estadoNuevo,
        ':comentario'   => $comentario,
        ':congelados'   => $jsonCongelado,
        ':ip'           => $ip
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Valida la propiedad del recurso:
 * Un usuario con rol Auditor o cliente reporta solo puede gestionar o cancelar sus propios registros.
 * Administrador y Analista tienen permisos operativos globales.
 */
function verificarPropiedadRecurso(array $registro, array $user): void {
    $rol = $user['rol'] ?? '';
    if ($rol === 'Administrador' || $rol === 'Analista') {
        return; // Permiso operativo autorizado
    }

    $duenoId = (int)($registro['usuario_reporta_id'] ?? $registro['usuario_id'] ?? 0);
    $sesionId = (int)$user['id'];

    if ($duenoId !== $sesionId) {
        jsonResponse(403, [
            'ok' => false,
            'message' => 'Acceso denegado: No cuenta con permisos para modificar o cancelar incidentes creados por otro usuario.',
            'data' => null
        ]);
    }
}
