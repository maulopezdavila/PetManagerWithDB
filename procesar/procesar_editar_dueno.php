<?php
require_once '../models/dueno.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener datos del formulario
$duenoId = intval($_POST['dueno_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? '') ?: null;
$direccion = trim($_POST['direccion'] ?? '') ?: null;

// Validaciones
if (!$duenoId || $nombre === '' || $telefono === '') {
    die("Error: Datos inválidos. <a href='../listas/listar_duenos.php'>Volver</a>");
}

// Validar formato
$patronNombre = '/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/';
$patronTelefono = '/^[0-9\-\+$$$$\s]+$/';

if (!preg_match($patronNombre, $nombre)) {
    die("Error: Nombre inválido. <a href='../editar/editar_dueno.php?id={$duenoId}'>Volver</a>");
}

if (!preg_match($patronTelefono, $telefono)) {
    die("Error: Teléfono inválido. <a href='../editar/editar_dueno.php?id={$duenoId}'>Volver</a>");
}

// Obtener el dueño
$dueno = Dueno::obtenerPorId($duenoId, $usuarioId);
if (!$dueno) {
    die("Error: Dueño no encontrado. <a href='../listas/listar_duenos.php'>Volver</a>");
}

// Actualizar datos
$dueno->setNombre($nombre);
$dueno->setTelefono($telefono);
$dueno->setEmail($email);
$dueno->setDireccion($direccion);

if ($dueno->actualizar()) {
    header('Location: ../listas/listar_duenos.php?exito=' . urlencode('Dueño actualizado exitosamente'));
} else {
    die("Error: No se pudo actualizar el dueño. <a href='../editar/editar_dueno.php?id={$duenoId}'>Volver</a>");
}
exit;
?>