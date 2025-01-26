<?php
//CONFIGURACIÓN DEL PROGRAMA EN SÍ.

// Ruta al archivo de logs
$logFile = __DIR__ . '/../logs/error_log.txt';

// Configurar PHP para enviar errores al archivo especificado
ini_set('log_errors', 'On');
ini_set('error_log', $logFile);

try {
    throw new Exception("Este es un error de prueba.");
} catch (Exception $e) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, $logFile);
}
