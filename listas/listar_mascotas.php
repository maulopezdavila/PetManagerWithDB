<?php
require_once '../models/mascota.php';
require_once '../models/dueno.php';
require_once '../models/visita_medica.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener término de búsqueda si existe
$terminoBusqueda = trim($_GET['buscar'] ?? '');

// Obtener mascotas
if ($terminoBusqueda !== '') {
    $mascotas = Mascota::buscar($terminoBusqueda, $usuarioId);
} else {
    $mascotas = Mascota::obtenerTodas($usuarioId);
}

// Cargar dueños y visitas para cada mascota
foreach ($mascotas as $mascota) {
    $mascota->cargarDuenos();
    $mascota->cargarVisitas();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mascotas - Pet Manager</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <div class="contenedor-principal">
        <header class="encabezado">
            <div class="logo-titulo">
                <span class="material-symbols-outlined icono-principal">pets</span>
                <h1>Pet Manager</h1>
            </div>

            <!-- Menú de navegación -->
            <nav class="navegacion">
                <a href="../index.php" class="enlace-nav">Inicio</a>
                <a href="listar_mascotas.php" class="enlace-nav activo">Mascotas</a>
                <a href="listar_duenos.php" class="enlace-nav">Dueños</a>
                <a href="listar_visitas.php" class="enlace-nav">Visitas</a>
            </nav>
            <div class="acciones-usuario">
                <button class="boton-agregar" onclick="window.location.href='../registrar/registrar_mascota.php'">
                    <span class="material-symbols-outlined">add</span>
                    <span>Nueva Mascota</span>
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
                <h2>Mascotas</h2>
                <p>Aquí encontrarás todas las mascotas registradas</p>
            </div>

            <!-- Mostrar mensajes -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="mensaje-exito">
                    <?php echo htmlspecialchars($_GET['exito']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Barra de búsqueda y exportación a CSV -->
            <div class="controles-lista">
                <div class="busqueda-filtros">
                    <form method="GET" class="campo-busqueda">
                        <span class="material-symbols-outlined">search</span>
                        <input type="text" name="buscar" placeholder="Buscar por nombre, raza..." 
                               value="<?php echo htmlspecialchars($terminoBusqueda); ?>">
                    </form>
                </div>
                <form action="../otros/exportar_csv.php" method="POST" class="formulario-exportar">
                    <button type="submit" class="boton-exportar">
                        <span class="material-symbols-outlined">download</span>
                        Exportar CSV
                    </button>
                </form>
            </div>
            
            <!-- Tabla que lista todas las mascotas -->
            <div class="tabla-contenedor">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Especie</th>
                            <th>Raza</th>
                            <th>Dueños</th>
                            <th>Última Visita</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mascotas as $mascota): ?>
                            <tr>
                                <td class="nombre-mascota"><?php echo htmlspecialchars($mascota->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($mascota->getEspecie()); ?></td>
                                <td><?php echo htmlspecialchars($mascota->getRaza() ?? 'No especificada'); ?></td>
                                <td>
                                    <?php
                                    // Juntar todos los nombres de dueños en una sola línea
                                    $nombresDuenos = array_map(function($d) {
                                        return htmlspecialchars($d->getNombre());
                                    }, $mascota->getDuenos());
                                    echo implode(', ', $nombresDuenos) ?: 'Sin dueño';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Mostrar la fecha de la última visita médica
                                    $visitas = $mascota->getVisitas();
                                    echo !empty($visitas) ? htmlspecialchars($visitas[0]->getFecha()) : 'Ninguna';
                                    ?>
                                </td>
                                <td class="acciones">
                                    <!-- Botón para ver -->
                                    <a href="../otros/buscar_mascota.php?id=<?php echo $mascota->getId(); ?>" class="boton-accion">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <!-- Botón para editar -->
                                    <a href="../editar/editar_mascota.php?id=<?php echo $mascota->getId(); ?>" class="boton-accion">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <!-- Botón para eliminar con confirmación -->
                                    <a href="../eliminar/eliminar_mascota.php?id=<?php echo $mascota->getId(); ?>" class="boton-accion boton-eliminar" 
                                       onclick="return confirmarEliminacion('la mascota', '<?php echo htmlspecialchars($mascota->getNombre()); ?>')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación o mensaje si no hay mascotas -->
            <?php if (count($mascotas) > 0): ?>
                <div class="paginacion">
                    <p class="info-paginacion">Mostrando <?php echo count($mascotas); ?> mascotas</p>
                </div>
            <?php else: ?>
                <div class="estado-vacio">
                    <span class="material-symbols-outlined">pets</span>
                    <h3>No hay mascotas registradas</h3>
                    <p>Comienza registrando tu primera mascota</p>
                    <a href="../registrar/registrar_mascota.php" class="boton-principal">Registrar Primera Mascota</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>