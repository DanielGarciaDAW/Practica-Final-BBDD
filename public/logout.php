<?php
// Iniciar la sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruir toda la sesión ---->todo cuando acabes la aplicación descomentar esto.
//session_unset();
//session_destroy();

// Redirigir al usuario a la página principal (index.php)
header("Location: index.php");
exit();

