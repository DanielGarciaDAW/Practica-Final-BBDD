<?php
// Incluimos la configuración, la conexión a la base de datos y las clases necesarias
require_once 'config.php';
require_once 'crearBBDD.php';
require_once '../includes/conexionBBDD.php';
require_once '../src/Casa.php';
require_once '../src/Habitacion.php';
require_once '../src/Empleado.php';

global $conexion;

// Verificar si la sesión está activa y, si no, iniciarla
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la instalación ya está completada
if (isset($_SESSION['instalacion_completada'])) {
    header('Location: ../public/index.php'); // Redirigir al sistema principal
    exit();
}

// Verificar si este archivo fue accedido desde index.php
if (!isset($_SESSION['desde_index'])) {
    die('Acceso no autorizado. Por favor, inicia la instalación desde index.php.');
}

// Crear las tablas necesarias para la base de datos
try {
    inicializarBaseDeDatos();
    echo '<h1>Instalación Inicial</h1>';
    echo '<p>Las tablas de la base de datos se han creado correctamente.</p>';
} catch (Exception $e) {
    echo '<p>Error durante la creación de las tablas: ' . $e->getMessage() . '</p>';
    exit();
}

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion']; // Determina qué acción seleccionó el usuario

    try {
        switch ($accion) {
            case 'registrar_casa':
                // Validar campos
                $errores = [];

                // Validar nombre de la casa
                if (empty($_POST['nombre_casa']) || strlen($_POST['nombre_casa']) < 3) {
                    $errores[] = "El nombre de la casa debe tener al menos 3 caracteres.";
                }

                // Validar capacidad de la casa
                if (!isset($_POST['capacidad_casa']) || !is_numeric($_POST['capacidad_casa']) || $_POST['capacidad_casa'] <= 0) {
                    $errores[] = "La capacidad debe ser un número mayor a 0.";
                }
                if (!isset($_POST['precio_casa']) || !is_numeric($_POST['precio_casa']) || $_POST['precio_casa'] <= 0) {
                    $errores[] = "El precio debe ser mayor que 0.";
                }

                // Si hay errores, detener el procesamiento y mostrarlos
                if (!empty($errores)) {
                    echo '<p>Error al registrar la casa:</p>';
                    echo '<ul>';
                    foreach ($errores as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul>';
                    break; // Salir del `case`
                }

                // Registrar una nueva casa
                $casa = new Casa(
                    null,
                    $_POST['nombre_casa'],
                    true,
                    $_POST['capacidad_casa'],
                    $_POST['precio_casa']
                );
                $casa->guardar($conexion);
                echo '<p>Casa registrada con éxito.</p>';
                break;

            case 'registrar_habitacion':
                //Validar campos primero
                $errores = [];

                // Validar nombre de la habitación
                if (empty($_POST['nombre_habitacion']) || strlen($_POST['nombre_habitacion']) < 3) {
                    $errores[] = "El nombre de la habitación debe tener al menos 3 caracteres.";
                }
                // Validar capacidad
                if (!isset($_POST['capacidad_habitacion']) || !is_numeric($_POST['capacidad_habitacion']) || $_POST['capacidad_habitacion'] <= 0) {
                    $errores[] = "La capacidad debe ser un número mayor a 0.";
                }
                // Validar número de habitación
                if (!isset($_POST['numero_habitacion']) || !is_numeric($_POST['numero_habitacion']) || $_POST['numero_habitacion'] <= 0) {
                    $errores[] = "El número de la habitación debe ser un número entero positivo.";
                }
                //Validar precio habitacion
                if (!isset($_POST['precio_habitacion']) || !is_numeric($_POST['precio_habitacion']) || $_POST['precio_habitacion'] <= 0) {
                    $errores[] = "El precio debe ser mayor que 0.";
                }
                // Si hay errores, detener el procesamiento y mostrarlos
                if (!empty($errores)) {
                    echo '<p>Error al registrar la habitación:</p>';
                    echo '<ul>';
                    foreach ($errores as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul>';
                    break; // Salir del `case`
                }


                // Registrar una nueva habitación
                $habitacion = new Habitacion(
                    null,
                    $_POST['nombre_habitacion'],
                    true, // Por defecto, disponible
                    $_POST['capacidad_habitacion'],
                    $_POST['precio_habitacion'],
                    $_POST['numero_habitacion']

                );
                $habitacion->guardar($conexion);
                echo '<p>Habitación registrada con éxito.</p>';
                break;

            case 'registrar_empleado':
                // Validar campos
                $errores = [];

                // Validar nombre del empleado
                if (empty($_POST['nombre_empleado']) || strlen($_POST['nombre_empleado']) < 3) {
                    $errores[] = "El nombre del empleado debe tener al menos 3 caracteres.";
                }

                // Validar usuario del empleado
                if (empty($_POST['usuario_empleado']) || strlen($_POST['usuario_empleado']) < 3) {
                    $errores[] = "El usuario del empleado debe tener al menos 3 caracteres.";
                }

                // Validar contraseña del empleado
                if (empty($_POST['password_empleado']) || strlen($_POST['password_empleado']) < 8) {
                    $errores[] = "La contraseña debe tener al menos 8 caracteres.";
                }

                // Validar fecha de contrato
                if (empty($_POST['fecha_contrato_empleado']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['fecha_contrato_empleado'])) {
                    $errores[] = "La fecha de contrato debe tener un formato válido (YYYY-MM-DD).";
                } elseif (strtotime($_POST['fecha_contrato_empleado']) > time()) {
                    $errores[] = "La fecha de contrato no puede ser una fecha futura.";
                }

                // Validar puesto del empleado
                $puestos_validos = ['Gerente', 'Recepcionista', 'Limpieza', 'Mantenimiento'];
                if (empty($_POST['puesto_empleado']) || !in_array($_POST['puesto_empleado'], $puestos_validos)) {
                    $errores[] = "El puesto del empleado no es válido. Seleccione una opción válida.";
                }

                // Si hay errores, detener el procesamiento y mostrarlos
                if (!empty($errores)) {
                    echo '<p>Error al registrar el empleado:</p>';
                    echo '<ul>';
                    foreach ($errores as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul>';
                    break; // Salir del `case`
                }

                // Registrar un nuevo empleado
                $empleado = new Empleado(
                    null,
                    trim($_POST['nombre_empleado']),
                    trim($_POST['usuario_empleado']),
                    trim($_POST['password_empleado']),
                    trim($_POST['fecha_contrato_empleado']),
                    trim($_POST['puesto_empleado'])
                );
                $empleado->guardar($conexion);
                echo '<p>Empleado registrado con éxito.</p>';
                break;

            case 'terminar_instalacion':
                // Guardar la configuración en el archivo config.php
                actualizarConfiguracion('instalacion_completada', true, '../config.php');
                $_SESSION['instalacion_completada'] = true;

                // Limpiar la variable de sesión `desde_index`
                $_SESSION['desde_index'] = null;
                unset($_SESSION['desde_index']);

                // Redirigir al sistema principal
                header('Location: ../public/index.php');
                exit();

            default:
                echo '<p>Acción no reconocida.</p>';
        }
    } catch (Exception $e) {
        echo '<p>Error al procesar la acción: ' . $e->getMessage() . '</p>';
    }
}
function actualizarConfiguracion($clave, $valor, $archivo = '../config.php') {
    // Leer la configuración existente
    $config = include $archivo;

    // Actualizar el valor de la clave
    $config[$clave] = $valor;

    // Guardar los cambios de vuelta al archivo
    file_put_contents($archivo, '<?php return ' . var_export($config, true) . ';');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación Inicial</title>
</head>
<body>
<h1>Instalación Inicial</h1>
<p>Selecciona qué datos deseas agregar:</p>

<form method="POST">
    <label for="accion">Elige una acción:</label>
    <select name="accion" id="accion" required>
        <option value="null">Seleccione una opción:</option>
        <option value="registrar_casa">Registrar Casa</option>
        <option value="registrar_habitacion">Registrar Habitación</option>
        <option value="registrar_empleado">Registrar Empleado</option>
        <option value="terminar_instalacion">Terminar Instalación</option>
    </select>
    <br><br>

    <!-- Contenedor para el formulario dinámico -->
    <div id="formulario"></div>
    <br>

    <button type="submit">Enviar</button>
</form>

<script>
    // JavaScript para mostrar el formulario según la acción seleccionada
    document.getElementById('accion').addEventListener('change', function () {
        const accion = this.value; // Obtiene la acción seleccionada
        const formulario = document.getElementById('formulario'); // Contenedor del formulario
        formulario.innerHTML = ''; // Limpia el contenido del formulario

        // Genera el formulario según la acción seleccionada
        switch (accion) {
            case 'registrar_casa':
                formulario.innerHTML = `
                        <h3>Registrar Casa</h3>
                        <label>Nombre: <input type="text" name="nombre_casa" required></label><br>
                        <label>Capacidad: <input type="number" name="capacidad_casa" min="1" required></label><br>
                        <label>Precio: <input type="number" name="precio_casa" min="1" required></label><br>
                    `;
                break;

            case 'registrar_habitacion':
                formulario.innerHTML = `
                        <h3>Registrar Habitación</h3>
                        <label>Nombre: <input type="text" name="nombre_habitacion" required></label><br><br>
                        <label>Número de Habitación: <input type="number" name="numero_habitacion" min="1" required></label><br>
                        <label>Capacidad: <input type="number" name="capacidad_habitacion" min="1" required></label><br>
                        <label>Precio: <input type="number" name="precio_habitacion" min="1" required></label><br>

                    `;
                break;

            case 'registrar_empleado':
                formulario.innerHTML = `
                        <h3>Registrar Empleado</h3>
                        <label>Nombre: <input type="text" name="nombre_empleado" required></label><br>
                        <label>Usuario: <input type="text" name="usuario_empleado" required></label><br>
                        <label>Contraseña: <input type="password" name="password_empleado" required></label><br>
                        <label>Fecha de Contrato: <input type="date" name="fecha_contrato_empleado" required></label><br>
                        <label>Puesto:
                            <select name="puesto_empleado" required>
                                <option value="Gerente">Gerente</option>
                                <option value="Recepcionista">Recepcionista</option>
                                <option value="Limpieza">Limpieza</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                        </label><br>
                    `;
                break;

            case 'terminar_instalacion':
                formulario.innerHTML = `<p>¡Estás a punto de terminar la instalación!</p>`;
                break;
        }
    });
</script>
</body>
</html>