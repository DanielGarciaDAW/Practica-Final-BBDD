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



