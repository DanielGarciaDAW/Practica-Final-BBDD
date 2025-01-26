<?php
// Incluir la conexión a la base de datos
require_once '../includes/conexionBBDD.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
global $conexion;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    try {
        // 1. Buscar al usuario en la tabla de empleados
        $stmt = $conexion->prepare("SELECT * FROM empleados WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($empleado) {
            // Verificar la contraseña para empleado
            if (password_verify($password, $empleado['password'])) {
                // Iniciar sesión como empleado
                $_SESSION['empleado'] = [
                    'id' => $empleado['id'],
                    'nombre' => $empleado['nombre'],
                    'usuario' => $empleado['usuario'],
                    'puesto' => $empleado['puesto'],
                ];

                echo "<p>¡Bienvenido empleado, " . htmlspecialchars($empleado['nombre']) . "!</p>";
                // Redirigir a la página de empleados
                header('Location: empleados_dashboard.php');
                exit();
            } else {
                echo "<p>Contraseña incorrecta para empleado.</p>";
                exit();
            }
        }

        // 2. Buscar al usuario en la tabla de clientes
        $stmt = $conexion->prepare("SELECT * FROM clientes WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            // Verificar la contraseña para cliente
            if (password_verify($password, $cliente['password'])) {
                // Iniciar sesión como cliente
                $_SESSION['cliente'] = [
                    'id' => $cliente['id'],
                    'nombre' => $cliente['nombre'],
                    'usuario' => $cliente['usuario'],

                ];

                echo "<p>¡Bienvenido cliente, " . htmlspecialchars($cliente['nombre']) . "!</p>";
                // Redirigir a la página de clientes
                header('Location: clientes_dashboard.php');
                exit();
            } else {
                echo "<p>Contraseña incorrecta para cliente.</p>";
                exit();
            }
        }

        // 3. Si no se encuentra ni en empleados ni en clientes
        echo "<p>El usuario no existe.</p>";
    } catch (PDOException $e) {
        echo "<p>Error al iniciar sesión: " . $e->getMessage() . "</p>";
    }
}
?>
