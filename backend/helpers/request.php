<?php
/**
 * HaloMonitor - Helper de Entrada y Solicitudes HTTP
 * Requisito: readJson() y estandarización de entrada
 */

function readJson(): array {
    $rawInput = file_get_contents('php://input');
    if (empty($rawInput)) {
        return [];
    }

    $decoded = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        require_once __DIR__ . '/response.php';
        jsonResponse(400, [
            'ok' => false,
            'message' => 'Cuerpo de solicitud JSON inválido o malformado: ' . json_last_error_msg(),
            'data' => null
        ]);
    }

    return is_array($decoded) ? $decoded : [];
}

function getQueryParam(string $name, $default = null) {
    if (!isset($_GET[$name])) {
        return $default;
    }
    return trim((string)$_GET[$name]);
}
