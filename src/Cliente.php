<?php
require_once 'Persona.php';

/**
 * Clase Cliente: Representa a un cliente del sistema.
 */
class Cliente extends Persona {
    protected $email;         // Correo electrónico único del cliente
    protected $tel;            // Teléfono del cliente


    /**
     * Constructor.
     *
     * @param int|null $id Identificador único.
     * @param string|null $nombre Nombre del cliente.
     * @param string|null $password Contraseña del cliente.
     * @param string|null $correo Correo electrónico del cliente.
     * @param string|null $tel Teléfono del cliente.
     * @param string|null $fecha_registro Fecha de registro del cliente.
     */
    public function __construct($id = null, $nombre = null,$usuario=null, $password = null, $email = null, $tel = null) {

        parent::__construct($id, $nombre,$usuario, $password);
        $this->email = $email;
        $this->tel = $tel;
    }

    /**
     * Getter para Email.
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Setter para Email.
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Getter para Teléfono.
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * Setter para Teléfono.
     */
    public function setTel($tel) {
        $this->tel = $tel;
    }

    /**
     * Guarda o actualiza un cliente en la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param string|null $table Ignorado, siempre será 'clientes'.
     * @param array $additionalFields Campos adicionales para guardar (opcional).
     * @return bool True si la operación fue exitosa.
     * @throws Exception Si las validaciones fallan.
     */
    public function guardar($conexion, $table = null, $additionalFields = []) {

        // Validación: El correo debe tener un formato válido
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo no tiene un formato válido.");
        }

        // Validación: El teléfono debe tener entre 10 y 15 dígitos
        if (!preg_match('/^\+?[0-9]{9,15}$/', $this->tel)) {
            throw new Exception("El teléfono debe tener entre 10 y 15 dígitos.");
        }

        // Campos adicionales específicos de Cliente
        $additionalFields = [
            'email' => $this->email,
            'telefono' => $this->tel
        ];

        // Llamamos al método save de la clase base, pasando la tabla 'clientes'
        return parent::guardar($conexion, 'clientes', $additionalFields);
    }
}
