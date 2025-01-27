<?php
/**
 * Clase abstracta Persona: Clase base para clientes y empleados.
 */
abstract class Persona {
    protected $id;
    protected $nombre;
    protected $usuario;
    protected $password;

    /**
     * Constructor.
     *
     * @param int|null $id Identificador único (opcional).
     * @param string|null $nombre Nombre de la persona.
     * @param string|null $password Contraseña sin encriptar.
     */
    public function __construct($id = null, $nombre = null,$usuario = null, $password = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->usuario = $usuario;
        $this->password = $password;
    }

    /**
     * Getter para ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Setter para ID.
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Getter para Nombre.
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Setter para Nombre.
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    /**
     * Getter para Usuario.
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Setter para Usuario.
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * Getter para Password.
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Setter para Password.
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Método para guardar o actualizar una persona en la base de datos.
     *
     * @param PDO $conexion Conexión PDO.
     * @param string $table Nombre de la tabla.
     * @param array $additionalFields Campos adicionales.
     * @return bool True si la operación fue exitosa.
     * @throws Exception Si las validaciones fallan.
     */
    public function guardar($conexion, $table, $additionalFields = []) {
        // Validaciones comunes
        if (strlen($this->nombre) < 3) {
            throw new Exception("El nombre debe tener al menos 3 caracteres.");
        }
        if (strlen($this->usuario) < 3) {
            throw new Exception("El usuario debe tener al menos 3 caracteres.");
        }
        if (strlen($this->password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres.");
        }

        // Campos y valores
        $fields = ['nombre', 'usuario', 'password'];
        $values = [$this->nombre,$this->usuario, password_hash($this->password, PASSWORD_DEFAULT)];
        foreach ($additionalFields as $field => $value) {
            $fields[] = $field;
            $values[] = $value;
        }

        if ($this->id) {
            // Update
            $setFields = implode(', ', array_map(function ($f) {
                return "$f = ?";
            }, $fields));
            $stmt = $conexion->prepare("UPDATE $table SET $setFields WHERE id = ?");
            $values[] = $this->id;
        } else {
            // Insert
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $stmt = $conexion->prepare("INSERT INTO $table (" . implode(', ', $fields) . ") VALUES ($placeholders)");
        }

        return $stmt->execute($values);
    }

    /**
     * Método para obtener todos los registros de una tabla.
     *
     * @param PDO $conexion Conexión PDO.
     * @param string $table Nombre de la tabla.
     * @return array Array de objetos.
     */
    public static function getAll($conexion, $table) {
        $stmt = $conexion->prepare("SELECT * FROM $table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
    }
}
