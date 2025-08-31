<?php
require_once '../models/visita_medica.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener todas las visitas
$visitas = VisitaMedica::obtenerTodas($usuarioId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas - Pet Manager</title>
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
                <a href="listar_mascotas.php" class="enlace-nav">Mascotas</a>
                <a href="listar_duenos.php" class="enlace-nav">Dueños</a>
                <a href="listar_visitas.php" class="enlace-nav activo">Visitas</a>
            </nav>
            <div class="acciones-usuario">
                <button class="boton-agregar" onclick="window.location.href='../registrar/registrar_visita.php'">
                    <span class="material-symbols-outlined">add</span>
                    <span>Nueva Visita</span>
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
                <h2>Visitas Médicas</h2>
                <p>Historial completo de todas las visitas médicas</p>
            </div>

            <!-- Mostrar mensajes -->
            <?php if (isset($_GET['exito'])): ?>
                <div class="mensaje-exito">
                    <?php echo htmlspecialchars($_GET['exito']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Barra de búsqueda para filtrar visitas -->
            <div class="controles-lista">
                <div class="busqueda-filtros">
                    <div class="campo-busqueda">
                        <span class="material-symbols-outlined">search</span>
                        <input type="text" placeholder="Buscar por mascota, diagnóstico..." id="buscarVisita">
                    </div>
                </div>
            </div>
            
            <!-- Tabla donde muestro todas las visitas -->
            <div class="tabla-contenedor">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Mascota</th>
                            <th>Motivo</th>
                            <th>Diagnóstico</th>
                            <th>Tratamiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Recorro todas las visitas -->
                        <?php foreach ($visitas as $visita): ?>
                            <tr>
                                <td class="fecha-visita"><?php echo htmlspecialchars($visita->getFecha()); ?></td>
                                <td class="nombre-mascota"><?php echo htmlspecialchars($visita->nombreMascota ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($visita->getMotivo() ?? 'No especificado'); ?></td>
                                <td class="diagnostico-celda">
                                    <?php 
                                    // Corto el diagnóstico si es muy largo
                                    $diagnostico = $visita->getDiagnostico();
                                    echo htmlspecialchars(substr($diagnostico, 0, 50)) . (strlen($diagnostico) > 50 ? '...' : '');
                                    ?>
                                </td>
                                <td class="tratamiento-celda">
                                    <?php 
                                    // Igual con el tratamiento, lo corto si está larguísimo
                                    $tratamiento = $visita->getTratamiento();
                                    echo htmlspecialchars(substr($tratamiento, 0, 50)) . (strlen($tratamiento) > 50 ? '...' : '');
                                    ?>
                                </td>
                                <td class="acciones">
                                    <!-- Botón pa’ editar la visita -->
                                    <a href="../editar/editar_visita.php?id=<?php echo $visita->getId(); ?>" class="boton-accion">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <!-- Botón pa’ eliminar con confirmación -->
                                    <a href="../eliminar/eliminar_visita.php?id=<?php echo $visita->getId(); ?>" class="boton-accion boton-eliminar" 
                                       onclick="return confirmarEliminacion('la visita', '<?php echo htmlspecialchars($visita->getFecha()); ?>')">
                                        <span class="material-symbols-outlined">delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Si hay visitas, muestro cuántas -->
            <?php if (count($visitas) > 0): ?>
                <div class="paginacion">
                    <p class="info-paginacion">Mostrando <?php echo count($visitas); ?> visitas</p>
                </div>
            <?php else: ?>
                <!-- Si no hay visitas registradas, muestro este mensajito -->
                <div class="estado-vacio">
                    <span class="material-symbols-outlined">medical_services</span>
                    <h3>No hay visitas registradas</h3>
                    <p>Comienza registrando la primera visita médica</p>
                    <a href="../registrar/registrar_visita.php" class="boton-principal">Registrar Primera Visita</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="../scripts.js"></script>
    <script>
        // Configurar búsqueda en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const inputBusqueda = document.getElementById('buscarVisita');
            if (inputBusqueda) {
                inputBusqueda.addEventListener('input', function() {
                    filtrarTablaVisitas(this.value);
                });
            }
        });
        
        function filtrarTablaVisitas(termino) {
            const tabla = document.querySelector('.tabla-datos tbody');
            if (!tabla) return;
            
            const filas = tabla.getElementsByTagName('tr');
            
            for (let i = 0; i < filas.length; i++) {
                const fila = filas[i];
                const celdas = fila.getElementsByTagName('td');
                let mostrar = false;
                
                for (let j = 0; j < 5; j++) {
                    if (celdas[j]) {
                        const texto = celdas[j].textContent.toLowerCase();
                        if (texto.includes(termino.toLowerCase())) {
                            mostrar = true;
                            break;
                        }
                    }
                }
                
                fila.style.display = mostrar ? '' : 'none';
            }
        }
    </script>
</body>
</html>