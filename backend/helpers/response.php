<?php
/**
 * HaloMonitor - Helper de Respuestas HTTP Estandarizadas
 * Requisito: jsonResponse($status, ['ok' => ..., 'message' => ..., 'data' => ...])
 */

function jsonResponse(int $status, array $payload): void {
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
    }
    
    // Asegurar estructura estándar { ok: bool, message: string, data: mixed }
    if (!isset($payload['ok'])) {
        $payload['ok'] = ($status >= 200 && $status < 300);
    }
    if (!isset($payload['message'])) {
        $payload['message'] = $payload['ok'] ? 'Operación exitosa' : 'Ocurrió un error en la solicitud';
    }
    if (!array_key_exists('data', $payload)) {
        $payload['data'] = null;
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
