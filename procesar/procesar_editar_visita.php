<?php
require_once '../models/visita_medica.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener datos del formulario
$visitaId = intval($_POST['visita_id'] ?? 0);
$mascotaId = intval($_POST['mascota_id'] ?? 0);
$fecha = trim($_POST['fecha'] ?? '');
$motivo = trim($_POST['motivo'] ?? '') ?: null;
$diagnostico = trim($_POST['diagnostico'] ?? '');
$tratamiento = trim($_POST['tratamiento'] ?? '');

// Validaciones
if (!$visitaId || !$mascotaId || $fecha === '' || $diagnostico === '' || $tratamiento === '') {
    die("Error: Datos inválidos. <a href='../listas/listar_visitas.php'>Volver</a>");
}

// Validar fecha
$dt = DateTime::createFromFormat('Y-m-d', $fecha);
if (!$dt || $dt->format('Y-m-d') !== $fecha) {
    die("Error: Fecha inválida. <a href='../editar/editar_visita.php?id={$visitaId}'>Volver</a>");
}

// Verificar que la mascota pertenezca al usuario
$mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
if (!$mascota) {
    die("Error: Mascota no encontrada. <a href='../listas/listar_visitas.php'>Volver</a>");
}

// Obtener la visita
$visita = VisitaMedica::obtenerPorId($visitaId, $usuarioId);
if (!$visita) {
    die("Error: Visita no encontrada. <a href='../listas/listar_visitas.php'>Volver</a>");
}

// Sanitizar textos
$diagnostico = strip_tags($diagnostico);
$tratamiento = strip_tags($tratamiento);
$motivo = $motivo ? strip_tags($motivo) : null;

// Actualizar datos
$visita->setMascotaId($mascotaId);
$visita->setFecha($fecha);
$visita->setMotivo($motivo);
$visita->setDiagnostico($diagnostico);
$visita->setTratamiento($tratamiento);

if ($visita->actualizar()) {
    header('Location: ../listas/listar_visitas.php?exito=' . urlencode('Visita actualizada exitosamente'));
} else {
    die("Error: No se pudo actualizar la visita. <a href='../editar/editar_visita.php?id={$visitaId}'>Volver</a>");
}
exit;
?>