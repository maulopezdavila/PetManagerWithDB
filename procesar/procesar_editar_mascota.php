<?php
require_once '../models/mascota.php';
require_once '../models/dueno.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener datos del formulario
$mascotaId = intval($_POST['mascota_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$especie = trim($_POST['especie'] ?? '');
$raza = trim($_POST['raza'] ?? '') ?: null;
$fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '') ?: null;
$color = trim($_POST['color'] ?? '') ?: null;

// Validaciones
if (!$mascotaId || $nombre === '' || $especie === '') {
    die("Error: Datos inválidos. <a href='../listas/listar_mascotas.php'>Volver</a>");
}

// Validar nombre
$patron = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/';
if (!preg_match($patron, $nombre)) {
    die("Error: Nombre inválido. <a href='../editar/editar_mascota.php?id={$mascotaId}'>Volver</a>");
}

// Obtener la mascota
$mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
if (!$mascota) {
    die("Error: Mascota no encontrada. <a href='../listas/listar_mascotas.php'>Volver</a>");
}

// Verificar si el nuevo nombre ya existe (excluyendo la mascota actual)
$bd = obtenerConexionBD();
$sql = "SELECT COUNT(*) FROM mascotas WHERE nombre = ? AND usuario_id = ? AND id != ? AND activo = TRUE";
$stmt = $bd->prepare($sql);
$stmt->execute([$nombre, $usuarioId, $mascotaId]);
if ($stmt->fetchColumn() > 0) {
    die("Error: Ya existe otra mascota con ese nombre. <a href='../editar/editar_mascota.php?id={$mascotaId}'>Volver</a>");
}

// Actualizar datos de la mascota
$mascota->setNombre($nombre);
$mascota->setEspecie($especie);
$mascota->setRaza($raza);
$mascota->setFechaNacimiento($fechaNacimiento);
$mascota->setColor($color);

if ($mascota->actualizar()) {
    // Actualizar relaciones con dueños
    $mascota->desasociarTodosDuenos();
    
    if (isset($_POST['duenos']) && is_array($_POST['duenos'])) {
        foreach ($_POST['duenos'] as $duenoId) {
            $duenoId = intval($duenoId);
            $mascota->asociarDueno($duenoId);
        }
    }
    
    header('Location: ../listas/listar_mascotas.php?exito=' . urlencode('Mascota actualizada exitosamente'));
} else {
    die("Error: No se pudo actualizar la mascota. <a href='../editar/editar_mascota.php?id={$mascotaId}'>Volver</a>");
}
exit;
?>