<?php
session_start();
include_once("../datos/reservas.php"); // insertarReserva()
include_once("../datos/rides.php");    // obtenerEspaciosDisponibles()

/**
 * Redirige con mensaje a una página determinada.
 */
function redirigirConMensaje($mensaje, $tipo = 'info', $destino = '../interfaz/buscarRide.php') {
    $_SESSION['mensaje_esperado'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

/**
 * Valida que la petición sea POST y que se reciba id_ride.
 */
function validarPeticion() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_ride'])) {
        redirigirConMensaje('Solicitud inválida.', 'error');
    }
}

/**
 * Valida que el usuario esté logueado y sea pasajero.
 * Retorna el usuario.
 */
function validarUsuario() {
    $usuario = $_SESSION['usuario'] ?? null;
    if ($usuario['rol'] !== 'Pasajero') {
        redirigirConMensaje('⚠️ Debes iniciar sesión como pasajero para reservar un ride.', 'error');
    }
    return $usuario;
}

/**
 * Valida que haya espacios disponibles para el ride.
 */
function validarEspaciosDisponibles($idRide) {
    $espacios = obtenerEspaciosDisponibles($idRide);
    if ($espacios < 1) {
        redirigirConMensaje('No hay espacios disponibles para este ride.', 'error');
    }
}

/**
 * Crea la reserva.
 */
function crearReserva($idRide, $idPasajero) {
    validarEspaciosDisponibles($idRide);

    $exito = insertarReserva($idRide, $idPasajero);

    if ($exito) {
        redirigirConMensaje('✅ Reserva registrada correctamente.', 'success');
    } else {
        redirigirConMensaje('Error al registrar la reserva. Intente nuevamente.', 'error');
    }
}

/**
 * Función principal que ejecuta el proceso.
 */
function procesarReserva() {
    validarPeticion();
    $usuario = validarUsuario();
    $idRide = $_POST['id_ride'];
    $idPasajero = $usuario['id_usuario'] ?? null;
    crearReserva($idRide, $idPasajero);
}

// Ejecutar el proceso
procesarReserva();
