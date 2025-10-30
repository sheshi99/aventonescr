
<?php
include_once("../datos/rides.php");


function prepararFormularioRide() {
    $datosFormulario = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

    // Determinar id_ride: primero en datos de sesión, si no, POST
    $id_ride = $datosFormulario['id_ride'] ?? $_POST['id_ride'] ?? null;

    if ($id_ride) {
        // Si hay id_ride, es edición: tomar datos de sesión si existen, si no, de la DB
        $ride = !empty($datosFormulario) ? $datosFormulario : (obtenerRidePorId($id_ride) ?? []);
        $accion = 'actualizar';
    } else {
        // Nuevo ride
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
    }

    return [
        'ride' => $ride,
        'accion' => $accion,
        'datosFormulario' => $datosFormulario,
        'mensaje' => $mensaje
    ];
}

function valor($campo, $datosFormulario, $ride) {
    return htmlspecialchars($datosFormulario[$campo] ?? $ride[$campo] ?? '');
}


function obtenerDias() {
    return ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
}

?>