<?php
require_once 'models/mascota.php';
require_once 'models/dueno.php';
require_once 'models/visita_medica.php';
require_once 'models/usuario.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener estadísticas básicas
$totalMascotas = count(Mascota::obtenerTodas($usuarioId));
$totalDuenos = count(Dueno::obtenerTodos($usuarioId));
$totalVisitas = count(VisitaMedica::obtenerTodas($usuarioId));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Manager - Inicio</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor-principal">
        <!-- Header con logo, navegación y usuario -->
        <header class="encabezado">
            <div class="logo-titulo">
                <span class="material-symbols-outlined icono-principal">pets</span>
                <h1>Pet Manager</h1>
            </div>
            <nav class="navegacion">
                <a href="index.php" class="enlace-nav activo">Inicio</a>
                <a href="listas/listar_mascotas.php" class="enlace-nav">Mascotas (<?php echo $totalMascotas; ?>)</a>
                <a href="listas/listar_duenos.php" class="enlace-nav">Dueños (<?php echo $totalDuenos; ?>)</a>
                <a href="listas/listar_visitas.php" class="enlace-nav">Visitas (<?php echo $totalVisitas; ?>)</a>
            </nav>
            <div class="acciones-usuario">
                <div class="info-usuario">
                    <div class="avatar-usuario"></div>
                    <div class="datos-usuario">
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['auth']['usuario'] ?? ''); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                    <a href="cerrar_sesion.php" class="enlace-cerrar">Cerrar sesión</a>
                </div>
            </div>
        </header>

        <!-- Main con bienvenida, tarjetas y búsqueda -->
        <main class="contenido-principal">
            <div class="seccion-bienvenida">
                <h2>Bienvenido a Pet Manager - Sistema de Gestión de Mascotas</h2>
                <p>Gestiona toda la información relacionada a las mascotas</p>
            </div>

            <!-- Mostrar mensajes -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="mensaje-exito" style="max-width: 80rem; margin: 0 auto 2rem; padding: 1rem; background: var(--color-primario); color: var(--color-fondo); border-radius: 0.5rem; text-align: center;">
                    <?php echo htmlspecialchars($_GET['exito']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Tarjetas rápidas para registrar o listar cosas -->
            <div class="tarjetas-dashboard">
                <a href="registrar/registrar_mascota.php" class="tarjeta-accion">
                    <div class="icono-tarjeta">
                        <span class="material-symbols-outlined">pets</span>
                    </div>
                    <h3>Registrar Mascota</h3>
                </a>

                <a href="registrar/registrar_dueno.php" class="tarjeta-accion">
                    <div class="icono-tarjeta">
                        <span class="material-symbols-outlined">person_add</span>
                    </div>
                    <h3>Registrar Dueño</h3>
                </a>

                <a href="registrar/registrar_visita.php" class="tarjeta-accion">
                    <div class="icono-tarjeta">
                        <span class="material-symbols-outlined">medical_services</span>
                    </div>
                    <h3>Registrar Visita</h3>
                </a>

                <a href="listas/listar_mascotas.php" class="tarjeta-accion">
                    <div class="icono-tarjeta">
                        <span class="material-symbols-outlined">list_alt</span>
                    </div>
                    <h3>Listado de Mascotas</h3>
                </a>
            </div>
            
            <!-- Formulario rápido para buscar mascota por nombre -->
            <div class="buscar-seccion">
                <h3>Buscar Mascota</h3>
                <form action="otros/buscar_mascota.php" method="GET" class="formulario-busqueda">
                    <div class="campo-busqueda">
                        <span class="material-symbols-outlined">search</span>
                        <input type="text" name="nombre" placeholder="Nombre de la mascota" required>
                    </div>
                    <button type="submit" class="boton-principal">Buscar</button>
                </form>
            </div>
        </main>
    </div>

    <script src="scripts.js"></script>
</body>
</html>