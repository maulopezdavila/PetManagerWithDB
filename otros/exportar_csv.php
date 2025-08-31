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

// Obtener todas las mascotas con sus relaciones
$mascotas = Mascota::obtenerTodas($usuarioId);
foreach ($mascotas as $mascota) {
    $mascota->cargarDuenos();
    $mascota->cargarVisitas();
}

// Preparar descarga CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="mascotas_' . date('Y-m-d') . '.csv"');

// Crear archivo CSV
$salida = fopen('php://output', 'w');
fputs($salida, "\xEF\xBB\xBF"); // BOM para UTF-8

// Escribir encabezados
fputcsv($salida, [
    'Nombre',
    'Especie', 
    'Raza',
    'Descripción',
    'Fecha Nacimiento',
    'Dueños',
    'Teléfonos',
    'Última Visita',
    'Total Visitas'
], ',', '"');

// Escribir datos
foreach ($mascotas as $mascota) {
    $nombresDuenos = array_map(function($d) {
        return $d->getNombre();
    }, $mascota->getDuenos());
    
    $telefonosDuenos = array_map(function($d) {
        return $d->getTelefono();
    }, $mascota->getDuenos());
    
    $visitas = $mascota->getVisitas();
    $ultimaVisita = 'Ninguna';
    $totalVisitas = count($visitas);
    
    if (!empty($visitas)) {
        $ultimaVisita = $visitas[0]->getFecha(); // Ya están ordenadas por fecha DESC
    }
    
    fputcsv($salida, [
        $mascota->getNombre(),
        $mascota->getEspecie(),
        $mascota->getRaza() ?? 'No especificada',
        $mascota->getColor() ?? 'No especificada',
        $mascota->getFechaNacimiento() ?? 'No especificada',
        implode(', ', $nombresDuenos),
        implode(', ', $telefonosDuenos),
        $ultimaVisita,
        $totalVisitas
    ], ',', '"');
}

fclose($salida);
exit;
?>