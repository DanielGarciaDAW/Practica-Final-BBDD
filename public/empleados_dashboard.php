<?php
// Incluir la conexión a la base de datos
require_once '../includes/conexionBBDD.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$empleado = $_SESSION['empleado'];

//Saludamos que siempre está bien.
echo "<p>¡Bienvenido empleado, " . htmlspecialchars($empleado['nombre']) . "!</p>";