<?php

/**
 * --------------------------------------------------------------
 * Archivo: procesarIndexRide.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Controla las acciones principales del index (página principal).
 * Gestiona tanto las reservas de rides por parte de los pasajeros,
 * como el filtrado y ordenamiento de rides disponibles.
 * Incluye validaciones de sesión, rol, disponibilidad y método HTTP.
 * --------------------------------------------------------------
 */

session_start();
include_once("../datos/rides.php");
include_once("../datos/reservas.php"); 

// ===== Funciones comunes =====
function redirigirConMensaje($texto, $tipo = 'error') {
    $_SESSION['mensaje_reserva'] = ['texto' => $texto, 'tipo' => $tipo];
    header("Location: ../index.php");
    exit;
}

function validarMetodo() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigirConMensaje("Acceso no permitido.");
    }
}

function validarUsuarioPasajero() {
    $usuario = $_SESSION['usuario'] ?? null;
    if (!$usuario || $usuario['rol'] !== 'Pasajero') {
        redirigirConMensaje("Debes iniciar sesión como pasajero para reservar un ride.");
    }
    return $usuario;
}

function obtenerIdRide() {
    $id_ride = $_POST['id_ride'] ?? null;
    if (!$id_ride) {
        redirigirConMensaje("Ride no válido.");
    }
    return $id_ride;
}

function validarEspaciosDisponibles($id_ride) {
    $espacios = obtenerEspaciosDisponibles($id_ride);
    if ($espacios <= 0) {
        redirigirConMensaje("Este ride ya no tiene espacios disponibles.");
    }
    return $espacios;
}

function obtenerCampos() {
    $fecha = trim($_POST['fecha'] ?? '');
    $salida = trim($_POST['salida'] ?? '');
    $llegada = trim($_POST['llegada'] ?? '');
    $direccion = ($_POST['orden_direccion'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
    return [$fecha, $salida, $llegada, $direccion];
}

function validarCamposLlenos($fecha, $salida, $llegada) {
    if ($fecha === '' && $salida === '' && $llegada === '') {
        redirigirConMensaje("Debes llenar al menos un campo para ordenar o filtrar.");
    }
}

function buscarRidesFiltrados($fecha, $salida, $llegada, $direccion) {
    return buscarRides($fecha, $salida, $llegada, $direccion);
}

// ===== Funciones separadas =====

// 1️⃣ Función para procesar reservas
function procesarReservaRide() {
    $usuario = validarUsuarioPasajero();
    $id_ride = obtenerIdRide();
    validarEspaciosDisponibles($id_ride);

    $exito = insertarReserva($id_ride, $usuario['id_usuario']);
    if ($exito) {
        $_SESSION['mensaje_reserva'] = [
            'texto' => '✅ Reserva registrada.',
            'tipo'  => 'success'
        ];
    } else {
        $_SESSION['mensaje_reserva'] = [
            'texto' => 'Error al registrar la reserva. Intente de nuevo.',
            'tipo'  => 'error'
        ];
    }

    header("Location: ../index.php");
    exit;
}

// 2️⃣ Función para procesar filtrado/ordenamiento
function procesarFiltradoRide() {
    // Campos de orden enviados desde el combo
    $campo = $_POST['campo_orden'] ?? 'dia';
    $direccion = $_POST['orden_direccion'] ?? 'ASC';

    // Guardar filtros en sesión
    $_SESSION['filtros_orden'] = [
        'campo_orden' => $campo,
        'direccion' => $direccion
    ];

    // Obtener todos los rides futuros
    $rides = consultarRides(); // todos los rides
    $rides = filtrarEspaciosDisponibles($rides); 
    $rides = ordenamientoRides($rides, $campo, $direccion);

    $_SESSION['rides_filtrados'] = $rides;

    if (empty($rides)) {
        $_SESSION['mensaje_reserva'] = [
            'texto' => 'No hay rides disponibles.',
            'tipo' => 'error'
        ];
    }

    header("Location: ../index.php");
    exit;
}


// ===== Función controladora =====
function procesarIndexRide() {
    validarMetodo();

    if (isset($_POST['id_ride'])) {
        // Si viene el ID del ride => procesar reserva
        procesarReservaRide();
    } else {
        // Si vienen campos de filtrado => procesar filtrado
        procesarFiltradoRide();
    }
}

// ===== Ejecutar =====
procesarIndexRide();

?>
