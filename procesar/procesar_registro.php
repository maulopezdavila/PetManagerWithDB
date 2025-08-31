<?php
require_once '../models/usuario.php';

session_start();

// Obtener datos del formulario
$nombreUsuario = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmarPassword = trim($_POST['confirmar_password'] ?? '');

// Validaciones básicas
if ($nombreUsuario === '' || $password === '' || $confirmarPassword === '') {
    header('Location: ../registro_usuario.php?error=' . urlencode('Por favor completa todos los campos'));
    exit;
}

// Validar que las contraseñas coincidan
if ($password !== $confirmarPassword) {
    header('Location: ../registro_usuario.php?error=' . urlencode('Las contraseñas no coinciden'));
    exit;
}

// Validar formato del usuario
$patronUsuario = '/^[a-zA-Z0-9_]+$/';
if (!preg_match($patronUsuario, $nombreUsuario)) {
    header('Location: ../registro_usuario.php?error=' . urlencode('El usuario solo puede contener letras, números y guiones bajos'));
    exit;
}

// Validar longitud del usuario
if (strlen($nombreUsuario) < 3 || strlen($nombreUsuario) > 20) {
    header('Location: ../registro_usuario.php?error=' . urlencode('El usuario debe tener entre 3 y 20 caracteres'));
    exit;
}

// Validar longitud de la contraseña
if (strlen($password) < 8) {
    header('Location: ../registro_usuario.php?error=' . urlencode('La contraseña debe tener al menos 8 caracteres'));
    exit;
}

// Verificar si el usuario ya existe
if (Usuario::existeUsuario($nombreUsuario)) {
    header('Location: ../registro_usuario.php?error=' . urlencode('Este usuario ya existe'));
    exit;
}

// Crear hash de la contraseña
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

// Crear y guardar nuevo usuario
$nuevoUsuario = new Usuario($nombreUsuario, $hashPassword);
if ($nuevoUsuario->guardar()) {
    header('Location: ../registro_usuario.php?exito=' . urlencode('Usuario registrado exitosamente. Ya puedes iniciar sesión.'));
} else {
    header('Location: ../registro_usuario.php?error=' . urlencode('Error al registrar usuario. Intenta nuevamente.'));
}
exit;
?>