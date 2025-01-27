<?php

abstract class Estancia {
    protected $id;
    protected $nombre;
    protected $disponible;
    protected $capacidad;
    protected $precio;

    /**
     * Constructor de la clase Estancia.
     *
     * @param int|null $id Identificador único (si existe).
     * @param bool $disponible Disponibilidad de la estancia (true/false).
     * @param int $capacidad Capacidad máxima de la estancia.
     */
    public function __construct($id = null, $nombre = null, $disponible = true, $capacidad = null, $precio = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->disponible = $disponible;
        $this->capacidad = $capacidad;
        $this->precio = $precio;
    }

    /**
     * Obtiene el ID de la estancia.
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Establece el ID de la estancia.
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Obtiene el nombre de la estancia.
     * @return string|null
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Establece el nombre de la estancia.
     * @param string $nombre
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Obtiene la disponibilidad de la estancia.
     * @return bool
     */
    public function isDisponible() {
        return $this->disponible;
    }

    /**
     * Establece la disponibilidad de la estancia.
     * @param bool $disponible
     */
    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }

    /**
     * Obtiene la capacidad máxima de la estancia.
     * @return int|null
     */
    public function getCapacidad() {
        return $this->capacidad;
    }

    /**
     * Establece la capacidad máxima de la estancia.
     * @param int $capacidad
     */
    public function setCapacidad($capacidad) {
        $this->capacidad = $capacidad;
    }

    /**
     * Obtiene el precio de la estancia.
     * @return float|null
     */
    public function getPrecio() {
        return $this->precio;
    }

    /**
     * Establece el precio de la estancia.
     * @param float $precio
     */
    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    /**
     * Guarda o actualiza una estancia en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string $table Nombre de la tabla en la base de datos.
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function guardar($conexion, $table) {
        $fields = ['nombre', 'disponible', 'capacidad', 'precio'];
        $values = [$this->nombre, $this->disponible, $this->capacidad, $this->precio];

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
