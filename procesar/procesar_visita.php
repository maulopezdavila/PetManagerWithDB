<?php
require_once '../models/visita_medica.php';
require_once '../models/mascota.php';

session_start();

// Verificar autenticación
if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener datos del formulario
$mascotaId = intval($_POST['mascota'] ?? 0);
$fecha = trim($_POST['fecha'] ?? '');
$motivo = trim($_POST['motivo'] ?? '') ?: null;
$diagnostico = trim($_POST['diagnostico'] ?? '');
$tratamiento = trim($_POST['tratamiento'] ?? '');

// Validaciones
if (!$mascotaId || $fecha === '' || $diagnostico === '' || $tratamiento === '') {
    die("Error: Completa todos los campos obligatorios. <a href='../registrar/registrar_visita.php'>Volver</a>");
}

// Validar formato de fecha
$dt = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$dt || $dt->format('Y-m-d') !== $fecha) {
    die("Error: Fecha inválida. <a href='../registrar/registrar_visita.php'>Volver</a>");
}

// Verificar que la mascota pertenezca al usuario
$mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
if (!$mascota) {
    die("Error: Mascota no encontrada. <a href='../registrar/registrar_visita.php'>Volver</a>");
}

// Sanitizar textos
$diagnostico = strip_tags($diagnostico);
$tratamiento = strip_tags($tratamiento);
$motivo = $motivo ? strip_tags($motivo) : null;

// Crear y guardar la visita
$visita = new VisitaMedica($mascotaId, $fecha, $diagnostico, $tratamiento, $usuarioId);
$visita->setMotivo($motivo);

if ($visita->guardar()) {
    header('Location: ../index.php?exito=' . urlencode('Visita médica registrada exitosamente'));
} else {
    die("Error: No se pudo registrar la visita. <a href='../registrar/registrar_visita.php'>Volver</a>");
}
exit;
?>