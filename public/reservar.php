<?php
include_once '../includes/conexionBBDD.php';
include_once '../includes/funciones.php';
include_once '../src/Casa.php';
include_once '../src/Habitacion.php';
include_once '../src/Reserva.php';

global $conexion;

echo "¡Bienvenido a reservas!";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['casa'])) {
        $estancia = recogerCasa($conexion, $_POST['id']);
        $tipo = 'casa';
    } elseif (isset($_POST['habitacion'])) {
        $estancia = recogerHabitacion($conexion, $_POST['id']);
        $tipo = 'habitacion';
    }

    $_SESSION['estancia'] = [
        'tipo' => $tipo,
        'id' => $estancia->getId(),
        'nombre' => $estancia->getNombre(),
        'disponible'=> $estancia->isDisponible(),
        'capacidad' => $estancia->getCapacidad(),
        'precio' => $estancia->getPrecio()
    ];

    // Si es una habitación, guardamos también el número de habitación
    if ($tipo === 'habitacion') {
        $_SESSION['estancia']['numero_habitacion'] = $estancia->getNumeroHabitacion();
    }
} else {
    echo "No se encontró la estancia seleccionada.";
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservar</title>
</head>
<body>
<?php if ($estancia instanceof Casa): ?>
    <h2>Reservar Casa</h2>
    <p>Has seleccionado la casa: <?php echo htmlspecialchars($estancia->getNombre()); ?></p>
    <p>Precio por noche: <?php echo htmlspecialchars($estancia->getPrecio()); ?> €</p>
    <form action="procesar_reserva.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($estancia->getId()); ?>">
        <label for="dias">¿Cuántos días quieres reservar?</label>
        <input type="number" id="dias" name="dias" min="1" required>
        <button type="submit" name="confirmar_reserva_casa">Reservar Casa</button>
    </form>
    <form action="clientes_dashboard.php" method="get">
        <button type="submit">Cancelar Reserva</button>
    </form>
<?php elseif ($estancia instanceof Habitacion): ?>
    <h2>Reservar Habitación</h2>
    <p>Has seleccionado la habitación: <?php echo htmlspecialchars($estancia->getNombre()); ?></p>
    <p>Precio por noche: <?php echo htmlspecialchars($estancia->getPrecio()); ?> €</p>
    <form action="procesar_reserva.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($estancia->getId()); ?>">
        <label for="dias">¿Cuántos días quieres reservar?</label>
        <input type="number" id="dias" name="dias" min="1" required>
        <button type="submit" name="confirmar_reserva_habitacion">Reservar habitación</button>
    </form>
    <form action="clientes_dashboard.php" method="get">
        <button type="submit">Cancelar Reserva</button>
    </form>
<?php else: ?>
    <p>No se ha seleccionado una estancia válida para reservar.</p>
    <form action="clientes_dashboard.php" method="get">
        <button type="submit">Volver al Dashboard</button>
    </form>
<?php endif; ?>

</body>
</html>
