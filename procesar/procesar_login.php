<?php
require_once '../models/usuario.php';

session_start();

// Obtener y limpiar datos del formulario
$nombreUsuario = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validar que no estén vacíos
if ($nombreUsuario === '' || $password === '') {
    header('Location: ../iniciar_sesion.php?error=' . urlencode('Por favor llena todos los campos'));
    exit;
}

// Sanitizar el nombre de usuario
$nombreUsuario = strip_tags($nombreUsuario);
$nombreUsuario = preg_replace('/[\x00-\x1F\x7F]/u', '', $nombreUsuario);

// Intentar autenticar al usuario
$usuario = Usuario::autenticar($nombreUsuario, $password);

if ($usuario) {
    // Crear sesión exitosa
    $_SESSION['auth'] = [
        'logueado' => true,
        'usuario_id' => $usuario->getId(),
        'usuario' => $usuario->getNombreUsuario()
    ];
    
    // Redirigir al dashboard
    header('Location: ../index.php');
    exit;
} else {
    // Error de autenticación
    header('Location: ../iniciar_sesion.php?error=' . urlencode('Usuario o contraseña incorrectos'));
    exit;
}
?>