
<?php

// Funciones auxiliares para las interfaces
include_once("../datos/reservas.php");
include_once("../datos/vehiculos.php");
include_once("../datos/rides.php");
include_once("../datos/usuarios.php");

//misReservas
function obtenerMisReservas($idUsuario, $rol) {
    return obtenerReservasPorUsuario($idUsuario, $rol);
}

//gestionVehiculo
function obtenerMisVehiculos($idChofer) {
    return obtenerVehiculosPorChofer($idChofer);
}

//gestionRides
function obtenerMisRides($idChofer) {
    return obtenerRidesPorChofer($idChofer);
}

//gestionRides
function verificarRideTieneReservas($idRide) {
    return rideTieneReservasAceptadas($idRide);
}


//activarCuenta
function obtenerToken($token){
    activarUsuarioPorToken($token);
}

?>