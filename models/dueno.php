<?php
require_once __DIR__ . '/../config/conexion_bd.php';

class Dueno {
    // Propiedades del dueño
    public $id;
    public $nombre;
    public $telefono;
    public $email;
    public $direccion;
    public $usuarioId;

    // Constructor: se ejecuta cuando creo un nuevo objeto Dueno
    public function __construct($nombre, $telefono, $usuarioId = null) {
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->usuarioId = $usuarioId;
    }

    // Getters y setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    
    public function getTelefono() { return $this->telefono; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }
    
    public function getDireccion() { return $this->direccion; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    // Métodos de base de datos

    // Guarda un nuevo dueño en la base de datos
    public function guardar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "INSERT INTO duenos (nombre, telefono, email, direccion, usuario_id) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $bd->prepare($sql);
            $stmt->execute([
                $this->nombre,
                $this->telefono,
                $this->email,
                $this->direccion,
                $this->usuarioId
            ]);
            $this->id = $bd->lastInsertId(); // Obtiene el id del dueño que se acaba de guardar
            return true;
        } catch (PDOException $e) {
            // Si algo sale mal, se guarda el error en los logs
            error_log("Error al guardar dueño: " . $e->getMessage());
            return false;
        }
    }

    // Actualiza los datos de un dueño existente
    public function actualizar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "UPDATE duenos SET nombre = ?, telefono = ?, email = ?, 
                    direccion = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([
                $this->nombre,
                $this->telefono,
                $this->email,
                $this->direccion,
                $this->id,
                $this->usuarioId
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar dueño: " . $e->getMessage());
            return false;
        }
    }

    // Elimina un dueño (en realidad solo lo marca como inactivo)
    public static function eliminar($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            // Primero eliminar relaciones
            $sql1 = "DELETE FROM mascota_dueno WHERE dueno_id = ?";
            $stmt1 = $bd->prepare($sql1);
            $stmt1->execute([$id]);
            
            // Luego marcar como inactivo
            $sql2 = "UPDATE duenos SET activo = FALSE WHERE id = ? AND usuario_id = ?";
            $stmt2 = $bd->prepare($sql2);
            return $stmt2->execute([$id, $usuarioId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar dueño: " . $e->getMessage());
            return false;
        }
    }

    // Obtiene un dueño por su id
    public static function obtenerPorId($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM duenos WHERE id = ? AND usuario_id = ? AND activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$id, $usuarioId]);
            $fila = $stmt->fetch();
            
            if ($fila) {
                return self::crearDesdeFila($fila); // Convierte la fila en un objeto Dueno
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error al obtener dueño: " . $e->getMessage());
            return null;
        }
    }

    // Obtiene todos los dueños de un usuario
    public static function obtenerTodos($usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM duenos WHERE usuario_id = ? AND activo = TRUE ORDER BY nombre";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$usuarioId]);
            
            $duenos = [];
            while ($fila = $stmt->fetch()) {
                $duenos[] = self::crearDesdeFila($fila);
            }
            return $duenos;
        } catch (PDOException $e) {
            error_log("Error al obtener dueños: " . $e->getMessage());
            return [];
        }
    }

    // Busca dueños por nombre, teléfono o email
    public static function buscar($termino, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM duenos WHERE usuario_id = ? AND activo = TRUE 
                    AND (nombre LIKE ? OR telefono LIKE ? OR email LIKE ?) ORDER BY nombre";
            $terminoBusqueda = "%{$termino}%";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$usuarioId, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            
            $duenos = [];
            while ($fila = $stmt->fetch()) {
                $duenos[] = self::crearDesdeFila($fila);
            }
            return $duenos;
        } catch (PDOException $e) {
            error_log("Error en búsqueda de dueños: " . $e->getMessage());
            return [];
        }
    }

    // Obtiene las mascotas que pertenecen a este dueño
    public function obtenerMascotas() {
        if (!$this->id) return [];
        
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT m.* FROM mascotas m 
                    INNER JOIN mascota_dueno md ON m.id = md.mascota_id 
                    WHERE md.dueno_id = ? AND m.activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$this->id]);
            
            $mascotas = [];
            while ($fila = $stmt->fetch()) {
                require_once __DIR__ . '/mascota.php';
                $mascotas[] = Mascota::crearDesdeFila($fila);
            }
            return $mascotas;
        } catch (PDOException $e) {
            error_log("Error al obtener mascotas del dueño: " . $e->getMessage());
            return [];
        }
    }

    // Convierte una fila de la base de datos en un objeto Dueno
    public static function crearDesdeFila($fila) {
        $dueno = new self($fila['nombre'], $fila['telefono'], $fila['usuario_id']);
        $dueno->setId($fila['id']);
        $dueno->setEmail($fila['email']);
        $dueno->setDireccion($fila['direccion']);
        return $dueno;
    }

    // Convierte un objeto Dueno en un array
    public function aArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion
        ];
    }
}
?>