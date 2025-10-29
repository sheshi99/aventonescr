
<?php
include_once("../datos/rides.php");

function prepararRidePorDefecto($id_ride = null) {
    $ride = [
        'id_ride'     => '',
        'id_vehiculo' => '',
        'nombre'      => '',
        'salida'      => '',
        'llegada'     => '',
        'dia'         => '',
        'hora'        => '',
        'costo'       => '',
        'espacios'    => ''
    ];
    $accion = 'insertar';

    if ($id_ride) {
        $rideDB = obtenerRidePorId($id_ride); // función de datos/rides.php
        if ($rideDB) {
            $ride = $rideDB;
            $accion = 'actualizar';
        }
    }

    return ['ride' => $ride, 'accion' => $accion];
}

function valor($campo, $datosFormulario, $ride) {
    return htmlspecialchars($datosFormulario[$campo] ?? $ride[$campo] ?? '');
}

function obtenerDias() {
    return ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
}

?>