<?php
return [
    // Configuración del programa
    'log_file' => __DIR__ . '/../logs/error_log.txt', // Ruta al archivo de logs
    'instalacion_completada' => false,               // Cambiar a true al completar la instalación
    'modo_debug' => true,                            // Cambiar a false para desactivar depuración
    'log_errors' => true,                            // Activar o desactivar registro de errores
];

/*
try {
    throw new Exception("Este es un error de prueba.");
} catch (Exception $e) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, $logFile);
}
*/