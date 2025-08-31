<?php
require_once '../models/visita_medica.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];
$visitaId = intval($_GET['id'] ?? 0);

// Obtener la visita
$visita = VisitaMedica::obtenerPorId($visitaId, $usuarioId);
if (!$visita) {
    header('Location: ../listas/listar_visitas.php');
    exit;
}

// Obtener todas las mascotas para el selector
$mascotas = Mascota::obtenerTodas($usuarioId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Visita - Pet Manager</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../estilos.css">
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
                <a href="../index.php" class="enlace-nav">Inicio</a>
                <a href="../listas/listar_mascotas.php" class="enlace-nav">Mascotas</a>
                <a href="../listas/listar_duenos.php" class="enlace-nav">Dueños</a>
                <a href="../listas/listar_visitas.php" class="enlace-nav activo">Visitas</a>
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

        <main class="contenido-formulario">
            <div class="encabezado-pagina">
                <h2>Editar Visita Médica</h2>
                <p>Modifica los datos de la visita</p>
            </div>

            <!-- Formulario para editar la visita -->
            <form action="../procesar/procesar_editar_visita.php" method="POST" class="formulario-principal" id="formVisita">
                <input type="hidden" name="visita_id" value="<?php echo $visita->getId(); ?>">
                
                <!-- Fecha de la visita -->
                <div class="campos-grid">
                    <div class="campo-formulario">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" 
                               value="<?php echo htmlspecialchars($visita->getFecha()); ?>" required>
                    </div>

                    <!-- Mascota asociada a la visita -->
                    <div class="campo-formulario">
                        <label for="mascota_id">Mascota</label>
                        <select id="mascota_id" name="mascota_id" required>
                            <?php foreach ($mascotas as $mascota): ?>
                                <option value="<?php echo $mascota->getId(); ?>" 
                                        <?php echo $mascota->getId() == $visita->getMascotaId() ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mascota->getNombre()) . ' (' . htmlspecialchars($mascota->getEspecie()) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Motivo de la visita -->
                <div class="campo-formulario">
                    <label for="motivo">Motivo de la visita</label>
                    <input type="text" id="motivo" name="motivo" 
                           value="<?php echo htmlspecialchars($visita->getMotivo() ?? ''); ?>" 
                           placeholder="Ej. Revisión anual, vacunación">
                </div>
                
                <!-- Diagnóstico -->
                <div class="campo-formulario">
                    <label for="diagnostico">Diagnóstico</label>
                    <textarea id="diagnostico" name="diagnostico" placeholder="Describe el diagnóstico..." required><?php echo htmlspecialchars($visita->getDiagnostico()); ?></textarea>
                </div>
                
                <!-- Tratamiento -->
                <div class="campo-formulario">
                    <label for="tratamiento">Tratamiento</label>
                    <textarea id="tratamiento" name="tratamiento" placeholder="Describe el tratamiento..." required><?php echo htmlspecialchars($visita->getTratamiento()); ?></textarea>
                </div>
                
                <!-- Botones para guardar o cancelar -->
                <div class="botones-formulario">
                    <a href="../listas/listar_visitas.php" class="boton-secundario">Cancelar</a>
                    <button type="submit" class="boton-principal">Guardar Cambios</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>