<?php
require_once '../models/dueno.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

// Guardamos el id del usuario y el id del dueño que viene por GET
$usuarioId = $_SESSION['auth']['usuario_id'];
$duenoId = intval($_GET['id'] ?? 0);

// Si el id no es válido, regresamos a la lista de dueños
if ($duenoId <= 0) {
    header('Location: ../listas/listar_duenos.php');
    exit;
}

// Intentamos eliminar al dueño
if (Dueno::eliminar($duenoId, $usuarioId)) {
    // Si se pudo eliminar, redirige con un mensaje de éxito
    header('Location: ../listas/listar_duenos.php?exito=' . urlencode('Dueño eliminado exitosamente'));
} else {
    // Si no, redirige con un mensaje de error
    header('Location: ../listas/listar_duenos.php?error=' . urlencode('Error al eliminar el dueño'));
}
exit;
?>