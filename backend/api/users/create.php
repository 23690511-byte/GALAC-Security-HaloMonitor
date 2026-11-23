<?php
/**
 * HaloMonitor - Endpoint: Creación de Usuarios (Exclusivo Administrador)
 * POST /backend/api/users/create.php
 */

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['ok' => false, 'message' => 'Método no permitido. Utilice POST.']);
}

// Exclusivo Administrador
$adminUser = requireRole(['Administrador']);

$input = readJson();
$nombre = trim($input['nombre'] ?? '');
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');
$rolId = isset($input['rol_id']) ? (int)$input['rol_id'] : 4; // Por defecto Cliente
$departamento = trim($input['departamento'] ?? 'Operaciones');

if (empty($nombre) || empty($email) || empty($password)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'Campos obligatorios incompletos: Nombre, Correo electrónico y Contraseña son requeridos.',
        'data' => null
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(422, [
        'ok' => false,
        'message' => 'El formato del correo electrónico es inválido.',
        'data' => null
    ]);
}

$pdo = getDatabaseConnection();

// Verificar si el correo ya existe
$stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
$stmtCheck->execute([':email' => $email]);
if ($stmtCheck->fetch()) {
    jsonResponse(409, [
        'ok' => false,
        'message' => "Conflicto: Ya existe un usuario registrado con el correo '{$email}'.",
        'data' => null
    ]);
}

// Cifrado de contraseña con bcrypt
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$stmtInsert = $pdo->prepare("
    INSERT INTO usuarios (nombre, email, password_hash, rol_id, activo, departamento, creado_en)
    VALUES (:nombre, :email, :pass, :rol_id, 1, :depto, NOW())
");

$stmtInsert->execute([
    ':nombre'   => $nombre,
    ':email'    => $email,
    ':pass'     => $passwordHash,
    ':rol_id'   => $rolId,
    ':depto'    => $departamento
]);

$nuevoId = (int)$pdo->lastInsertId();

jsonResponse(201, [
    'ok' => true,
    'message' => "Usuario '{$nombre}' creado exitosamente con rol asignado.",
    'data' => [
        'id' => $nuevoId,
        'nombre' => $nombre,
        'email' => $email,
        'rol_id' => $rolId
    ]
]);
