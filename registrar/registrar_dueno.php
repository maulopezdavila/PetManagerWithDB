<?php
require_once '../models/dueno.php';
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
    <title>Registrar Dueño - Pet Manager</title>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <div class="contenedor-principal">
        <!-- Header con logo, navegación y info del usuario -->
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
        
        <!-- Main con el formulario para registrar un dueño -->
        <main class="contenido-formulario">
            <div class="encabezado-pagina">
                <h2>Nuevo Dueño</h2>
                <p>Completa el formulario para registrar a un dueño</p>
            </div>

            <form action="../procesar/procesar_dueno.php" method="POST" class="formulario-principal" id="formDueno">

                <!-- Campos principales: nombre y teléfono -->
                <div class="campos-grid">
                    <div class="campo-formulario">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingresa el nombre" required>
                        <div class="error-campo" id="errorNombre"></div>
                    </div>

                    <div class="campo-formulario">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="Ingresa el número de teléfono" required>
                        <div class="error-campo" id="errorTelefono"></div>
                    </div>
                </div>
                
                <!-- Email opcional -->
                <div class="campo-formulario">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Ingresa el correo electrónico">
                </div>

                <!-- Dirección opcional -->
                <div class="campo-formulario">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Ingresa la dirección">
                </div>

                <!-- Asociar mascota existente (opcional) -->
                <div class="campo-formulario">
                    <label for="mascota">Mascota asociada</label>
                    <select id="mascota" name="mascota">
                        <option value="">Selecciona una mascota existente (opcional)</option>
                        <?php foreach ($mascotas as $mascota): ?>
                            <option value="<?php echo $mascota->getId(); ?>">
                                <?php echo htmlspecialchars($mascota->getNombre()) . ' (' . htmlspecialchars($mascota->getEspecie()) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Botones: cancelar o guardar -->
                <div class="botones-formulario">
                    <a href="../index.php" class="boton-secundario">Cancelar</a>
                    <button type="submit" class="boton-principal">Guardar Dueño</button>
                </div>
            </form>
        </main>
    </div>

    <script src="../scripts.js"></script>
</body>
</html>