<?php
/**
 * HaloMonitor - Conexión Segura a Base de Datos (PDO)
 * GALAC Security - Conexión mediante PDO con consultas preparadas para prevenir inyección SQL.
 */

require_once __DIR__ . '/app.php';

function getDatabaseConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'halomonitor_db';
    $user = getenv('DB_USER') ?: 'halouser';
    $pass = getenv('DB_PASSWORD') ?: 'HaloPass2026!';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // Consultas preparadas reales nativas
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        ensureDbIntegrity($pdo);
        return $pdo;
    } catch (PDOException $e) {
        // En caso de que se ejecute fuera de docker para pruebas locales, intentar con 127.0.0.1 y puertos 3307 / 3306
        if ($host === 'db') {
            foreach (['3307', '3306'] as $p) {
                try {
                    $dsnFallback = "mysql:host=127.0.0.1;port={$p};dbname={$dbname};charset={$charset}";
                    $pdo = new PDO($dsnFallback, $user, $pass, $options);
                    ensureDbIntegrity($pdo);
                    return $pdo;
                } catch (PDOException $fallbackEx) {
                    // Continuar probando
                }
            }
        }

        // Salida limpia de error de infraestructura
        http_response_code(503);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'ok' => false,
            'message' => 'Error de conexión a la base de datos de HaloMonitor: ' . $e->getMessage(),
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Garantiza que el rol Cliente y la cuenta demo cliente@pyme.com existan en volúmenes persistentes
 * y repara cadenas UTF-8 doblemente codificadas (ej. ExfiltraciÃ³n -> Exfiltración).
 */
function ensureDbIntegrity(PDO $pdo): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    try {
        // 1. Asegurar los 4 roles base RBAC
        $pdo->exec("
            INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
            (1, 'Administrador', 'Acceso total al sistema, gestión de usuarios, incidentes, alertas y bitácora'),
            (2, 'Analista', 'Operación SOC: atención de incidentes, alertas y reportes operativos'),
            (3, 'Auditor', 'Solo lectura ejecutiva: supervisión de incidentes, métricas y bitácora SQL'),
            (4, 'Cliente', 'Cliente PyME: Portal exclusivo de reporte de nuevos incidentes')
        ");

        // 2. Asegurar los 5 usuarios iniciales solicitados (con password_hash bcrypt)
        $seedUsers = [
            [
                'nombre'       => 'Gerardo Axel Castillo Balderas',
                'email'        => 'admin@halomonitor.com',
                'password'     => 'Admin123!',
                'rol_id'       => 1,
                'departamento' => 'Dirección / SOC'
            ],
            [
                'nombre'       => 'Luis Roberto Martínez Méndez',
                'email'        => 'roberto.analista@halomonitor.com',
                'password'     => 'Analista123!',
                'rol_id'       => 2,
                'departamento' => 'Operaciones SOC'
            ],
            [
                'nombre'       => 'Luis Antonio Ponce Navarro',
                'email'        => 'antonio.analista@halomonitor.com',
                'password'     => 'Analista123!',
                'rol_id'       => 2,
                'departamento' => 'Arquitectura / SOC'
            ],
            [
                'nombre'       => 'Carlos Reyes Manuel',
                'email'        => 'carlos.auditor@halomonitor.com',
                'password'     => 'Auditor123!',
                'rol_id'       => 3,
                'departamento' => 'Calidad y Cumplimiento'
            ],
            [
                'nombre'       => 'Empresa PyME Demo (Contabilidad)',
                'email'        => 'cliente@pymedemo.com',
                'password'     => 'Cliente123!',
                'rol_id'       => 4,
                'departamento' => 'Contabilidad / PyME'
            ]
        ];

        $stmtCheckUser = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmtInsertUser = $pdo->prepare("
            INSERT INTO usuarios (nombre, email, password_hash, rol_id, activo, departamento, creado_en)
            VALUES (:nombre, :email, :hash, :rol_id, 1, :depto, NOW())
        ");
        $stmtUpdateAdmin = $pdo->prepare("
            UPDATE usuarios SET nombre = :nombre, departamento = :depto WHERE email = :email
        ");

        foreach ($seedUsers as $su) {
            $stmtCheckUser->execute([':email' => $su['email']]);
            $existing = $stmtCheckUser->fetch();
            if (!$existing) {
                $stmtInsertUser->execute([
                    ':nombre' => $su['nombre'],
                    ':email'  => $su['email'],
                    ':hash'   => password_hash($su['password'], PASSWORD_BCRYPT),
                    ':rol_id' => $su['rol_id'],
                    ':depto'  => $su['departamento']
                ]);
            } elseif ($su['email'] === 'admin@halomonitor.com') {
                $stmtUpdateAdmin->execute([
                    ':nombre' => $su['nombre'],
                    ':depto'  => $su['departamento'],
                    ':email'  => $su['email']
                ]);
            }
        }

        // 2. Reparar textos UTF-8 doblemente codificados en tablas principales (si existen)
        $repairs = [
            'incidentes'            => ['titulo', 'descripcion', 'categoria'],
            'alertas'               => ['titulo', 'mensaje'],
            'historial_solicitud'   => ['comentario'],
            'reglas_alertas'        => ['nombre'],
            'sistemas_monitoreados' => ['nombre', 'tipo'],
            'usuarios'              => ['nombre', 'departamento'],
            'roles'                 => ['descripcion']
        ];

        foreach ($repairs as $table => $cols) {
            foreach ($cols as $col) {
                $pdo->exec("
                    UPDATE `{$table}`
                    SET `{$col}` = CONVERT(BINARY(CONVERT(`{$col}` USING latin1)) USING utf8mb4)
                    WHERE `{$col}` LIKE '%Ã%' OR `{$col}` LIKE '%Â%'
                ");
            }
        }
    } catch (Throwable $e) {
        // Silencioso para no interrumpir la operación normal
    }
}
