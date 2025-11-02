<?php
session_start();
include_once("../datos/rides.php");

// ===== Funciones =====

// Redirige con mensaje a la interfaz principal
function redirigirConMensaje($texto, $tipo = 'error') {
    $_SESSION['mensaje_reserva'] = ['texto' => $texto, 'tipo' => $tipo];
    header("Location: ../interfaz/index.php");
    exit;
}

// Validar método POST
function validarMetodo() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirigirConMensaje("Acceso no permitido.");
    }
}

// Validar usuario pasajero
function validarUsuarioPasajero() {
    $usuario = $_SESSION['usuario'] ?? null;
    if (!$usuario || $usuario['rol'] !== 'Pasajero') {
        redirigirConMensaje("Debes iniciar sesión como pasajero para reservar un ride.");
    }
    return $usuario;
}

// Obtener ID del ride
function obtenerIdRide() {
    $id_ride = $_POST['id_ride'] ?? null;
    if (!$id_ride) {
        redirigirConMensaje("Ride no válido.");
    }
    return $id_ride;
}

// Validar espacios disponibles
function validarEspaciosDisponibles($id_ride) {
    $espacios = obtenerEspaciosDisponibles($id_ride);
    if ($espacios <= 0) {
        redirigirConMensaje("Este ride ya no tiene espacios disponibles.");
    }
    return $espacios;
}

// Obtener filtros enviados
function obtenerCampos() {
    $fecha = trim($_POST['fecha'] ?? '');
    $salida = trim($_POST['salida'] ?? '');
    $llegada = trim($_POST['llegada'] ?? '');
    $direccion = ($_POST['orden_direccion'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
    return [$fecha, $salida, $llegada, $direccion];
}

// Validar que al menos un campo esté lleno
function validarCamposLlenos($fecha, $salida, $llegada) {
    if ($fecha === '' && $salida === '' && $llegada === '') {
        redirigirConMensaje("Debes llenar al menos un campo para ordenar o filtrar.");
    }
}

// Buscar rides filtrados
function buscarRidesFiltrados($fecha, $salida, $llegada, $direccion) {
    return buscarRides($fecha, $salida, $llegada, $direccion);
}

// ===== PROCESO PRINCIPAL =====
validarMetodo();

// Si se envía para reservar ride
if (isset($_POST['id_ride'])) {
    $usuario = validarUsuarioPasajero();
    $id_ride = obtenerIdRide();
    validarEspaciosDisponibles($id_ride);

    // Guardar ride a reservar en sesión
    $_SESSION['id_ride_a_reservar'] = $id_ride;
    header("Location: ../logica/procesarReserva.php");
    exit;
}

// Si se envía para ordenar/filtrar
list($fecha, $salida, $llegada, $direccion) = obtenerCampos();
validarCamposLlenos($fecha, $salida, $llegada);

$rides = buscarRidesFiltrados($fecha, $salida, $llegada, $direccion);

// Guardar resultados y filtros
$_SESSION['rides_filtrados'] = $rides;
$_SESSION['filtros_orden'] = [
    'fecha' => $fecha,
    'salida' => $salida,
    'llegada' => $llegada,
    'direccion' => $direccion
];

// Mensaje si no hay rides
if (empty($rides)) {
    $_SESSION['mensaje_reserva'] = [
        'texto' => 'No hay rides disponibles con esos filtros.',
        'tipo' => 'error'
    ];
}

header("Location: ../interfaz/index.php");
exit;
?>
