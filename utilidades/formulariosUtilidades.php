<?php
include_once("../datos/rides.php");

function prepararFormularioRide() {
    // Tomar datos guardados del formulario si hubo error
    $datosGuardados = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;

    // Limpiar los datos de sesión para no mostrarlos de nuevo
    unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

    // Revisar si estamos editando (si viene id_ride)
    $id_ride = $_POST['id_ride'] ?? $datosGuardados['id_ride'] ?? null;

    if ($id_ride) {
        // Es edición: usar datos guardados o buscar en la base de datos
        if (!empty($datosGuardados)) {
            $ride = $datosGuardados;
        } else {
            $ride = obtenerRidePorId($id_ride) ?? [];
        }
        $accion = 'actualizar';
    } else {
        // Es un ride nuevo: dejamos todos los campos vacíos
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

    // Devolver todo listo para el formulario
    return [
        'ride' => $ride,
        'accion' => $accion,
        'datosFormulario' => $datosGuardados,
        'mensaje' => $mensaje
    ];
}

function valor($campo, $datosFormulario, $ride) {
    return htmlspecialchars($datosFormulario[$campo] ?? $ride[$campo] ?? '');
}

?>