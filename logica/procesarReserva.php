<?php
session_start();
include_once("../datos/reservas.php"); // insertarReserva()
include_once("../datos/rides.php");    // obtenerEspaciosDisponibles()

/**
 * Redirige a la página de búsqueda con un mensaje en sesión.
 */
function redirigirConMensaje($mensaje, $tipo = 'info', $destino = '../interfaz/buscarRide.php') {
    $_SESSION['mensaje_esperado'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

/**
 * Valida que la petición sea POST y que se reciba un id_ride.
 */
function validarPeticion() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_ride'])) {
        redirigirConMensaje('Solicitud inválida.', 'error');
    }
}

/**
 * Valida que el usuario esté logueado y sea pasajero.
 */
function validarUsuario() {
    $usuario = $_SESSION['usuario'] ?? null;
    if (!$usuario || $usuario['rol'] !== 'Pasajero') {
        redirigirConMensaje('⚠️ Debes iniciar sesión como pasajero para reservar.', 'error');
    }
    return $usuario;
}

/**
 * Verifica si hay espacios disponibles para el ride.
 */
function validarEspaciosDisponibles($idRide) {
    $espacios = obtenerEspaciosDisponibles($idRide);
    if ($espacios < 1) {
        redirigirConMensaje('No hay espacios disponibles para este ride.', 'error');
    }
}

/**
 * Crea la reserva si todo está correcto.
 */
function crearReserva($idRide, $idPasajero) {
    if (!$idPasajero) {
        redirigirConMensaje('Usuario inválido.', 'error');
    }

    // Validar espacios antes de insertar
    validarEspaciosDisponibles($idRide);

    $exito = insertarReserva($idRide, $idPasajero);

    if ($exito) {
        redirigirConMensaje('✅ Reserva registrada.', 'success');
    } else {
        redirigirConMensaje('Error al registrar la reserva. Intente de nuevo.', 'error');
    }
}

// --- Lógica principal ---
validarPeticion();
$usuario = validarUsuario();
$idRide = $_POST['id_ride'];
$idPasajero = $usuario['id_usuario'] ?? null;
crearReserva($idRide, $idPasajero);
