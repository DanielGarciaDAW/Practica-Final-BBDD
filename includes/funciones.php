<?php
include_once '../src/Casa.php';
include_once '../src/Habitacion.php';

function recogerCasas($conexion){
    try {
        $stmtCasas = $conexion->prepare("SELECT * FROM casas");
        $stmtCasas->execute();
        return $stmtCasas->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        throw new PDOException("Error al recoger casas: " . $e->getMessage(), (int)$e->getCode(), $e);
    }
}

function recogerHabitaciones($conexion){
    try {
        $stmtHabitaciones = $conexion->prepare("SELECT * FROM habitaciones");
        $stmtHabitaciones->execute();
        return $stmtHabitaciones->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        throw new PDOException("Error al recoger habitaciones: " . $e->getMessage(), (int)$e->getCode(), $e);
    }
}
function recogerCasa($conexion, $id){
    try {
        $stmt = $conexion->prepare("SELECT * FROM casas WHERE id = :id");
        $stmt->execute([':id'=> $id]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($datos){
            return new Casa(
                $datos["id"],
                $datos["nombre"],
                $datos["disponible"],
                $datos["capacidad"],
                $datos["precio"]
            );
        }
        return null;
    }catch (PDOException $e) {
        throw new PDOException("Error al recoger la reserva: " . $e->getMessage(), (int)$e->getCode(), $e);
    }

}
function recogerHabitacion($conexion, $id){
    try {
        $stmt = $conexion->prepare("SELECT * FROM habitaciones WHERE id = :id");
        $stmt->execute([':id'=> $id]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos){
            return new Habitacion(
                $datos["id"],
                $datos["nombre"],
                $datos["disponible"],
                $datos ["capacidad"],
                $datos["precio"],
                $datos["numero_habitacion"]
            );
        }
    }catch (PDOException $e) {
        throw new PDOException("Error al recoger la reserva: " . $e->getMessage(), (int)$e->getCode(), $e);
    }
}

function procesarReserva($conexion, $idCliente, $fechaInicio, $fechaFin, $tipoEstancia, $idEstancia, $precioPorNoche, $dias) {
    try {
        // Calcular precio total
        $precioTotal = $precioPorNoche * $dias;

        // Determinar la columna de estancia a insertar
        $columnaEstancia = ($tipoEstancia === 'casa') ? 'id_casa' : 'id_habitacion';

        // Iniciar una transacción para garantizar consistencia
        $conexion->beginTransaction();

        // Insertar la reserva en la tabla `reservas` con la estancia correspondiente
        $sqlInsertReserva = "
            INSERT INTO reservas (id_cliente, $columnaEstancia, fecha_reserva, fecha_inicio, fecha_fin, estado, precio_total)
            VALUES (:id_cliente, :id_estancia, NOW(), :fecha_inicio, :fecha_fin, 'Confirmada', :precio_total)
        ";

        $stmt = $conexion->prepare($sqlInsertReserva);
        $stmt->execute([
            ':id_cliente' => $idCliente,      // ID del cliente que realiza la reserva
            ':id_estancia' => $idEstancia,   // ID de la casa o habitación
            ':fecha_inicio' => $fechaInicio, // Fecha de inicio de la reserva
            ':fecha_fin' => $fechaFin,       // Fecha de fin de la reserva
            ':precio_total' => $precioTotal  // Precio total de la reserva
        ]);

        // Obtener el ID de la reserva recién creada
        $idReserva = $conexion->lastInsertId();

        // Confirmar la transacción
        $conexion->commit();

        // Guardar los datos de la reserva en la sesión para confirmación
        $_SESSION['reserva'] = [
            'id_reserva' => $idReserva,
            'tipo_estancia' => $tipoEstancia,
            'id_estancia' => $idEstancia,
            'nombre_estancia' => $_SESSION['estancia']['nombre'], // Desde la sesión existente
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'dias' => $dias,
            'precio_por_noche' => $precioPorNoche,
            'precio_total' => $precioTotal,
            'nombre_cliente' => $_SESSION['cliente']['nombre']
        ];

        // Devolver el ID de la reserva para confirmar su éxito
        return $idReserva;

    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $conexion->rollBack();
        error_log("Error al procesar la reserva: " . $e->getMessage());
        return false; // Indicar que ocurrió un error
    }
}


function obtenerFechasReservadas($conexion, $idEstancia, $tipoEstancia) {
    try {
        // Determinar la columna a filtrar
        $columnaEstancia = ($tipoEstancia === 'casa') ? 'id_casa' : 'id_habitacion';

        // Consulta para obtener las fechas reservadas
        $query = "
            SELECT fecha_inicio, fecha_fin 
            FROM reservas
            WHERE $columnaEstancia = :idEstancia
              AND estado = 'Confirmada'
        ";

        $stmt = $conexion->prepare($query);
        $stmt->execute([':idEstancia' => $idEstancia]);

        $fechas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fechaInicio = new DateTime($row['fecha_inicio']);
            $fechaFin = new DateTime($row['fecha_fin']);

            // Generar todas las fechas del rango
            while ($fechaInicio <= $fechaFin) {
                $fechas[] = $fechaInicio->format('Y-m-d');
                $fechaInicio->modify('+1 day');
            }
        }

        return $fechas;
    } catch (PDOException $e) {
        error_log("Error al obtener las fechas reservadas: " . $e->getMessage());
        return [];
    }
}





