<?php

// Incluir la conexión a la base de datos
require_once '../includes/conexionBBDD.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

global $conexion;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Obtener los datos enviados
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    try {
        //1. Buscamos en tabla clientes.
        $stmt = $conexion->prepare ("SELECT * FROM empleados WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empleado) {
            //Verificar la contraseña para empleado
            if (password_verify($password, $empleado['password'])) {
                $_SESSION['empleado'] = [
                    'id' => $empleado['id'],
                    'nombre' => $empleado['nombre'],
                    'usuario' => $empleado['usuario'],
                    'puesto' => $empleado['puesto'],
                ];

                //Redirigir a la página de empleados
                header ('Location: empleados_dashboard.php');
                exit();
            } else {
                echo "<p>Contraseña incorrecta para empleado.</p>";
                header ('Location: login.php');
                exit();
            }
        }

        //2. Buscamos en tabla clientes.
        $stmt = $conexion->prepare ("SELECT * FROM clientes WHERE usuario = :usuario");
        $stmt->execute([':usuario' => $usuario]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cliente) {
            if (password_verify($password, $cliente['password'])) {
                $_SESSION['cliente'] = [
                    'id' => $cliente['id'],
                    'nombre' => $cliente['nombre'],
                    'usuario' => $cliente['usuario'],
                ];
                //Saludamos por si de caso.
                echo "<p>¡Bienvenido cliente, " . htmlspecialchars($cliente['nombre']) . "!</p>";
                header('Location: clientes_dashboard.php');
                exit();
            } else {
                echo "<p>Contraseña incorrecta para cliente.</p>";
                header ('Location: login.php');
                exit();
            }
        }


    } catch (PDOException $e) {
        echo "<p>Error al iniciar sesión: " . $e->getMessage() . "</p>";
    }

}