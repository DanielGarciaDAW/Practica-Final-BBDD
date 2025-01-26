<?php
require_once 'Estancia.php';

/**
 * Clase Habitacion: Representa una habitación en el sistema.
 */
class Habitacion extends Estancia {
    protected static $tableName = 'habitaciones'; // Tabla fija para habitaciones
    private $numero_habitacion;

    /**
     * Constructor de la clase Habitacion.
     *
     * @param int|null $id Identificador único.
     * @param bool $disponible Disponibilidad de la habitación.
     * @param int $capacidad Capacidad máxima de la habitación.
     * @param int|null $numero_habitacion Número de la habitación.
     */
    public function __construct($id = null,$nombre = null, $disponible = true, $capacidad = 2, $numero_habitacion = null) {
        parent::__construct($id,$nombre, $disponible, $capacidad);
        $this->numero_habitacion = $numero_habitacion;
    }

    /**
     * Guarda o actualiza una habitación en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string|null $table Ignorado, ya que usamos una tabla fija ('habitaciones').
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function guardar($conexion, $table = null) {
        $fields = ['nombre','disponible', 'capacidad', 'numero_habitacion'];
        $values = [$this->nombre,$this->disponible, $this->capacidad, $this->numero_habitacion];

        if ($this->id) {
            $setFields = implode(', ', array_map(function($f) {
                return "$f = ?";
            }, $fields));
            $stmt = $conexion->prepare("UPDATE " . self::$tableName . " SET $setFields WHERE id = ?");
            $values[] = $this->id; // Añadimos el ID al final
        } else {
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt = $conexion->prepare("INSERT INTO " . self::$tableName . " (" . implode(', ', $fields) . ") VALUES ($placeholders)");
        }

        return $stmt->execute($values);
    }

    /**
     * Elimina una habitación de la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function eliminar($conexion, $table = null) {
        // Llamamos al método padre con la tabla fija de habitaciones
        return parent::eliminar($conexion, self::$tableName);
    }
}
