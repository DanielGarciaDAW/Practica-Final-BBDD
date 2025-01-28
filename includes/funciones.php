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
        // Iniciar una transacción para garantizar consistencia
        $conexion->beginTransaction();

        // Insertar la reserva en la tabla `reservas`
        $sqlInsertReserva = "
            INSERT INTO reservas (id_cliente, fecha_reserva, fecha_inicio, fecha_fin, estado)
            VALUES (:id_cliente, NOW(), :fecha_inicio, :fecha_fin, 'Confirmada')
        ";
        $stmt = $conexion->prepare($sqlInsertReserva);
        $stmt->execute([
            ':id_cliente' => $idCliente,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin' => $fechaFin
        ]);

        // Obtener el ID de la reserva recién creada
        $idReserva = $conexion->lastInsertId();

        //Calcular precio total
        $precioTotal = $precioPorNoche * $dias;

        // Insertar la relación entre la reserva y la casa o habitación con el precio total
        $sqlInsertRelacion = "
            INSERT INTO reservas_casas_habitaciones (id_reserva, id_casa, id_habitacion, precio)
            VALUES (:id_reserva, :id_casa, :id_habitacion, :precio)
        ";

        $stmtRelacion = $conexion->prepare($sqlInsertRelacion);
        $stmtRelacion->execute([
            ':id_reserva' => $idReserva,
            ':id_casa' => $tipoEstancia === 'casa' ? $idEstancia : null,
            ':id_habitacion' => $tipoEstancia === 'habitacion' ? $idEstancia : null,
            ':precio' => $precioTotal // Precio total calculado
        ]);

        // Confirmar la transacción
        $conexion->commit();

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

        return $idReserva; // Devolver el ID de la reserva para confirmar su éxito
    } catch (PDOException $e) {
        // Revertir la transacción en caso de error
        $conexion->rollBack();
        error_log("Error al procesar la reserva: " . $e->getMessage());
        return false; // Indicar que ocurrió un error
    }
}




