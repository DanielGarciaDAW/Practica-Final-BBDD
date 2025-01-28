<?php
// Incluimos la conexión a la base de datos
require_once '../includes/conexionBBDD.php';

// Conexión global
global $conexion;

// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mostrar mensaje de conexión si está disponible
if (isset($_SESSION['mensajeConexion'])) {
    $mensaje = $_SESSION['mensajeConexion'];
    unset($_SESSION['mensajeConexion']);
    //echo '<script>console.log("' . $mensaje . '");</script>';
}

/**
 * Inicializa la base de datos creando las tablas necesarias.
 */
function inicializarBaseDeDatos() {
    // Declaramos la conexión global dentro de la función
    global $conexion;

    if (!$conexion) {
        die("Error: La conexión a la base de datos no está configurada.");
    }

    echo '<script>console.log("Iniciando Base de datos");</script>';

    // Creación de la tabla `clientes`
    try {
        echo '<script>console.log("Creando Tabla Clientes");</script>';
        $sqlClientes = "
            CREATE TABLE IF NOT EXISTS clientes (
                id INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único
                nombre VARCHAR(255) NOT NULL,              -- Nombre completo
                usuario VARCHAR(255) UNIQUE NOT NULL,             -- Nombre de usuario
                email VARCHAR(255) UNIQUE NOT NULL,       -- Correo único
                password VARCHAR(255) NOT NULL,          -- Contraseña encriptada
                telefono VARCHAR(15),                      -- Teléfono
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        $conexion->query($sqlClientes);
        echo '<script>console.log("Tabla Clientes creada");</script>';
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] Error en tabla Clientes: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');
    }

    // Creación de la tabla `empleados`
    try {
        echo '<script>console.log("Creando Tabla Empleados");</script>';
        $sqlEmpleados = "
            CREATE TABLE IF NOT EXISTS empleados (
                id INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único
                nombre VARCHAR(255) NOT NULL,               -- Nombre completo
                usuario VARCHAR(255) UNIQUE NOT NULL,             -- Nombre de usuario
                password VARCHAR(255) NOT NULL,           -- Contraseña encriptada
                fecha_contrato DATE NOT NULL,               -- Fecha de contratación
                puesto VARCHAR(255) NOT NULL               -- Puesto o jerarquía
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        $conexion->query($sqlEmpleados);
        echo '<script>console.log("Tabla Empleados creada");</script>';
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] Error en tabla Empleados: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');
    }

    // Creación de la tabla `casas`
    try {
        echo '<script>console.log("Creando Tabla Casas");</script>';
        $sqlCasas = "
            CREATE TABLE IF NOT EXISTS casas (
                id INT AUTO_INCREMENT PRIMARY KEY,     -- Identificador único
                nombre VARCHAR(255) UNIQUE NOT NULL,              -- Nombre de la casa
                disponible BOOLEAN DEFAULT TRUE,           -- Disponibilidads
                capacidad INT NOT NULL,
                precio INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        $conexion->query($sqlCasas);
        echo '<script>console.log("Tabla Casas creada");</script>';
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] Error en tabla Casas: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');
    }

    // Creación de la tabla `habitaciones`
    try {
        echo '<script>console.log("Creando Tabla Habitaciones");</script>';
        $sqlHabitaciones = "
            CREATE TABLE IF NOT EXISTS habitaciones (
                id INT AUTO_INCREMENT PRIMARY KEY, -- Identificador único
                nombre VARCHAR(255) UNIQUE NOT NULL,         -- Nombre de la habitación
                numero_habitacion INT UNIQUE NOT NULL,              -- Número de habitación
                disponible BOOLEAN DEFAULT TRUE,             -- Disponibilidad
                capacidad INT NOT NULL,
                precio INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        $conexion->query($sqlHabitaciones);
        echo '<script>console.log("Tabla Habitaciones creada");</script>';
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] Error en tabla Habitaciones: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');
    }

    // Creación de la tabla `reservas`
    try {
        echo '<script>console.log("Creando Tabla Reservas");</script>';
        $sqlReservas = "
        CREATE TABLE reservas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_cliente INT NOT NULL,
        id_casa INT DEFAULT NULL,         -- Relación con casas (opcional)
        id_habitacion INT DEFAULT NULL,   -- Relación con habitaciones (opcional)
        fecha_reserva DATETIME NOT NULL,
        fecha_inicio DATE NOT NULL,
        fecha_fin DATE NOT NULL,
        precio_total DECIMAL(10, 2) NOT NULL, -- Precio total de la reserva
        estado ENUM('Pendiente', 'Confirmada', 'Cancelada') DEFAULT 'Pendiente',
        FOREIGN KEY (id_cliente) REFERENCES clientes(id),
        FOREIGN KEY (id_casa) REFERENCES casas(id),
        FOREIGN KEY (id_habitacion) REFERENCES habitaciones(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ";

        $conexion->query($sqlReservas);
        echo '<script>console.log("Tabla Reservas creada correctamente");</script>';
    } catch (PDOException $e) {
        error_log("[" . date("Y-m-d H:i:s") . "] Error en tabla Reservas: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/error_log.txt');
    }

}

// Llamada a la función para inicializar la base de datos
inicializarBaseDeDatos();
