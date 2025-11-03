<?php

/*
 * Archivo: procesarBuscarRide.php
 * Autores: Seidy Alanis y Walbyn González
 *
 * Descripción:
 * Procesa la búsqueda de rides según filtros de salida y llegada,
 * guarda los resultados en sesión y redirige a la página de búsqueda.
 */

session_start();
include_once("../datos/rides.php");


function mostrarMensajeYRedirigir($mensaje, $tipo = 'info', $destino = '../interfaz/buscarRide.php') {
    $_SESSION['mensaje_esperado'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}


function validarMetodoPOST() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        mostrarMensajeYRedirigir("Acceso no permitido.", "error");
    }
}


function obtenerFiltros() {
    $salida = trim($_POST['salida'] ?? '');
    $llegada = trim($_POST['llegada'] ?? '');
    $_SESSION['filtros'] = ['salida' => $salida, 'llegada' => $llegada];
    return [$salida, $llegada];
}


function ejecutarBusqueda($salida, $llegada) {
    if ($salida === '' && $llegada === '') {
        mostrarMensajeYRedirigir("Debe ingresar al menos un filtro.", "error");
    }

    // --- Obtener rides filtrados ---
    $fecha = '';
    $rides = buscarRides($fecha, $salida, $llegada);

    // --- Agregar campo espacios_disponibles para cada ride ---
    foreach ($rides as &$r) {
        $r['espacios_disponibles'] = obtenerEspaciosDisponibles($r['id_ride']);
    }
    unset($r); 

    if (empty($rides)) {
        $_SESSION['mensaje'] = ['texto' => 'No se encontraron rides.', 'tipo' => 'info'];
    } else {
        $_SESSION['mensaje'] = ['texto' => 'Rides disponibles.', 'tipo' => 'success'];
    }

    $_SESSION['rides'] = $rides;
    header("Location: ../interfaz/buscarRide.php");
    exit;
}

// --- Punto de entrada principal ---
validarMetodoPOST();
list($salida, $llegada) = obtenerFiltros();
ejecutarBusqueda($salida, $llegada);

