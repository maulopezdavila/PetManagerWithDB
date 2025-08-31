<?php
session_start();

// Eliminar toda la información de la sesión
session_destroy();

// Redirigir al login
header('Location: iniciar_sesion.php');
exit;
?>