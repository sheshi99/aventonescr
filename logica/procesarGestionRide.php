<?php
session_start();
include_once("../datos/rides.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info') {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

// Eliminar ride
function procesarEliminar($id_ride) {
    if (eliminarRide($id_ride)) { // Debes tener esta función en datos/rides.php
        mostrarMensajeYRedirigir("✅ Ride eliminado", "../interfaz/gestionRides.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al eliminar", "../interfaz/gestionRides.php", "error");
    }
}

// Procesar acción según POST
function procesarAccion() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['accion'])) {
        die("Acceso no permitido");
    }

    $accion = $_POST['accion'];

    switch($accion) {
        case 'eliminar':
            if (!empty($_POST['id_ride'])) procesarEliminar($_POST['id_ride']);
            break;

        default:
            mostrarMensajeYRedirigir("❌ Acción no válida", "../interfaz/gestionRides.php", "error");
    }
}

procesarAccion();
