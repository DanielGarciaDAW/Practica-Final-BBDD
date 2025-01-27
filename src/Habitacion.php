<?php
require_once 'Estancia.php';

/**
 * Clase Habitacion: Representa una habitación en el sistema.
 * Hereda atributos y métodos de la clase abstracta Estancia.
 */
class Habitacion extends Estancia {
    private $numero_habitacion;

    /**
     * Constructor de la clase Habitacion.
     *
     * @param int|null $id Identificador único.
     * @param string|null $nombre Nombre de la habitación.
     * @param bool $disponible Disponibilidad de la habitación.
     * @param int|null $capacidad Capacidad máxima de la habitación.
     * @param float|null $precio Precio de la habitación.
     * @param int|null $numero_habitacion Número de la habitación.
     */
    public function __construct($id = null, $nombre = null, $disponible = true, $capacidad = null, $precio = null, $numero_habitacion = null) {
        parent::__construct($id, $nombre, $disponible, $capacidad, $precio);
        $this->numero_habitacion = $numero_habitacion;
    }

    /**
     * Obtiene el número de la habitación.
     * @return int|null
     */
    public function getNumeroHabitacion() {
        return $this->numero_habitacion;
    }

    /**
     * Establece el número de la habitación.
     * @param int $numero_habitacion
     */
    public function setNumeroHabitacion($numero_habitacion) {
        $this->numero_habitacion = $numero_habitacion;
    }
}
