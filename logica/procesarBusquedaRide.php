<?php
session_start();
include_once("../datos/rides.php");

/**
 * Guarda un mensaje en sesión y redirige.
 */
function mostrarMensajeYRedirigir($mensaje, $tipo = 'info', $destino = '../interfaz/buscarRide.php') {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: $destino");
    exit;
}

/**
 * Valida el método de acceso (solo POST permitido).
 */
function validarMetodoPOST() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        mostrarMensajeYRedirigir("Acceso no permitido.", "error");
    }
}

/**
 * Obtiene y limpia los filtros enviados por formulario.
 */
function obtenerFiltros() {
    $salida = trim($_POST['salida'] ?? '');
    $llegada = trim($_POST['llegada'] ?? '');
    $_SESSION['filtros'] = ['salida' => $salida, 'llegada' => $llegada];
    return [$salida, $llegada];
}

/**
 * Ejecuta la búsqueda de rides y guarda resultados en sesión.
 */
function ejecutarBusqueda($salida, $llegada) {
    if ($salida === '' && $llegada === '') {
        mostrarMensajeYRedirigir("Debe ingresar al menos un filtro.", "error");
    }

    $rides = buscarRides($salida, $llegada);

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
?>
