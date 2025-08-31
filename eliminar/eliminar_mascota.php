<?php
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

// Guardamos el id del usuario logueado y el id de la mascota que viene por GET
$usuarioId = $_SESSION['auth']['usuario_id'];
$mascotaId = intval($_GET['id'] ?? 0);

// Si el id de la mascota no sirve (es 0 o menor), lo mandamos de vuelta a la lista
if ($mascotaId <= 0) {
    header('Location: ../listas/listar_mascotas.php');
    exit;
}

// Intentamos eliminar la mascota
if (Mascota::eliminar($mascotaId, $usuarioId)) {
    // Si se eliminó bien, redirige con un mensaje de éxito
    header('Location: ../listas/listar_mascotas.php?exito=' . urlencode('Mascota eliminada exitosamente'));
} else {
    // Si falló, redirige con un mensaje de error
    header('Location: ../listas/listar_mascotas.php?error=' . urlencode('Error al eliminar la mascota'));
}
exit;
?>