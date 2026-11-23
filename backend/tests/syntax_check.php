<?php
/**
 * HaloMonitor - Verificador de Integridad y Sintaxis de Endpoints PHP
 * Ejecuta verificación de sintaxis sobre cada archivo .php del backend.
 */

$rootDir = realpath(__DIR__ . '/..');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));
$phpFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

echo "========================================\n";
echo " HALOMONITOR - PHP SYNTAX LINTER VERIFIER\n";
echo "========================================\n";
echo "Archivos PHP encontrados: " . count($phpFiles) . "\n\n";

$errors = 0;
foreach ($phpFiles as $filePath) {
    $relativePath = str_replace($rootDir, '', $filePath);
    $output = [];
    $returnVar = 0;
    exec("php -l " . escapeshellarg($filePath), $output, $returnVar);

    if ($returnVar === 0) {
        echo "[OK]  {$relativePath}\n";
    } else {
        echo "[ERR] {$relativePath}\n";
        echo "      " . implode("\n      ", $output) . "\n";
        $errors++;
    }
}

echo "\nResultado final: " . ($errors === 0 ? "TODOS LOS ARCHIVOS PASARON LA VERIFICACIÓN." : "SE ENCONTRARON {$errors} ERRORES.") . "\n";
exit($errors === 0 ? 0 : 1);
