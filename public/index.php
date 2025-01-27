<?php

// Incluimos la configuración y conexión a la base de datos
require_once '../config/config.php';
require_once '../includes/conexionBBDD.php';
require_once '../includes/funciones.php';
include_once '../src/Casa.php';
include_once '../src/Habitacion.php';

global $conexion;

// Verificar si la sesión está activa y, si no, iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//session_unset();
//session_destroy();


if (!empty($_SESSION['errores'])) {
    echo '<div style="color: red;">';
    foreach ($_SESSION['errores'] as $error) {
        echo $error . '<br>';
    }
    echo '</div>';
    unset($_SESSION['errores']);
}

// Establecer un indicador en la sesión para confirmar acceso desde index.php
$_SESSION['desde_index'] = true;

// Comprobar si la instalación está completa
if (!isset($_SESSION['instalacion_completada']) && (!isset($config['instalacion_completada']) || $config['instalacion_completada'] === false)) {
    header('Location: ../config/install.php');
    exit();
}

// Conexión a la base de datos usando PDO
try {
    $casas = recogerCasas($conexion);
} catch (Exception $e) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
    echo "Ocurrió un problema al cargar las casas. Por favor, intenta nuevamente más tarde.";
}
try {
    $habitaciones = recogerHabitaciones($conexion);
} catch (Exception $e) {
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, "../logs/error_log.txt");
    echo "Ocurrió un problema al cargar las habitaciones. Por favor, intenta nuevamente más tarde.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casas y Habitaciones</title>
    <style>
        body {
            display: grid;
            grid-template-areas:
                "header header"
                "main main";
            grid-template-rows: auto 1fr;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            grid-area: header;
            background-color: #f8f9fa;
            padding: 1em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        main {
            grid-area: main;
            padding: 1em;
            overflow-y: auto;
        }

        .auth-buttons button {
            margin-left: 10px;
            padding: 0.5em 1em;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login {
            background-color: #007bff;
            color: white;
        }

        .register {
            background-color: #28a745;
            color: white;
        }

        .casa {
            border: 1px solid #ddd;
            padding: 1em;
            margin-bottom: 1em;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #form-container {
            margin-top: 1em;
            padding: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        #form-container form {
            display: flex;
            flex-direction: column;
        }

        #form-container label {
            margin-bottom: 0.5em;
        }

        #form-container input {
            padding: 0.8em;
            margin-bottom: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }

        #form-container button {
            padding: 0.8em;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            font-size: 1em;
            cursor: pointer;
        }
    </style>
</head>
<body>
<header>
    <div>Casa Rural</div>
    <div>Bienvenido</div>
    <div class="auth-buttons">
        <!-- Botones para mostrar los formularios -->
        <button class="login" id="loginButton">Login</button>
        <button class="register" id="registerButton">Registrar</button>
    </div>
</header>

<main>
    <div id="list-container">
        <!-- Contenedor para las casas y habitaciones -->
        <h2>Casas disponibles</h2>
        <?php if (!empty($casas)): ?>
            <?php foreach ($casas as $casa): ?>

                <div class="casa">
                    <h3><?php echo htmlspecialchars($casa['nombre']); ?></h3>
                    <p>Capacidad: <?php echo htmlspecialchars($casa['capacidad']); ?></p>
                    <p><strong>Precio: </strong> <?php echo htmlspecialchars($casa['precio']); ?>€</p>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay casas disponibles.</p>
        <?php endif; ?>

        <h2>Habitaciones disponibles</h2>
        <?php if (!empty($habitaciones)): ?>
            <?php foreach ($habitaciones as $habitacion): ?>

                <div class="casa">
                    <h3><?php echo htmlspecialchars($habitacion['nombre']); ?></h3>
                    <p>Capacidad: <?php echo htmlspecialchars($habitacion['capacidad']); ?></p>
                    <p><strong>Precio: </strong> <?php echo htmlspecialchars($habitacion['precio']); ?>€ por noche
                    </p>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay habitaciones disponibles.</p>
        <?php endif; ?>
    </div>

    <div id="form-container" style="display: none;"></div> <!-- Contenedor para los formularios -->
</main>

<script>
    // Obtener los elementos de los botones, contenedores y listas
    const formContainer = document.getElementById('form-container');
    const loginButton = document.getElementById('loginButton');
    const registerButton = document.getElementById('registerButton');
    const listContainer = document.getElementById('list-container');

    // Formulario de Login
    const loginForm = `
        <form action="login.php" method="POST">
            <h2>Iniciar Sesión</h2>
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required placeholder="Ingresa tu usuario">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
            <button type="submit">Iniciar Sesión</button>
        </form>
    `;

    // Formulario de Registro
    const registerForm = `
        <form action="registrar.php" method="POST">
            <h2>Registro</h2>
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ingresa tu nombre completo">

            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required placeholder="Crea tu usuario">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required placeholder="Crea tu contraseña">

            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="email" required placeholder="Ingresa tu correo">

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required placeholder="Ingresa tu teléfono (10-15 dígitos)">

            <button type="submit" name="registrar">Registrar</button>
        </form>
    `;

    // Mostrar el formulario de Login y ocultar las listas
    loginButton.addEventListener('click', () => {
        listContainer.style.display = 'none'; // Ocultar la lista de casas y habitaciones
        formContainer.style.display = 'block'; // Mostrar el contenedor del formulario
        formContainer.innerHTML = loginForm; // Mostrar el formulario de login
    });

    // Mostrar el formulario de Registro y ocultar las listas
    registerButton.addEventListener('click', () => {
        listContainer.style.display = 'none'; // Ocultar la lista de casas y habitaciones
        formContainer.style.display = 'block'; // Mostrar el contenedor del formulario
        formContainer.innerHTML = registerForm; // Mostrar el formulario de registro
    });
</script>
</body>
</html>
