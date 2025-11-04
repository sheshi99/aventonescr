
<?php

// Funciones auxiliares para gestionVehiculo, gestionRides, formularioRide y mis reservas

include_once("../datos/reservas.php");
include_once("../datos/vehiculos.php");
include_once("../datos/rides.php");
include_once("../datos/usuarios.php");

function obtenerMisReservas($idUsuario, $rol) {
    return obtenerReservasPorUsuario($idUsuario, $rol);
}

function obtenerMisVehiculos($idChofer) {
    return obtenerVehiculosPorChofer($idChofer);
}

function obtenerMisRides($idChofer) {
    return obtenerRidesPorChofer($idChofer);
}

function verificarRideTieneReservas($idRide) {
    return rideTieneReservasRealizadas($idRide);
}

function obtenerRidesFiltrados($fecha = '', $salida = '', $llegada = '', $orden = 'ASC') {
    return buscarRides($fecha, $salida, $llegada, 'dia', $orden);
}

function obtenerToken($token){
    activarUsuarioPorToken($token);
}

?>