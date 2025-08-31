<?php
require_once '../models/visita_medica.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

// Guardo el id del usuario logueado y el id de la visita que viene por la URL
$usuarioId = $_SESSION['auth']['usuario_id'];
$visitaId = intval($_GET['id'] ?? 0);

// Si el id de la visita no existe o es inválido, lo regreso a la lista de visitas
if ($visitaId <= 0) {
    header('Location: ../listas/listar_visitas.php');
    exit;
}

// Intentamos eliminar la visita médica
if (VisitaMedica::eliminar($visitaId, $usuarioId)) {
    // Si se eliminó bien, lo mando de vuelta con un mensaje de éxito
    header('Location: ../listas/listar_visitas.php?exito=' . urlencode('Visita eliminada exitosamente'));
} else {
    // Si falló, lo mando de vuelta con un mensaje de error
    header('Location: ../listas/listar_visitas.php?error=' . urlencode('Error al eliminar la visita'));
}
exit;
?>