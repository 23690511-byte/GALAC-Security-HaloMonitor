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
        return $pdo;
    } catch (PDOException $e) {
        // En caso de que se ejecute fuera de docker para pruebas locales, intentar con 127.0.0.1 y puertos 3307 / 3306
        if ($host === 'db') {
            foreach (['3307', '3306'] as $p) {
                try {
                    $dsnFallback = "mysql:host=127.0.0.1;port={$p};dbname={$dbname};charset={$charset}";
                    $pdo = new PDO($dsnFallback, $user, $pass, $options);
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
