<?php
include_once '../includes/conexionBBDD.php';
include_once '../includes/funciones.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (!isset($_SESSION['estancia'])) {
    header('Location: clientes_dashboard.php');
    exit();
}


//Recuperamos los datos de la estancia seleccionada desde la sesión
$estanciaData = $_SESSION['estancia']; // Array con información de la estancia
$idEstancia = $estanciaData['id']; // ID de la casa o habitación
$tipoEstancia = $estanciaData['tipo']; // Tipo: 'casa' o 'habitacion'
$precioPorNoche = $estanciaData['precio']; // Precio por noche
$idCliente = $_SESSION['cliente']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fechaInicio = filter_input(INPUT_POST, 'fecha_inicio',FILTER_SANITIZE_STRING ); // Fecha de inicio
    $dias = filter_input(INPUT_POST, 'dias', FILTER_VALIDATE_INT); // Número de días

    if (!$fechaInicio || !$dias || $dias < 1){
        header('Location: reservar.php');
        exit();
    }

    //Calcular la fecha de fin y el precio total
    $fechaFin = date("Y-m-d", strtotime($fechaInicio . "+" . $dias . " days"));

    // Llamar a la función para procesar la reserva
    try{
        global $conexion;
        $idReserva = procesarReserva(
            $conexion,
            $idCliente,
            $fechaInicio,
            $fechaFin,
            $tipoEstancia,
            $idEstancia,
            $precioPorNoche,
            $dias
        );

        if ($idReserva) {
            // Redirigir al dashboard con un mensaje de éxito
            header('Location: clientes_dashboard.php?reserva=confirmada&id_reserva=' . $idReserva);
            exit;
        } else {
            // Redirigir con un mensaje de error
            header('Location: reservar.php?error=reservation_failed');
            exit;
        }
    }catch(Exception $e){
        error_log("Error al procesar la reserva: " . $e->getMessage());
        header('Location: reservar.php');
        exit();
    }
}
