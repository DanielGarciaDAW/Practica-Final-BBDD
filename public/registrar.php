<?php
// Incluir la conexión a la base de datos
require_once '../includes/conexionBBDD.php';
include_once '../src/Cliente.php';

global $conexion;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $errores = [];
    //Validar Nombre
    if (empty($_POST['nombre']) || strlen($_POST['nombre']) < 3) {
        $errores[] = 'Nombre es requerido';
    }
    //Validar usuario
    if (empty($_POST['usuario']) || strlen($_POST['usuario']) < 3) {
        $errores[] = 'Usuario es requerido';
    }
    if (empty($_POST['password']) || strlen($_POST['password']) < 8) {
        $errores[] = 'Password es requerido y debe contener al menos 8 caracteres';
    }
    if (empty($_POST['email'])) {
        $errores[] = 'El campo de email es requerido.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del email no es válido.';
    }
    if (empty($_POST['telefono']) || strlen($_POST['telefono']) < 9) {
        $errores[] = 'El campo telefono es requerido';
    }

    // Si hay errores, detener el procesamiento y mostrarlos
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header('Location: index.php');
        exit();
    }

    $cliente = new Cliente(
        null,
        trim($_POST['nombre']),
        trim($_POST['usuario']),
        trim($_POST['password']),
        trim($_POST['email']),
        trim($_POST['telefono'])

    );
    $cliente->guardar($conexion);
    $_SESSION['cliente'] = $cliente;
    header('Location: clientes_dashboard.php');
    exit();
}