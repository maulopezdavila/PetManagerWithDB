<?php
session_start();

if (!empty($_SESSION['auth']['logueado'])) {
    header('Location: index.php');
    exit;
}

$error = $_GET['error'] ?? '';
$exito = $_GET['exito'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Manager - Registro</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body class="cuerpo-login">
    <div class="contenedor-login">
        <div class="formulario-login">
            <header class="encabezado-login">
                <div class="logo-login">
                    <span class="material-symbols-outlined">pets</span>
                    <h1>Pet Manager</h1>
                </div>
            </header>

            <!-- Contenido del registro -->
            <main class="contenido-login">
                <div class="bienvenida-login">
                    <h2>Crear cuenta nueva</h2>
                    <p>Regístrate para comenzar a usar Pet Manager</p>
                </div>

                <!-- Mensaje de error si algo sale mal -->
                <?php if ($error): ?>
                    <div class="mensaje-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Mensaje de éxito si todo salió bien -->
                <?php if ($exito): ?>
                    <div class="mensaje-exito">
                        <?php echo htmlspecialchars($exito); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario de registro -->
                <form action="procesar/procesar_registro.php" method="POST" class="formulario" id="formRegistro">
                    <div class="campo-entrada">
                        <span class="material-symbols-outlined">person</span>
                        <input type="text" name="usuario" placeholder="Nombre de usuario" required 
                               minlength="3" maxlength="20" id="usuarioInput">
                        <div class="error-campo" id="errorUsuario"></div>
                    </div>

                    <div class="campo-entrada">
                        <span class="material-symbols-outlined">lock</span>
                        <input type="password" name="password" placeholder="Contraseña" required 
                               minlength="8" id="passwordInput">
                        <div class="error-campo" id="errorPassword"></div>
                    </div>

                    <div class="campo-entrada">
                        <span class="material-symbols-outlined">lock</span>
                        <input type="password" name="confirmar_password" placeholder="Confirmar contraseña" 
                               required id="confirmarPasswordInput">
                        <div class="error-campo" id="errorConfirmar"></div>
                    </div>

                    <button type="submit" class="boton-login">Crear Cuenta</button>
                </form>
                
                <!-- Link para iniciar sesión si ya tiene cuenta -->
                <p class="enlace-registro">
                    ¿Ya tienes cuenta?
                    <a href="iniciar_sesion.php">Inicia sesión</a>
                </p>
            </main>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>