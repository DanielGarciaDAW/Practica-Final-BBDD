<?php

// Incluimos la configuración y conexión a la base de datos
require_once '../config/config.php';
require_once '../includes/conexionBBDD.php';

// Verificar si la sesión está activa y, si no, iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//Descomenta para borrar daots de sesión y comenzar de nuevo
//session_unset();
//session_destroy();
// Establecer un indicador en la sesión para confirmar acceso desde index.php
$_SESSION['desde_index'] = true;

// Comprobar si la instalación está completa
if (!isset($_SESSION['instalacion_completada'])) {
    header('Location: ../config/install.php');
    exit();
}

// Mostrar mensaje si la instalación está completa
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación Completa</title>
</head>
<body>
<h1>Instalación Correcta</h1>
<form action="login.php" method="POST">
    <label for="usuario">Usuario:</label>
    <input type="text" name="usuario" id="usuario" required>
    <br><br>
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>
    <br><br>
    <button type="submit">Iniciar Sesión</button>
</form>
</body>
</html>
