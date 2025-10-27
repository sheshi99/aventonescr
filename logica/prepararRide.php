<?php

include_once("../datos/rides.php");
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

$ride = [
    'id_vehiculo' => '',
    'nombre' => '',
    'salida' => '',
    'llegada' => '',
    'dia' => '',
    'hora' => '',
    'costo' => '',
    'espacios' => ''
];

$accion = 'insertar';
$id_ride = null;

if (!empty($_POST['id_ride'])) {
    $id_ride = $_POST['id_ride'];
    $rideDB = obtenerRidePorId($id_ride);

    if ($rideDB) {
        $ride = [
            'id_ride'     => $id_ride,   
            'id_vehiculo' => $rideDB['id_vehiculo'],
            'nombre' => $rideDB['nombre'],
            'salida' => $rideDB['salida'],
            'llegada' => $rideDB['llegada'],
            'dia' => $rideDB['dia'],
            'hora' => $rideDB['hora'],
            'costo' => $rideDB['costo'],
            'espacios' => $rideDB['espacios']
        ];
        $accion = 'actualizar';
    }
}
