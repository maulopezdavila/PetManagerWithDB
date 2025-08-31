<?php
require_once '../models/mascota.php';
require_once '../models/dueno.php';

session_start();

// Verificar autenticación
if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];



// Obtener datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$especie = trim($_POST['especie'] ?? '');
$raza = trim($_POST['raza'] ?? '');
$fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '') ?: null;
$color = trim($_POST['color'] ?? '');

// Validaciones
if ($nombre === '' || $especie === '') {
    die("Error: Completa los campos obligatorios. <a href='../registrar/registrar_mascota.php'>Volver</a>");
}

// Validar que el nombre solo contenga letras y espacios
$patron = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/';
if (!preg_match($patron, $nombre)) {
    die("Error: El nombre solo puede contener letras y espacios. <a href='../registrar/registrar_mascota.php'>Volver</a>");
}

// Verificar si ya existe una mascota con ese nombre para este usuario
$mascotaExistente = Mascota::obtenerPorNombre($nombre, $usuarioId);
if ($mascotaExistente) {
    die("Error: Ya existe una mascota con ese nombre. <a href='../registrar/registrar_mascota.php'>Volver</a>");
}

// Crear y guardar la mascota
$mascota = new Mascota($nombre, $especie, $usuarioId);
$mascota->setRaza($raza ?: null);
$mascota->setFechaNacimiento($fechaNacimiento);
$mascota->setColor($color ?: null);

if ($mascota->guardar()) {
    // Asociar dueños seleccionados
    if (isset($_POST['duenos']) && is_array($_POST['duenos'])) {
        foreach ($_POST['duenos'] as $duenoId) {
            $duenoId = intval($duenoId);
            $mascota->asociarDueno($duenoId);
        }
    }
    
    header('Location: ../index.php?exito=' . urlencode('Mascota registrada exitosamente'));
} else {
    die("Error: No se pudo registrar la mascota. <a href='../registrar/registrar_mascota.php'>Volver</a>");
}
exit;
?>