<?php
/*
 * Archivo de configuración para conexión a base de datos MySQL
 * Modifica estos valores según tu configuración local
 */

class ConexionBD {
    // Aquí guardamos la única instancia (para que no se creen conexiones de más)
    private static $instancia = null;
    private $conexion;
    
    // Configuración de la base de datos
    private $servidor = 'localhost';
    private $base_datos = 'sistema_mascotas';
    private $usuario = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    // El constructor se encarga de crear la conexión
    private function __construct() {
        try {
            // Se arma el "dsn" que es como la dirección + datos de la BD
            $dsn = "mysql:host={$this->servidor};dbname={$this->base_datos};charset={$this->charset}";

            // Opciones de la conexión (manejo de errores, fetch, etc.)
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Aquí finalmente se conecta a la base de datos
            $this->conexion = new PDO($dsn, $this->usuario, $this->password, $opciones);
        } catch (PDOException $e) {

            // Si algo sale mal, muere el programa y muestra el error
            die('Error de conexión: ' . $e->getMessage());
        }
    }
    
    // Método para obtener la misma conexión sin crear otra
    public static function obtenerInstancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    
    // Devuelve la conexión para usarla en las consultas
    public function obtenerConexion() {
        return $this->conexion;
    }
    
    // Cierra la conexión (aunque en PDO normalmente se cierra sola)
    public function cerrarConexion() {
        $this->conexion = null;
    }
}

// Función helper para obtener conexión
function obtenerConexionBD() {
    return ConexionBD::obtenerInstancia()->obtenerConexion();
}
?>