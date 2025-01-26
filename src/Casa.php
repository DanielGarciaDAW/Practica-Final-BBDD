<?php
require_once 'Estancia.php';

/**
 * Clase Casa: Representa las casas en el sistema.
 * Hereda atributos y métodos de la clase abstracta Estancia.
 */
class Casa extends Estancia {
    protected static $tableName = 'casas'; // Nombre de la tabla asociada en la base de datos

    /**
     * Constructor de la clase Casa.
     *
     * @param int|null $id Identificador único.
     * @param string $nombre Nombre de la casa.
     * @param bool $disponible Disponibilidad de la casa.
     * @param int $capacidad Capacidad máxima de la casa.
     */
    public function __construct($id = null, $nombre = null, $disponible = true, $capacidad = null, $precio = null) {
        parent::__construct($id, $nombre, $disponible, $capacidad, $precio);
    }


    /**
     * Guarda o actualiza una casa en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string|null $table Ignorado, ya que usamos una tabla fija ('casas').
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function guardar($conexion, $table = null) {
        // Llamamos al método de la clase padre usando la tabla fija
        return parent::guardar($conexion, self::$tableName);
    }
}
