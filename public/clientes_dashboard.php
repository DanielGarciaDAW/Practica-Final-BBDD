<?php

include_once '../includes/conexionBBDD.php';
include_once '../includes/funciones.php';


global $conexion;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el cliente está logueado
if (!isset($_SESSION['cliente'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos usando PDO
try{
    $casas = recogerCasas($conexion);
}catch (Exception $e){
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, "../logs/error_log.txt" );
    echo "Ocurrió un problema al cargar las casas. Por favor, intenta nuevamente más tarde.";
}
try{
    $habitaciones = recogerHabitaciones($conexion);
}catch (Exception $e){
    error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . "\n", 3, "../logs/error_log.txt" );
    echo "Ocurrió un problema al cargar las habitaciones. Por favor, intenta nuevamente más tarde.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Dashboard</title>
    <style>
        body {
            display: grid;
            grid-template-areas:
                "header header"
                "aside main";
            grid-template-columns: 1fr 3fr;
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
        aside {
            grid-area: aside;
            background-color: #e9ecef;
            padding: 1em;
            display: flex;
            flex-direction: column;
            gap: 1em;
        }
        button, input[type="text"] {
            padding: 0.5em;
            font-size: 1em;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .casa, .habitacion {
            border: 1px solid #ddd;
            padding: 1em;
            margin-bottom: 1em;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<header>
    <div>Casa Rural</div>
    <div>Bienvenido, <?php echo htmlspecialchars($_SESSION['cliente']['nombre']); ?></div>
    <form method="post" action="logout.php" style="margin: 0;">
        <button class="logout-btn">Logout</button>
    </form>
</header>

<aside>
    <input type="text" placeholder="Buscar casas...">
    <button>Perfil</button>
    <button>Reservas</button>
    <button>Historial de Reservas</button>
</aside>

<main>
    <h2>Casas disponibles</h2>
    <?php if (!empty($casas)): ?>
        <?php $hayCasas = false; ?>
        <?php foreach ($casas as $casa): ?>
            <?php if ($casa['disponible'] == 1): ?>
                <div class="casa">
                    <h3><?php echo htmlspecialchars($casa['nombre']); ?></h3>
                    <p>Capacidad: <?php echo htmlspecialchars($casa['capacidad']); ?></p>
                    <p><strong>Precio: </strong> <?php echo htmlspecialchars(($casa['precio'])) ?> € por noche</p>
                    <form action="reservar.php" method="post">
                        <input type="hidden" name="casa" value="casa">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($casa['id']); ?>">
                        <button type="submit" name="reservar_casa">Reservar</button>
                    </form>
                </div>
                <?php $hayCasas = true; ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!$hayCasas): ?>
            <p>No hay casas disponibles.</p>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Habitaciones disponibles</h2>
    <?php if (!empty($habitaciones)): ?>
        <?php $hayHabitaciones = false; ?>
        <?php foreach ($habitaciones as $habitacion): ?>
            <?php if ($habitacion['disponible'] == 1): ?>
                <div class="habitacion">
                    <h3><?php echo htmlspecialchars($habitacion['nombre']); ?></h3>
                    <p>Capacidad: <?php echo htmlspecialchars($habitacion['capacidad']); ?></p>
                    <p><strong>Precio: </strong> <?php echo htmlspecialchars(($habitacion['precio'])) ?> € por noche</p>
                    <form action="reservar.php" method="post">
                        <input type="hidden" name="habitacion" value="habitacion">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($habitacion['id']); ?>">
                        <button type="submit" name="reservar_habitacion">Reservar</button>
                    </form>
                </div>
                <?php $hayHabitaciones = true; ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!$hayHabitaciones): ?>
            <p>No hay casas disponibles</p>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>
