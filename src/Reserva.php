<?php
/**
 * Clase Reserva: Representa las reservas realizadas por los clientes.
 */
class Reserva {
    public $id_reserva;    // Identificador único de la reserva
    public $id_cliente;    // Identificador del cliente que realiza la reserva
    public $id_empleado;   // Identificador del empleado que gestiona la reserva (opcional)
    public $fecha_reserva; // Fecha en que se realizó la reserva
    public $fecha_inicio;  // Fecha de inicio de la reserva
    public $fecha_fin;     // Fecha de fin de la reserva
    public $estado;        // Estado de la reserva (Pendiente, Confirmada, Cancelada)

    /**
     * Constructor: Inicializa los atributos de la reserva.
     *
     * @param int|null $id_reserva Identificador único (opcional).
     * @param int $id_cliente Identificador del cliente.
     * @param int|null $id_empleado Identificador del empleado.
     * @param string|null $fecha_reserva Fecha de la reserva.
     * @param string $fecha_inicio Fecha de inicio de la reserva.
     * @param string $fecha_fin Fecha de fin de la reserva.
     * @param string $estado Estado de la reserva.
     */
    public function __construct($id_reserva = null, $id_cliente = null, $id_empleado = null, $fecha_reserva = null, $fecha_inicio = null, $fecha_fin = null, $estado = 'Pendiente') {
        $this->id_reserva = $id_reserva;
        $this->id_cliente = $id_cliente;
        $this->id_empleado = $id_empleado;
        $this->fecha_reserva = $fecha_reserva;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->estado = $estado;
    }

    /**
     * Obtiene todas las reservas de la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @return array Devuelve un array de reservas.
     */
    public static function getAll($conexion) {
        $stmt = $conexion->prepare("SELECT * FROM reservas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Guarda o actualiza una reserva en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @return bool Devuelve true si la operación fue exitosa.
     */
    public function save($conexion) {
        // Validaciones básicas
        if (strtotime($this->fecha_inicio) > strtotime($this->fecha_fin)) {
            throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin.");
        }

        if ($this->id_reserva) {
            // Actualizar reserva existente
            $stmt = $conexion->prepare("UPDATE reservas SET id_cliente = ?, id_empleado = ?, fecha_reserva = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id_reserva = ?");
            return $stmt->execute([$this->id_cliente, $this->id_empleado, $this->fecha_reserva, $this->fecha_inicio, $this->fecha_fin, $this->estado, $this->id_reserva]);
        } else {
            // Insertar nueva reserva
            $stmt = $conexion->prepare("INSERT INTO reservas (id_cliente, id_empleado, fecha_reserva, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$this->id_cliente, $this->id_empleado, $this->fecha_reserva, $this->fecha_inicio, $this->fecha_fin, $this->estado]);
        }
    }
}
