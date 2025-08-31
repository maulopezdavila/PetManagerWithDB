<?php
require_once __DIR__ . '/../config/conexion_bd.php';

class Usuario {
    // Propiedades básicas
    public $id;
    public $nombreUsuario;
    public $hashPassword; // la contraseña en hash
    public $fechaRegistro;
    public $activo; // si el usuario está activo o no
    
    // Constructor: solo necesitamos nombre de usuario y contraseña 
    public function __construct($nombreUsuario, $hashPassword) {
        $this->nombreUsuario = $nombreUsuario;
        $this->hashPassword = $hashPassword;
        $this->activo = true;
    }
    
    // Getters y setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getNombreUsuario() { return $this->nombreUsuario; }
    public function setNombreUsuario($nombreUsuario) { $this->nombreUsuario = $nombreUsuario; }
    
    public function getHashPassword() { return $this->hashPassword; }
    public function setHashPassword($hashPassword) { $this->hashPassword = $hashPassword; }
    
    public function getFechaRegistro() { return $this->fechaRegistro; }
    
    public function getActivo() { return $this->activo; }
    public function setActivo($activo) { $this->activo = $activo; }
    
    // Métodos de autenticación
    public function verificarPassword($password) {
        return password_verify($password, $this->hashPassword);
    }
    
    // Métodos de base de datos

    // Guardar un usuario nuevo
    public function guardar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "INSERT INTO usuarios (nombre_usuario, hash_password) VALUES (?, ?)";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$this->nombreUsuario, $this->hashPassword]);
            $this->id = $bd->lastInsertId();
            return true;
        } catch (PDOException $e) {
            error_log("Error al guardar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener usuario por nombre (solo activo)
    public static function obtenerPorNombre($nombreUsuario) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$nombreUsuario]);
            $fila = $stmt->fetch();
            
            if ($fila) {
                return self::crearDesdeFila($fila);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return null;
        }
    }
    
    // Verifica si un nombre de usuario ya existe (para registro)
    public static function existeUsuario($nombreUsuario) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ?";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$nombreUsuario]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Autenticar: devuelve el objeto usuario si la contraseña coincide
    public static function autenticar($nombreUsuario, $password) {
        $usuario = self::obtenerPorNombre($nombreUsuario);
        if ($usuario && $usuario->verificarPassword($password)) {
            return $usuario;
        }
        return null;
    }
    
    // Crear un objeto Usuario desde un registro
    public static function crearDesdeFila($fila) {
        $usuario = new self($fila['nombre_usuario'], $fila['hash_password']);
        $usuario->setId($fila['id']);
        $usuario->fechaRegistro = $fila['fecha_registro'];
        $usuario->setActivo($fila['activo']);
        return $usuario;
    }
    
    // Convertir a array
    public function aArray() {
        return [
            'id' => $this->id,
            'nombreUsuario' => $this->nombreUsuario,
            'fechaRegistro' => $this->fechaRegistro,
            'activo' => $this->activo
        ];
    }
}
?>