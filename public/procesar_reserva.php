<?php
include_once '../includes/conexionBBDD.php';
include_once '../includes/funciones.php';
include_once '../src/Casa.php';
include_once '../src/Habitacion.php';
include_once '../src/Reserva.php';

if (session_status() == PHP_SESSION_NONE){
    session_start();
}

if (isset($_SESSION['estancia'])) {
    $estanciaData = $_SESSION['estancia'];

    if ($estanciaData['tipo'] === 'casa') {
        $estancia = new Casa(
            $estanciaData['id'],
            $estanciaData['nombre'],
            $estanciaData['disponible'],
            $estanciaData['capacidad'],
            $estanciaData['precio']
        );
    } elseif ($estanciaData['tipo'] === 'habitacion') {
        $estancia = new Habitacion(
            $estanciaData['id'],
            $estanciaData['nombre'],
            $estanciaData['disponible'],
            $estanciaData['capacidad'],
            $estanciaData['precio'],
            $estanciaData['numero_habitacion'] ?? null // Incluye el número si está disponible
        );
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirmar_reserva_casa'])) {
        echo "Acabas de reservar uan casa";
    }elseif (isset($_POST['confirmar_reserva_habitacion'])) {
        echo "Acabas de reservar habitacion";
    }
}
