<?php
require_once '../models/dueno.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener término de búsqueda si existe
$terminoBusqueda = trim($_GET['buscar'] ?? '');

// Obtener dueños
if ($terminoBusqueda !== '') {
    $duenos = Dueno::buscar($terminoBusqueda, $usuarioId);
} else {
    $duenos = Dueno::obtenerTodos($usuarioId);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dueños - Pet Manager</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <div class="contenedor-principal">

        <!-- Encabezado con logo, menú y acciones de usuario -->
        <header class="encabezado">
            <div class="logo-titulo">
                <span class="material-symbols-outlined icono-principal">pets</span>
                <h1>Pet Manager</h1>
            </div>
            <nav class="navegacion">
                <a href="../index.php" class="enlace-nav">Inicio</a>
                <a href="listar_mascotas.php" class="enlace-nav">Mascotas</a>
                <a href="listar_duenos.php" class="enlace-nav activo">Dueños</a>
                <a href="listar_visitas.php" class="enlace-nav">Visitas</a>
            </nav>
            <div class="acciones-usuario">
                <button class="boton-agregar" onclick="window.location.href='../registrar/registrar_dueno.php'">
                    <span class="material-symbols-outlined">add</span>
                    <span>Nuevo Dueño</span>
                </button>
                <div class="info-usuario">
                    <div class="avatar-usuario"></div>
                    <div class="datos-usuario">
                        <p class="nombre-usuario"><?php echo htmlspecialchars($_SESSION['auth']['usuario'] ?? ''); ?></p>
                        <p class="rol-usuario">Administrador</p>
                    </div>
                    <a href="../cerrar_sesion.php" class="enlace-cerrar">Cerrar sesión</a>
                </div>
            </div>
        </header>

        <main class="contenido-lista">
            <div class="encabezado-lista">
                <h2>Dueños de Mascotas</h2>
                <p>Aquí encontrarás todos los dueños registrados</p>
            </div>

            <!-- Mostrar mensajes -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="mensaje-exito">
                    <?php echo htmlspecialchars($_GET['exito']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Barra de búsqueda -->
            <div class="controles-lista">
                <div class="busqueda-filtros">
                    <form method="GET" class="campo-busqueda">
                        <span class="material-symbols-outlined">search</span>
                        <input type="text" name="buscar" placeholder="Buscar por nombre, teléfono..." 
                               value="<?php echo htmlspecialchars($terminoBusqueda); ?>">
                    </form>
                </div>
            </div>
            
            <!-- Tabla que muestra los dueños -->
            <div class="tabla-contenedor">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Mascotas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($duenos as $dueno): ?>
                            <tr>
                                <td class="nombre-dueno"><?php echo htmlspecialchars($dueno->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($dueno->getTelefono()); ?></td>
                                <td><?php echo htmlspecialchars($dueno->getEmail() ?? 'No especificado'); ?></td>
                                <td><?php echo htmlspecialchars($dueno->getDireccion() ?? 'No especificada'); ?></td>
                                <td>
                                    <?php

                                    // Listar mascotas que pertenecen a este dueño
                                    $mascotas = $dueno->obtenerMascotas();
                                    if (!empty($mascotas)) {
                                        $links = array_map(function($m) {
                                            return '<a class="mascota-link" href="../otros/buscar_mascota.php?id=' . $m->getId() . '">' . htmlspecialchars($m->getNombre()) . '</a>';
                                        }, $mascotas);
                                        echo implode(', ', $links);
                                    } else {
                                        echo 'Ninguna';
                                    }
                                    ?>
                                </td>
                                <td class="acciones">
                                    <!-- Botón para editar -->
                                    <a href="../editar/editar_dueno.php?id=<?php echo $dueno->getId(); ?>" class="boton-accion">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <!-- Botón para eliminar con confirmación -->
                                    <a href="../eliminar/eliminar_dueno.php?id=<?php echo $dueno->getId(); ?>" class="boton-accion boton-eliminar" 
                                       onclick="return confirmarEliminacion('el dueño', '<?php echo htmlspecialchars($dueno->getNombre()); ?>')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación o mensaje de lista vacía -->
            <?php if (count($duenos) > 0): ?>
                <div class="paginacion">
                    <p class="info-paginacion">Mostrando <?php echo count($duenos); ?> dueños</p>
                </div>
            <?php else: ?>
                <div class="estado-vacio">
                    <span class="material-symbols-outlined">person_add</span>
                    <h3>No hay dueños registrados</h3>
                    <p>Comienza registrando el primer dueño</p>
                    <a href="../registrar/registrar_dueno.php" class="boton-principal">Registrar Primer Dueño</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>