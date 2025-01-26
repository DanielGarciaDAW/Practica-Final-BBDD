<?php

abstract class Estancia {
    protected $id;
    protected $nombre;
    protected $disponible;
    protected $capacidad;

    /**
     * Constructor de la clase Estancia.
     *
     * @param int|null $id Identificador único (si existe).
     * @param bool $disponible Disponibilidad de la estancia (true/false).
     * @param int $capacidad Capacidad máxima de la estancia.
     */
    public function __construct($id = null, $nombre = null, $disponible = true, $capacidad = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->disponible = $disponible;
        $this->capacidad = $capacidad;
    }

    /**
     * Guarda o actualiza una estancia en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string $table Nombre de la tabla en la base de datos.
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function guardar($conexion, $table) {
        $fields = ['nombre', 'disponible', 'capacidad'];
        $values = [$this->nombre, $this->disponible, $this->capacidad];

        if ($this->id) {
            // Usamos una función anónima estándar compatible con PHP 7.3
            $setFields = implode(', ', array_map(function($f) {
                return "$f = ?";
            }, $fields));
            //Construncciond e la consulta Update
            $stmt = $conexion->prepare("UPDATE $table SET $setFields WHERE id = ?");
            $values[] = $this->id; // Añadimos el ID al final de los valores
        } else {
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt = $conexion->prepare("INSERT INTO $table (" . implode(', ', $fields) . ") VALUES ($placeholders)");
        }

        return $stmt->execute($values); // Ejecutamos la consulta con los valores
    }

    /**
     * Elimina una estancia de la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string $table Nombre de la tabla en la base de datos.
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function eliminar($conexion, $table) {
        if ($this->id) {
            $stmt = $conexion->prepare("DELETE FROM $table WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
