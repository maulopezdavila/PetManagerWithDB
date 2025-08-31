<?php
require_once '../models/visita_medica.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];

// Obtener mascotas disponibles
$mascotas = Mascota::obtenerTodas($usuarioId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Visita - Pet Manager</title>
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

        <!-- Main con el formulario para registrar una visita médica -->
        <main class="contenido-formulario">
            <div class="encabezado-pagina">
                <h2>Nueva Visita Médica</h2>
                <p>Completa el formulario para registrar una nueva visita médica</p>
            </div>

            <form action="../procesar/procesar_visita.php" method="POST" class="formulario-principal" id="formVisita">
                <!-- Fecha y mascota asociada -->
                <div class="campos-grid">
                    <div class="campo-formulario">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="campo-formulario">
                        <label for="mascota">Mascota</label>
                        <select id="mascota" name="mascota" required>
                            <option value="" disabled selected>Selecciona una mascota</option>
                            <?php foreach ($mascotas as $mascota): ?>
                                <option value="<?php echo $mascota->getId(); ?>">
                                    <?php echo htmlspecialchars($mascota->getNombre()) . ' (' . htmlspecialchars($mascota->getEspecie()) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Motivo de la visita, diagnóstico y tratamiento -->
                <div class="campo-formulario">
                    <label for="motivo">Motivo de la visita</label>
                    <input type="text" id="motivo" name="motivo" placeholder="Ej. Revisión anual, vacunación">
                </div>

                <div class="campo-formulario">
                    <label for="diagnostico">Diagnóstico</label>
                    <textarea id="diagnostico" name="diagnostico" placeholder="Describe el diagnóstico..." required></textarea>
                </div>

                <div class="campo-formulario">
                    <label for="tratamiento">Tratamiento</label>
                    <textarea id="tratamiento" name="tratamiento" placeholder="Describe el tratamiento..." required></textarea>
                </div>
                
                <!-- Botones: cancelar o guardar -->
                <div class="botones-formulario">
                    <a href="../index.php" class="boton-secundario">Cancelar</a>
                    <button type="submit" class="boton-principal">Guardar Visita</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>