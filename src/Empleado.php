<?php
require_once 'Persona.php';

/**
 * Clase Empleado: Representa a un empleado del sistema.
 */
class Empleado extends Persona {
    protected $fecha_contrato; // Fecha de contratación del empleado
    protected $puesto;         // Puesto del empleado

    /**
     * Constructor.
     *
     * @param int|null $id Identificador único.
     * @param string|null $nombre Nombre del empleado.
     * @param string|null $password Contraseña del empleado.
     * @param string|null $fecha_contrato Fecha de contratación.
     * @param string|null $puesto Puesto del empleado.
     */
    public function __construct($id = null, $nombre = null, $usuario = null, $password = null, $fecha_contrato = null, $puesto = null) {
        parent::__construct($id, $nombre, $usuario, $password);
        $this->fecha_contrato = $fecha_contrato;
        $this->puesto = $puesto;
    }

    /**
     * Guarda o actualiza un empleado en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string|null $table Nombre de la tabla (ignorado, siempre será 'empleados').
     * @param array $additionalFields Campos adicionales para guardar.
     * @return bool True si la operación fue exitosa.
     * @throws Exception Si las validaciones fallan.
     */
    public function guardar($conexion, $table = null, $additionalFields = []) {
        // Validaciones específicas del empleado (si es necesario)

        // Campos adicionales específicos de la tabla 'empleados'
        $additionalFields = [
            'fecha_contrato' => $this->fecha_contrato,
            'puesto' => $this->puesto
        ];

        // Llamamos al método save de la clase base con la tabla 'empleados'
        return parent::guardar($conexion, 'empleados', $additionalFields);
    }
}
