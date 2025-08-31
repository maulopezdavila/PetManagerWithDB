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

// Obtener mascota por ID o nombre
$mascotaId = intval($_GET['id'] ?? 0);
$nombre = trim($_GET['nombre'] ?? '');

$mascota = null;
// Si hay ID lo usamos para buscar
if ($mascotaId > 0) {
    $mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
} else if ($nombre !== '') {
    // Si no hay ID, pero sí nombre, buscamos por nombre
    $mascota = Mascota::obtenerPorNombre($nombre, $usuarioId);
}

// Si no encontramos la mascota, lo mandamos a la lista de mascotas
if (!$mascota) {
    header('Location: ../listas/listar_mascotas.php');
    exit;
}

// Cargar relaciones
$mascota->cargarDuenos();
$mascota->cargarVisitas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de <?php echo htmlspecialchars($mascota->getNombre()); ?> - Pet Manager</title>
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
            <nav class="navegacion">
                <a href="../index.php" class="enlace-nav">Inicio</a>
                <a href="../listas/listar_mascotas.php" class="enlace-nav">Mascotas</a>
                <a href="../listas/listar_duenos.php" class="enlace-nav">Dueños</a>
                <a href="../listas/listar_visitas.php" class="enlace-nav">Visitas</a>
            </nav>
            <div class="acciones-usuario">
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

         <!-- Main con el historial de la mascota -->
        <main class="contenido-historial">
            <div class="encabezado-historial">
                <span class="material-symbols-outlined icono-mascota">pets</span>
                <h1>Historial de <?php echo htmlspecialchars($mascota->getNombre()); ?></h1>
                <p>Información completa</p>
            </div>
            
            <!-- Tarjetas con info general, dueños y visitas -->
            <div class="tarjetas-info">
                <!-- Info general -->
                <div class="tarjeta-info">
                    <h2>
                        <span class="material-symbols-outlined">info</span>
                        Información General
                    </h2>
                    <div class="contenido-info">
                        <div class="dato-info">
                            <strong>Nombre:</strong> <?php echo htmlspecialchars($mascota->getNombre()); ?>
                        </div>
                        <div class="dato-info">
                            <strong>Especie:</strong> <?php echo htmlspecialchars($mascota->getEspecie()); ?>
                        </div>
                        <?php if ($mascota->getRaza()): ?>
                        <div class="dato-info">
                            <strong>Raza:</strong> <?php echo htmlspecialchars($mascota->getRaza()); ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($mascota->getColor()): ?>
                        <div class="dato-info">
                            <strong>Descripción:</strong> <?php echo htmlspecialchars($mascota->getColor()); ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($mascota->getFechaNacimiento()): ?>
                        <div class="dato-info">
                            <strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($mascota->getFechaNacimiento()); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Dueños -->
                <div class="tarjeta-info">
                    <h2>
                        <span class="material-symbols-outlined">person</span>
                        Dueños
                    </h2>
                    <div class="contenido-info">
                        <?php
                        $duenos = $mascota->getDuenos();
                        if (!empty($duenos)):
                            foreach ($duenos as $dueno):
                        ?>
                            <div class="info-dueno">
                                <strong>Nombre:</strong> <?php echo htmlspecialchars($dueno->getNombre()); ?><br>
                                <strong>Teléfono:</strong> <?php echo htmlspecialchars($dueno->getTelefono()); ?>
                                <?php if ($dueno->getEmail()): ?>
                                    <br><strong>Email:</strong> <?php echo htmlspecialchars($dueno->getEmail()); ?>
                                <?php endif; ?>
                                <?php if ($dueno->getDireccion()): ?>
                                    <br><strong>Dirección:</strong> <?php echo htmlspecialchars($dueno->getDireccion()); ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <p class="sin-datos">Sin dueños registrados</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Visitas médicas -->
                <div class="tarjeta-info tarjeta-completa">
                    <h2>
                        <span class="material-symbols-outlined">medical_services</span>
                        Visitas Médicas
                    </h2>
                    <div class="contenido-info">
                        <?php
                        $visitas = $mascota->getVisitas();
                        if (!empty($visitas)):
                            foreach ($visitas as $visita):
                        ?>
                            <div class="info-visita">
                                <div class="fecha-visita"><?php echo htmlspecialchars($visita->getFecha()); ?></div>
                                <?php if ($visita->getMotivo()): ?>
                                    <div class="motivo-visita">
                                        <strong>Motivo:</strong> <?php echo htmlspecialchars($visita->getMotivo()); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="diagnostico-visita">
                                    <strong>Diagnóstico:</strong> <?php echo htmlspecialchars($visita->getDiagnostico()); ?>
                                </div>
                                <div class="tratamiento-visita">
                                    <strong>Tratamiento:</strong> <?php echo htmlspecialchars($visita->getTratamiento()); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <p class="sin-datos">No hay visitas registradas para esta mascota.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Botón de volver al inicio -->
            <div class="acciones-historial">
                <a href="../index.php" class="boton-principal">← Volver al inicio</a>
            </div>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>

<style>
.contenido-historial {
    flex: 1;
    padding: 2rem 1.5rem;
    max-width: 80rem;
    margin: 0 auto;
}

.encabezado-historial {
    text-align: center;
    margin-bottom: 2rem;
}

.icono-mascota {
    font-size: 4rem;
    color: var(--color-primario);
    display: block;
    margin-bottom: 1rem;
}

.encabezado-historial h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.encabezado-historial p {
    color: var(--color-texto-secundario);
    font-size: 1.125rem;
}

.tarjetas-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.tarjeta-completa {
    grid-column: 1 / -1;
}

.tarjeta-info {
    background-color: var(--color-superficie);
    border-radius: 1rem;
    padding: 1.5rem;
}

.tarjeta-info h2 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--color-texto);
}

.contenido-info {
    space-y: 1rem;
}

.dato-info,
.info-dueno,
.info-visita {
    background-color: var(--color-fondo);
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.info-visita {
    border-left: 4px solid var(--color-primario);
}

.fecha-visita {
    font-weight: 700;
    color: var(--color-primario);
    margin-bottom: 0.5rem;
}

.sin-datos {
    color: var(--color-texto-secundario);
    font-style: italic;
    text-align: center;
    padding: 2rem;
}

.acciones-historial {
    text-align: center;
}
</style>