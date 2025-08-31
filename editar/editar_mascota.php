<?php
require_once '../models/mascota.php';
require_once '../models/dueno.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];
$mascotaId = intval($_GET['id'] ?? 0);

// Obtener la mascota
$mascota = Mascota::obtenerPorId($mascotaId, $usuarioId);
if (!$mascota) {
    header('Location: ../listas/listar_mascotas.php');
    exit;
}

// Cargar dueños y obtener todos los dueños disponibles
$mascota->cargarDuenos();
$todosDuenos = Dueno::obtenerTodos($usuarioId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Mascota - Pet Manager</title>
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
                <a href="../listas/listar_mascotas.php" class="enlace-nav activo">Mascotas</a>
                <a href="../listas/listar_duenos.php" class="enlace-nav">Dueños</a>
                <a href="../listas/listar_visitas.php" class="enlace-nav">Visitas</a>
            </nav>

            <!-- Info del usuario logueado y link para cerrar sesión -->
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
                <h2>Editar Mascota</h2>
                <p>Modifica los datos de la mascota</p>
            </div>

            <!-- Formulario para editar la mascota -->
            <form action="../procesar/procesar_editar_mascota.php" method="POST" class="formulario-principal" id="formMascota">
                <input type="hidden" name="mascota_id" value="<?php echo $mascota->getId(); ?>">
                
                <div class="campos-grid">

                    <!-- Nombre de la mascota -->
                    <div class="campo-formulario">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($mascota->getNombre()); ?>" 
                               placeholder="Ingresa el nombre de la mascota" required>
                        <div class="error-campo" id="errorNombre"></div>
                    </div>

                    <!-- Especie (select con opciones) -->
                    <div class="campo-formulario">
                        <label for="especie">Especie</label>
                        <select id="especie" name="especie" required>
                            <option value="Perro" <?php echo $mascota->getEspecie() === 'Perro' ? 'selected' : ''; ?>>Perro</option>
                            <option value="Gato" <?php echo $mascota->getEspecie() === 'Gato' ? 'selected' : ''; ?>>Gato</option>
                            <option value="Ave" <?php echo $mascota->getEspecie() === 'Ave' ? 'selected' : ''; ?>>Ave</option>
                            <option value="Conejo" <?php echo $mascota->getEspecie() === 'Conejo' ? 'selected' : ''; ?>>Conejo</option>
                            <option value="Otro" <?php echo $mascota->getEspecie() === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>
                    
                    <!-- Raza -->
                    <div class="campo-formulario">
                        <label for="raza">Raza</label>
                        <input type="text" id="raza" name="raza" 
                               value="<?php echo htmlspecialchars($mascota->getRaza() ?? ''); ?>" 
                               placeholder="Ej. Golden Retriever">
                    </div>

                    <!-- Fecha de nacimiento -->
                    <div class="campo-formulario">
                        <label for="fechaNacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fechaNacimiento" name="fecha_nacimiento" 
                               value="<?php echo htmlspecialchars($mascota->getFechaNacimiento() ?? ''); ?>">
                    </div>
                </div>

                <!-- Descripción general de la mascota -->
                <div class="campo-formulario">
                    <label for="color">Descripción de la mascota</label>
                    <input type="text" id="color" name="color" 
                           value="<?php echo htmlspecialchars($mascota->getColor() ?? ''); ?>" 
                           placeholder="Describe de manera general la mascota">
                </div>

                <!-- Lista de dueños asociados -->
                <div class="campo-formulario">
                    <label for="duenos">Dueños asociados (opcional)</label>
                    <select id="duenos" name="duenos[]" multiple>
                        <option value="" disabled>Selecciona uno o más dueños</option>
                        <?php
                        // Los dueños que ya tiene la mascota para marcarlos en el select
                        $duenosAsociados = array_map(function($d) { return $d->getId(); }, $mascota->getDuenos());
                        foreach ($todosDuenos as $dueno):
                            $selected = in_array($dueno->getId(), $duenosAsociados) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $dueno->getId(); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($dueno->getNombre()) . ' (' . htmlspecialchars($dueno->getTelefono()) . ')'; ?>
                            </option>
                        <?php endforeach; ?>

                        <!-- Si no hay dueños, se muestra un aviso -->
                        <?php if (empty($todosDuenos)): ?>
                            <option value="" disabled>No hay dueños registrados aún</option>
                        <?php endif; ?>
                    </select>
                    <p class="nota-campo">Mantén presionada la tecla Ctrl para seleccionar múltiples dueños.</p>
                </div>
                
                <!-- Botones para cancelar o guardar -->
                <div class="botones-formulario">
                    <a href="../listas/listar_mascotas.php" class="boton-secundario">Cancelar</a>
                    <button type="submit" class="boton-principal">Guardar Cambios</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>