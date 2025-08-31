<?php
require_once '../models/dueno.php';
require_once '../models/mascota.php';

session_start();

// Verificar autenticación
if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? '') ?: null;
$direccion = trim($_POST['direccion'] ?? '') ?: null;
$mascotaId = intval($_POST['mascota'] ?? 0) ?: null;

// Validaciones básicas
if ($nombre === '' || $telefono === '') {
    die("Error: Completa los campos obligatorios. <a href='../registrar/registrar_dueno.php'>Volver</a>");
}

// Patrones de validación
$patronNombre = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/';
$patronTelefono = '/^[0-9\-\+$$$$\s]+$/';

// Validar nombre
if (!preg_match($patronNombre, $nombre)) {
    die("Error: El nombre solo puede contener letras y espacios. <a href='../registrar/registrar_dueno.php'>Volver</a>");
}

// Validar teléfono
if (!preg_match($patronTelefono, $telefono)) {
    die("Error: Formato de teléfono inválido. <a href='../registrar/registrar_dueno.php'>Volver</a>");
}

// Crear y guardar el dueño
$dueno = new Dueno($nombre, $telefono, $usuarioId);
$dueno->setEmail($email);
$dueno->setDireccion($direccion);

if ($dueno->guardar()) {
    // Si se seleccionó una mascota, asociarla con el dueño
    if ($mascotaId) {
        $mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
        if ($mascota) {
            $mascota->asociarDueno($dueno->getId());
        }
    }
    
    header('Location: ../index.php?exito=' . urlencode('Dueño registrado exitosamente'));
} else {
    die("Error: No se pudo registrar el dueño. <a href='../registrar/registrar_dueno.php'>Volver</a>");
}
exit;
?>