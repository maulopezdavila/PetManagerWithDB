<?php
require_once __DIR__ . '/../config/conexion_bd.php';

class VisitaMedica {
    // Propiedades básicas
    public $id;
    public $mascotaId;
    public $fecha;
    public $motivo;
    public $diagnostico;
    public $tratamiento;
    public $usuarioId;

    // Constructor
    public function __construct($mascotaId, $fecha, $diagnostico, $tratamiento, $usuarioId = null) {
        $this->mascotaId = $mascotaId;
        $this->fecha = $fecha;
        $this->diagnostico = $diagnostico;
        $this->tratamiento = $tratamiento;
        $this->usuarioId = $usuarioId;
    }

    // Getters y setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function getMascotaId() { return $this->mascotaId; }
    public function setMascotaId($mascotaId) { $this->mascotaId = $mascotaId; }
    
    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    
    public function getMotivo() { return $this->motivo; }
    public function setMotivo($motivo) { $this->motivo = $motivo; }
    
    public function getDiagnostico() { return $this->diagnostico; }
    public function setDiagnostico($diagnostico) { $this->diagnostico = $diagnostico; }
    
    public function getTratamiento() { return $this->tratamiento; }
    public function setTratamiento($tratamiento) { $this->tratamiento = $tratamiento; }

    // Métodos de base de datos

    // Guardar una nueva visita
    public function guardar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "INSERT INTO visitas_medicas (mascota_id, fecha, motivo, diagnostico, tratamiento, usuario_id) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $bd->prepare($sql);
            $stmt->execute([
                $this->mascotaId,
                $this->fecha,
                $this->motivo,
                $this->diagnostico,
                $this->tratamiento,
                $this->usuarioId
            ]);
            $this->id = $bd->lastInsertId();
            return true;
        } catch (PDOException $e) {
            error_log("Error al guardar visita: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar visita existente
    public function actualizar() {
        $bd = obtenerConexionBD();
        try {
            $sql = "UPDATE visitas_medicas SET mascota_id = ?, fecha = ?, motivo = ?, 
                    diagnostico = ?, tratamiento = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([
                $this->mascotaId,
                $this->fecha,
                $this->motivo,
                $this->diagnostico,
                $this->tratamiento,
                $this->id,
                $this->usuarioId
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar visita: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar visita
    public static function eliminar($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "DELETE FROM visitas_medicas WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            return $stmt->execute([$id, $usuarioId]);
        } catch (PDOException $e) {
            error_log("Error al eliminar visita: " . $e->getMessage());
            return false;
        }
    }

    // Obtener visita por su ID
    public static function obtenerPorId($id, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM visitas_medicas WHERE id = ? AND usuario_id = ?";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$id, $usuarioId]);
            $fila = $stmt->fetch();
            
            if ($fila) {
                return self::crearDesdeFila($fila);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Error al obtener visita: " . $e->getMessage());
            return null;
        }
    }

    // Obtener todas las visitas de un usuario (con nombre de mascota
    public static function obtenerTodas($usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT vm.*, m.nombre as nombre_mascota 
                    FROM visitas_medicas vm 
                    INNER JOIN mascotas m ON vm.mascota_id = m.id 
                    WHERE vm.usuario_id = ? AND m.activo = TRUE 
                    ORDER BY vm.fecha DESC";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$usuarioId]);
            
            $visitas = [];
            while ($fila = $stmt->fetch()) {
                $visita = self::crearDesdeFila($fila);
                $visita->nombreMascota = $fila['nombre_mascota'];
                $visitas[] = $visita;
            }
            return $visitas;
        } catch (PDOException $e) {
            error_log("Error al obtener visitas: " . $e->getMessage());
            return [];
        }
    }

    // Obtener todas las visitas de una mascota específica
    public static function obtenerPorMascota($mascotaId, $usuarioId) {
        $bd = obtenerConexionBD();
        try {
            $sql = "SELECT * FROM visitas_medicas WHERE mascota_id = ? AND usuario_id = ? ORDER BY fecha DESC";
            $stmt = $bd->prepare($sql);
            $stmt->execute([$mascotaId, $usuarioId]);
            
            $visitas = [];
            while ($fila = $stmt->fetch()) {
                $visitas[] = self::crearDesdeFila($fila);
            }
            return $visitas;
        } catch (PDOException $e) {
            error_log("Error al obtener visitas por mascota: " . $e->getMessage());
            return [];
        }
    }

    // Crear objeto VisitaMedica desde fila de db
    public static function crearDesdeFila($fila) {
        $visita = new self($fila['mascota_id'], $fila['fecha'], $fila['diagnostico'], $fila['tratamiento'], $fila['usuario_id']);
        $visita->setId($fila['id']);
        $visita->setMotivo($fila['motivo']);
        return $visita;
    }

     // Convertir a array
    public function aArray() {
        return [
            'id' => $this->id,
            'mascotaId' => $this->mascotaId,
            'fecha' => $this->fecha,
            'motivo' => $this->motivo,
            'diagnostico' => $this->diagnostico,
            'tratamiento' => $this->tratamiento
        ];
    }
}
?>