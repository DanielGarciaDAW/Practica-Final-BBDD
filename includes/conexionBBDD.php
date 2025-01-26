<?php
// Iniciamos sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Credenciales de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "casarural";

try {
    // Creamos la conexión usando PDO
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mensaje de éxito para depuración
    $_SESSION['mensajeConexion'] = "Conexión creada correctamente " . date("Y-m-d H:i:s");

} catch (PDOException $e) {
    // Registro de error en un archivo
    error_log("[" . date("Y-m-d H:i:s") . "] Error de conexión a la base de datos: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');

    // Mensaje de error para la sesión
    $_SESSION['mensajeConexion'] = "La conexión a la BBDD ha fallado. Por favor, revisa los logs.";
    die("Error al conectar con la base de datos.");
}
?>