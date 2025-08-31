<?php
require_once __DIR__ . '/../config/conexion_bd.php';

class Mascota {
    // Propiedades básicas
    public $id;
    public $nombre;
    public $especie;
    public $raza;
    public $fechaNacimiento;
    public $color;
    public $usuarioId;
    public $duenos = []; // lista de dueños asociados (tabla intermedia)
    public $visitas = []; // lista de visitas médicas de la mascota

    // Constructor para inicializar con lo mínimo
    public function __construct($nombre, $especie, $usuarioId = null) {
        $this->nombre = $nombre;
        $this->especie = $especie;
        $this->usuarioId = $usuarioId;
    }

    // Getters y setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    
    public function getEspecie() { return $this->especie; }
    public function setEspecie($especie) { $this->especie = $especie; }
    
    public function getRaza() { return $this->raza; }
    public function setRaza($raza) { $this->raza = $raza; }
    
    public function getFechaNacimiento() { return $this->fechaNacimiento; }
    public function setFechaNacimiento($fechaNacimiento) { $this->fechaNacimiento = $fechaNacimiento; }
    
    public function getColor() { return $this->color; }
    public function setColor($color) { $this->color = $color; }
    
    public function getDuenos() { return $this->duenos; }
    public function getVisitas() { return $this->visitas; }

    // Métodos de base de datos

    // Guardar mascota nueva
    public function guardar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "INSERT INTO mascotas (nombre, especie, raza, fecha_nacimiento, color, usuario_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $bd->prepare($sql);
            $stmt->execute([
                $this->nombre,
                $this->especie,
                $this->raza,
                $this->fechaNacimiento,
                $this->color,
                $this->usuarioId
            ]);
            // guarda el id generado
            $this->id = $bd->lastInsertId();
            return true;
        } catch (PDOException $e) {
            error_log("Error al guardar mascota: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar datos de una mascota ya guardada
    public function actualizar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, 
                    fecha_nacimiento = ?, color = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([
                $this->nombre,
                $this->especie,
                $this->raza,
                $this->fechaNacimiento,
                $this->color,
                $this->id,
                $this->usuarioId
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar mascota: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar (lógico, no físico) una mascota
    public static function eliminar($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "UPDATE mascotas SET activo = FALSE WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([$id, $usuarioId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar mascota: " . $e->getMessage());
            return false;
        }
    }

    // Obtener una mascota por id
    public static function obtenerPorId($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM mascotas WHERE id = ? AND usuario_id = ? AND activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$id, $usuarioId]);
            $fila = $stmt->fetch();
            
            if ($fila) {
                return self::crearDesdeFila($fila);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error al obtener mascota: " . $e->getMessage());
            return null;
        }
    }

    // Buscar mascota por nombre exacto
    public static function obtenerPorNombre($nombre, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM mascotas WHERE nombre = ? AND usuario_id = ? AND activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$nombre, $usuarioId]);
            $fila = $stmt->fetch();
            
            if ($fila) {
                return self::crearDesdeFila($fila);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error al obtener mascota por nombre: " . $e->getMessage());
            return null;
        }
    }

    // Obtener todas las mascotas del usuario
    public static function obtenerTodas($usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM mascotas WHERE usuario_id = ? AND activo = TRUE ORDER BY nombre";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$usuarioId]);
            
            $mascotas = [];
            while ($fila = $stmt->fetch()) {
                $mascota = self::crearDesdeFila($fila);
                $mascotas[] = $mascota;
            }
            return $mascotas;
        } catch (PDOException $e) {
            error_log("Error al obtener mascotas: " . $e->getMessage());
            return [];
        }
    }

    // Buscar mascotas por nombre, especie o raza
    public static function buscar($termino, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM mascotas WHERE usuario_id = ? AND activo = TRUE 
                    AND (nombre LIKE ? OR especie LIKE ? OR raza LIKE ?) ORDER BY nombre";
            $terminoBusqueda = "%{$termino}%";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$usuarioId, $terminoBusqueda, $terminoBusqueda, $terminoBusqueda]);
            
            $mascotas = [];
            while ($fila = $stmt->fetch()) {
                $mascotas[] = self::crearDesdeFila($fila);
            }
            return $mascotas;
        } catch (PDOException $e) {
            error_log("Error en búsqueda de mascotas: " . $e->getMessage());
            return [];
        }
    }

    // Cargar los dueños extra de la mascota
    public function cargarDuenos() {
        if (!$this->id) return;
        
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT d.* FROM duenos d 
                    INNER JOIN mascota_dueno md ON d.id = md.dueno_id 
                    WHERE md.mascota_id = ? AND d.activo = TRUE";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$this->id]);
            
            $this->duenos = [];
            while ($fila = $stmt->fetch()) {
                require_once __DIR__ . '/dueno.php';
                $this->duenos[] = Dueno::crearDesdeFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error al cargar dueños: " . $e->getMessage());
        }
    }

    // Cargar las visitas médicas de la mascota
    public function cargarVisitas() {
        if (!$this->id) return;
        
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM visitas_medicas WHERE mascota_id = ? ORDER BY fecha DESC";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$this->id]);
            
            $this->visitas = [];
            while ($fila = $stmt->fetch()) {
                require_once __DIR__ . '/visita_medica.php';
                $this->visitas[] = VisitaMedica::crearDesdeFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error al cargar visitas: " . $e->getMessage());
        }
    }

    // Relacionar la mascota con un dueño
    public function asociarDueno($duenoId) {
        if (!$this->id) return false;
        
        $bd = obtenerConexionBD();
        try {
            $sql = "INSERT IGNORE INTO mascota_dueno (mascota_id, dueno_id) VALUES (?, ?)";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([$this->id, $duenoId]);
        } catch (PDOException $e) {
            error_log("Error al asociar dueño: " . $e->getMessage());
            return false;
        }
    }

    // Quitar todos los dueños asociados
    public function desasociarTodosDuenos() {
        if (!$this->id) return false;
        
        $bd = obtenerConexionBD();
        try {
            $sql = "DELETE FROM mascota_dueno WHERE mascota_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Error al desasociar dueños: " . $e->getMessage());
            return false;
        }
    }

    // Crear un objeto Mascota a partir de un registro
    public static function crearDesdeFila($fila) {
        $mascota = new self($fila['nombre'], $fila['especie'], $fila['usuario_id']);
        $mascota->setId($fila['id']);
        $mascota->setRaza($fila['raza']);
        $mascota->setFechaNacimiento($fila['fecha_nacimiento']);
        $mascota->setColor($fila['color']);
        return $mascota;
    }

    // Convertir la mascota a un array
    public function aArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'fechaNacimiento' => $this->fechaNacimiento,
            'color' => $this->color,
            'duenos' => array_map(function($dueno) {
                return $dueno->aArray();
            }, $this->duenos),
            'visitas' => array_map(function($visita) {
                return $visita->aArray();
            }, $this->visitas)
        ];
    }
}
?>