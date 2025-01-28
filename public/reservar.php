<?php
include_once '../includes/conexionBBDD.php';
include_once '../includes/funciones.php';
include_once '../src/Casa.php';
include_once '../src/Habitacion.php';
include_once '../src/Reserva.php';

global $conexion;

echo "<h2>¡Bienvenido a reservas!</h2>";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo']) && isset($_POST['id'])) {
    // Configurar $_SESSION['estancia']
    $_SESSION['estancia'] = [
        'tipo' => $_POST['tipo'], // casa o habitacion
        'id' => intval($_POST['id']), // ID de la estancia
    ];
} else {
    // Si no se recibe un formulario válido, redirigir al dashboard
    header("Location: clientes_dashboard.php?error=datos_invalidos");
    exit();
}

if (isset($_SESSION['estancia'])) {
    $fechasReservadas = obtenerFechasReservadas(
        $conexion,
        $_SESSION['estancia']['id'],
        $_SESSION['estancia']['tipo']
    );
} else {
    $fechasReservadas = []; // No hay estancia, fechas reservadas vacío
    echo "Error: No se encontró la estancia seleccionada.";
}
//$fechasReservadas = obtenerFechasReservadas($conexion, $_SESSION['estancia']['id'], $_SESSION['estancia']['tipo']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['tipo']) && $_POST['tipo'] === 'casa') {

        $estancia = recogerCasa($conexion, $_POST['id']);

        if (!$estancia) {
            header("Location: clientes_dashboard.php?error=no_casa");
            exit();
        }
        $tipo = 'casa';
    } elseif (isset($_POST['tipo']) && $_POST['tipo'] === 'habitacion') {
        $estancia = recogerHabitacion($conexion, $_POST['id']);

        if (!$estancia) {
            header("Location: clientes_dashboard.php?error=no_habitacion");
            exit();
        }
        $tipo = 'habitacion';
    }
    //Guardar la estancia en la sesión.
    if (isset($estancia)) {
        $_SESSION['estancia'] = [
            'tipo' => $tipo,
            'id' => $estancia->getId(),
            'nombre' => $estancia->getNombre(),
            'disponible' => $estancia->isDisponible(),
            'capacidad' => $estancia->getCapacidad(),
            'precio' => $estancia->getPrecio()
        ];

        // Si es una habitación, guardamos también el número de habitación
        if ($tipo === 'habitacion') {
            $_SESSION['estancia']['numero_habitacion'] = $estancia->getNumeroHabitacion();
        }
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
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #fff;
        }
    </style>

</head>
<?php if (isset($estancia) && $estancia instanceof Casa): ?>
    <h2>Reservar Casa</h2>
    <p>Has seleccionado la casa: <?php echo htmlspecialchars($estancia->getNombre()); ?></p>
    <p>Precio por noche: <?php echo htmlspecialchars($estancia->getPrecio()); ?> €</p>
    <div id="calendar"></div>
    <form action="procesar_reserva.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($estancia->getId()); ?>">

        <input type="hidden" id="dias" name="dias">
        <input type="hidden" id="fecha_inicio" name="fecha_inicio">
        <input type="hidden" id="fecha_fin" name="fecha_fin">

        <!-- Contenedor para el precio total -->
        <p id="precio_total" style="font-weight: bold; color: green;">Selecciona un rango de fechas para calcular el precio total.</p>
        <button type="submit" name="confirmar_reserva_casa">Reservar Casa</button>
    </form>
    <form action="clientes_dashboard.php" method="get">
        <button type="submit">Cancelar Reserva</button>
    </form>
<?php elseif (isset($estancia) && $estancia instanceof Habitacion): ?>
    <h2>Reservar Habitación</h2>
    <p>Has seleccionado la habitación: <?php echo htmlspecialchars($estancia->getNombre()); ?></p>
    <p>Precio por noche: <?php echo htmlspecialchars($estancia->getPrecio()); ?> €</p>
    <div id="calendar"></div>
    <form action="procesar_reserva.php" method="post">
        <input type="hidden" id="fecha_inicio" name="fecha_inicio">
        <input type="hidden" id="fecha_fin" name="fecha_fin">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($estancia->getId()); ?>">
        <input type="hidden" id="dias" name="dias">

        <!-- Contenedor para el precio total -->
        <p id="precio_total" style="font-weight: bold; color: green;">Selecciona un rango de fechas para calcular el precio total.</p>
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

<script>
    const fechasReservadas = <?php echo json_encode($fechasReservadas); ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');

        // Convertir las fechas reservadas en eventos para FullCalendar
        const eventosReservados = fechasReservadas.map(fecha => ({
            title: 'No disponible',
            start: fecha,
            color: 'red',
            display: 'background'
        }));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            events: eventosReservados,
            selectable: true,
            selectHelper: true,
            validRange: {
                start: new Date().toISOString().split('T')[0],
            },
            select: function (info) {
                // Obtener las fechas seleccionadas
                const fechaInicio = info.startStr;
                const fechaFinAjustada = new Date(info.end);
                const fechaFinStr = fechaFinAjustada.toISOString().split('T')[0];



                // Calcular los días reservados
                const diasReservados = Math.ceil((fechaFinAjustada - new Date(info.start)) / (1000 * 60 * 60 * 24));

                // Obtener el precio por noche desde PHP
                const precioPorNoche = <?php echo json_encode($_SESSION['estancia']['precio'] ?? 0); ?>;


                // Calcular el precio total
                const precioTotal = diasReservados * precioPorNoche;


                // Mostrar el precio total en el elemento correspondiente
                document.getElementById('precio_total').textContent =
                    `Precio total: ${precioTotal.toFixed(2)} € (${diasReservados} noches a ${precioPorNoche.toFixed(2)} €/noche)`;

                // Actualizar los campos ocultos del formulario
                document.getElementById('fecha_inicio').value = fechaInicio;
                document.getElementById('fecha_fin').value = fechaFinStr;
                document.getElementById('dias').value = diasReservados;
            },
        });

        calendar.render();
    });
</script>




</body>
</html>
