<?php

/**
 * --------------------------------------------------------------
 * Archivo: procesarReserva.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Maneja la lógica para registrar una reserva de ride por parte
 * de un pasajero. Valida la sesión, el tipo de usuario, la
 * disponibilidad de espacios y registra la reserva en la base
 * de datos, mostrando mensajes según el resultado.
 * --------------------------------------------------------------
 */

session_start();
include_once("../datos/reservas.php"); // insertarReserva()
include_once("../datos/rides.php");    // obtenerEspaciosDisponibles()


function redirigirConMensaje($mensaje, $tipo = 'info', $destino = 
                            '../interfaz/buscarRide.php') {
    $_SESSION['mensaje_esperado'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}


function validarPeticion() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_ride'])) {
        redirigirConMensaje('Solicitud inválida.', 'error');
    }
}


function validarUsuario() {
    $usuario = $_SESSION['usuario'] ?? null;
    if ($usuario['rol'] !== 'Pasajero') {
        redirigirConMensaje('⚠️ Debes iniciar sesión como pasajero para reservar un ride.', 'error');
    }
    return $usuario;
}


function validarEspaciosDisponibles($idRide) {
    $espacios = obtenerEspaciosDisponibles($idRide);
    if ($espacios < 1) {
        redirigirConMensaje('No hay espacios disponibles para este ride.', 'error');
    }
}

function crearReserva($idRide, $idPasajero) {
    validarEspaciosDisponibles($idRide);

    $exito = insertarReserva($idRide, $idPasajero);

    if ($exito) {
        redirigirConMensaje('✅ Reserva registrada correctamente.', 'success');
    } else {
        redirigirConMensaje('Error al registrar la reserva. Intente nuevamente.', 'error');
    }
}


function procesarReserva() {
    validarPeticion();
    $usuario = validarUsuario();
    $idRide = $_POST['id_ride'];
    $idPasajero = $usuario['id_usuario'] ?? null;
    crearReserva($idRide, $idPasajero);
}

// Ejecutar el proceso
procesarReserva();
