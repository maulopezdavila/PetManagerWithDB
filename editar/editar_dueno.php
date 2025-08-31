<?php
require_once '../models/dueno.php';
require_once '../models/mascota.php';

session_start();

if (empty($_SESSION['auth']['logueado'])) {
    header('Location: ../iniciar_sesion.php');
    exit;
}

$usuarioId = $_SESSION['auth']['usuario_id'];
$duenoId = intval($_GET['id'] ?? 0);

// Obtener el dueño
$dueno = Dueno::obtenerPorId($duenoId, $usuarioId);
if (!$dueno) {
    header('Location: ../listas/listar_duenos.php');
    exit;
}

// Obtener mascotas disponibles
$mascotas = Mascota::obtenerTodas($usuarioId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dueño - Pet Manager</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <div class="contenedor-principal">

        <!-- Encabezado con menú y usuario --> 
        <header class="encabezado">
            <div class="logo-titulo">
                <span class="material-symbols-outlined icono-principal">pets</span>
                <h1>Pet Manager</h1>
            </div>
            <nav class="navegacion">
                <a href="../index.php" class="enlace-nav">Inicio</a>
                <a href="../listas/listar_mascotas.php" class="enlace-nav">Mascotas</a>
                <a href="../listas/listar_duenos.php" class="enlace-nav activo">Dueños</a>
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

        <main class="contenido-formulario">
            <div class="encabezado-pagina">
                <h2>Editar Dueño</h2>
                <p>Modifica los datos del dueño</p>
            </div>

            <!-- Formulario para editar dueño -->
            <form action="../procesar/procesar_editar_dueno.php" method="POST" class="formulario-principal" id="formDueno">
                <input type="hidden" name="dueno_id" value="<?php echo $dueno->getId(); ?>">
                
                <!-- Campo de nombre -->
                <div class="campos-grid">
                    <div class="campo-formulario">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($dueno->getNombre()); ?>" 
                               placeholder="Ingresa el nombre" required>
                        <div class="error-campo" id="errorNombre"></div>
                    </div>

                    <!-- Campo de teléfono -->
                    <div class="campo-formulario">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" 
                               value="<?php echo htmlspecialchars($dueno->getTelefono()); ?>" 
                               placeholder="Ingresa el número de teléfono" required>
                        <div class="error-campo" id="errorTelefono"></div>
                    </div>
                </div>

                <!-- Campo de email -->
                <div class="campo-formulario">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($dueno->getEmail() ?? ''); ?>" 
                           placeholder="Ingresa el correo electrónico">
                </div>

                <!-- Campo de dirección -->
                <div class="campo-formulario">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" 
                           value="<?php echo htmlspecialchars($dueno->getDireccion() ?? ''); ?>" 
                           placeholder="Ingresa la dirección">
                </div>
                
                <!-- Botones para cancelar o guardar -->
                <div class="botones-formulario">
                    <a href="../listas/listar_duenos.php" class="boton-secundario">Cancelar</a>
                    <button type="submit" class="boton-principal">Guardar Cambios</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>