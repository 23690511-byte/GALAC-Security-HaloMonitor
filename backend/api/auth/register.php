<?php
/**
 * HaloMonitor - Endpoint: Autorregistro Público de Clientes PyME
 * POST /backend/api/auth/register.php
 *
 * REGLA DE SEGURIDAD CRÍTICA:
 * - Asigna FORZOSAMENTE desde el servidor el rol 'Cliente' (nunca acepta rol del frontend).
 * - Cifra la contraseña con bcrypt (password_hash).
 * - Guarda el registro con estado Activo (activo = 1) e inicia sesión automáticamente.
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

$input = readJson();
$nombre = trim($input['nombre'] ?? '');
$email = trim($input['email'] ?? '');
$departamento = trim($input['departamento'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';

if (empty($nombre) || empty($email) || empty($departamento) || empty($password)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Todos los campos son obligatorios: Razón Social/Nombre, Correo corporativo, Departamento/Área y Contraseña.',
        'data' => null
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'El formato del correo electrónico corporativo es inválido.',
        'data' => null
    ]);
}

if (strlen($password) < 8) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'La contraseña debe tener al menos 8 caracteres.',
        'data' => null
    ]);
}

if (!empty($confirmPassword) && $password !== $confirmPassword) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Las contraseñas no coinciden.',
        'data' => null
    ]);
}

$pdo = getDatabaseConnection();

// Validar que el correo no esté duplicado
$stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
$stmtCheck->execute([':email' => $email]);
if ($stmtCheck->fetch()) {
    jsonResponse(409, [
        'ok' => false,
        'message' => "El correo '{$email}' ya se encuentra registrado. Por favor inicia sesión.",
        'data' => null
    ]);
}

// Obtener el ID del rol 'Cliente' forzosamente desde el servidor
$stmtRole = $pdo->prepare("SELECT id, nombre FROM roles WHERE LOWER(nombre) = 'cliente' LIMIT 1");
$stmtRole->execute();
$roleRow = $stmtRole->fetch();
$clienteRolId = $roleRow ? (int)$roleRow['id'] : 4;
$clienteRolNombre = $roleRow ? $roleRow['nombre'] : 'Cliente';

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$stmtInsert = $pdo->prepare("
    INSERT INTO usuarios (nombre, email, password_hash, rol_id, activo, departamento, creado_en)
    VALUES (:nombre, :email, :hash, :rol_id, 1, :depto, NOW())
");
$stmtInsert->execute([
    ':nombre' => $nombre,
    ':email'  => $email,
    ':hash'   => $passwordHash,
    ':rol_id' => $clienteRolId,
    ':depto'  => $departamento
]);

$nuevoId = (int)$pdo->lastInsertId();

// Iniciar sesión automáticamente con el rol Cliente
$userSessionData = [
    'id'           => $nuevoId,
    'nombre'       => $nombre,
    'email'        => $email,
    'rol_nombre'   => $clienteRolNombre,
    'rol_id'       => $clienteRolId,
    'departamento' => $departamento
];
loginUserSession($userSessionData);

jsonResponse(201, [
    'ok' => true,
    'message' => 'Cuenta de Cliente PyME registrada e iniciada exitosamente.',
    'data' => [
        'user' => [
            'id'           => $nuevoId,
            'nombre'       => $nombre,
            'email'        => $email,
            'rol'          => $clienteRolNombre,
            'rol_id'       => $clienteRolId,
            'departamento' => $departamento
        ]
    ]
]);
