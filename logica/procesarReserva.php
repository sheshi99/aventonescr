<?php
session_start();
include_once("../datos/reservas.php");

/**
 * Muestra un mensaje en la sesión y redirige a otra página
 */
function mostrarMensajeYRedirigir($mensaje, $tipo = 'info', $destino = '../interfaz/buscarRides.php') {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

/**
 * Valida que el usuario esté logueado y que sea pasajero.
 * Si no cumple, redirige con un mensaje de error.
 */
function validarUsuarioPasajero() {
    if (!isset($_SESSION['usuario'])) {
        mostrarMensajeYRedirigir("⚠️ Debe iniciar sesión como pasajero para reservar.", "error");
    }

    if ($_SESSION['usuario']['rol'] !== 'Pasajero') {
        mostrarMensajeYRedirigir("⚠️ Solo los usuarios pasajeros pueden realizar reservas.", "error");
    }

    return $_SESSION['usuario']['id_usuario'];
}

/**
 * Ejecuta la acción de reserva
 */
function procesarReserva() {
    $accion = $_POST['accion'] ?? '';
    $id_ride = $_POST['id_ride'] ?? null;

    if ($accion !== 'reservar' || !$id_ride) {
        mostrarMensajeYRedirigir("Acción no válida.", "error");
    }

    // Validar usuario y obtener su ID
    $id_pasajero = validarUsuarioPasajero();

    // Insertar la reserva
    $ok = insertarReserva($id_ride, $id_pasajero);

    if ($ok) {
        mostrarMensajeYRedirigir("✅ Reserva creada correctamente.", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al crear la reserva.", "error");
    }
}

// --- Punto de entrada principal ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarReserva();
} else {
    mostrarMensajeYRedirigir("Acceso no permitido.", "error");
}
?>
