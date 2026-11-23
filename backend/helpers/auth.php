<?php
/**
 * HaloMonitor - Control de Autenticación, Sesiones y RBAC
 * Requisitos:
 * - Control de sesiones
 * - requireRole(['ROL_1', 'ROL_2']) bloqueando acceso directo según rol
 * - Identidad por sesión del servidor ($user['id']), nunca de URL o body
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/response.php';

function getCurrentUser(): ?array {
    if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        return null;
    }
    return $_SESSION['user'];
}

function requireAuth(): array {
    $user = getCurrentUser();
    if (!$user) {
        jsonResponse(401, [
            'ok' => false,
            'message' => 'No autorizado. Debe iniciar sesión en HaloMonitor para continuar.',
            'data' => null
        ]);
    }
    return $user;
}

function requireRole(array $allowedRoles): array {
    $user = requireAuth();
    $userRole = $user['rol'] ?? '';

    // Coincidencia exacta insensible a mayúsculas
    $authorized = false;
    foreach ($allowedRoles as $role) {
        if (strcasecmp($role, $userRole) === 0) {
            $authorized = true;
            break;
        }
    }

    if (!$authorized) {
        jsonResponse(403, [
            'ok' => false,
            'message' => "Acceso denegado. Se requiere uno de los siguientes roles: [" . implode(', ', $allowedRoles) . "]. Su rol actual es '{$userRole}'.",
            'data' => null
        ]);
    }

    return $user;
}

function loginUserSession(array $userRecord): void {
    // Regenerar ID de sesión para prevenir Session Fixation
    session_regenerate_id(true);
    
    $_SESSION['user'] = [
        'id'           => (int)$userRecord['id'],
        'nombre'       => $userRecord['nombre'],
        'email'        => $userRecord['email'],
        'rol'          => $userRecord['rol_nombre'] ?? $userRecord['rol'] ?? 'Analista',
        'rol_id'       => (int)($userRecord['rol_id'] ?? 2),
        'departamento' => $userRecord['departamento'] ?? 'Ciberseguridad',
        'logged_at'    => date('Y-m-d H:i:s')
    ];
}

function logoutUserSession(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
