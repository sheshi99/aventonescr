<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info') {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

// Eliminar vehículo
function procesarEliminar($id_vehiculo) {
    if (eliminarVehiculo($id_vehiculo)) {
        mostrarMensajeYRedirigir("✅ Vehículo eliminado correctamente", "../interfaz/gestionVehiculos.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al eliminar el vehículo", "../interfaz/gestionVehiculos.php", "error");
    }
}

// Procesar acción según POST
function procesarAccion() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['accion'])) die("Acceso no permitido");

    switch ($_POST['accion']) {
        case 'eliminar':
            if (!empty($_POST['id_vehiculo'])) procesarEliminar($_POST['id_vehiculo']);
            break;
        default:
            mostrarMensajeYRedirigir("❌ Acción no válida", "../interfaz/gestionVehiculos.php", "error");
    }
}

procesarAccion();
