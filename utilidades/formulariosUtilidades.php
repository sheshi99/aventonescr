<?php
include_once("../datos/usuarios.php");
include_once("../datos/rides.php");
include_once("../datos/vehiculos.php");


// ==================== USUARIO ====================

function prepararFormularioUsuario() {
    $datosGuardados = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;

    unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

    $id_usuario = $_POST['id_usuario'] ?? $datosGuardados['id_usuario'] ?? null;

    if ($id_usuario) {
        // Edición
        if (!empty($datosGuardados)) {
            $usuario = $datosGuardados;
        } else {
            $usuario = obtenerUsuarioPorId($id_usuario) ?? [];
        }
        $accion = 'actualizar';
    } else {
        // Nuevo usuario
        $usuario = [
            'id_usuario'           => '',
            'nombre'               => '',
            'apellido'             => '',
            'correo'               => '',
            'telefono'             => '',
            'rol'                  => '',
            'contrasena'           => '',
            'contrasena2'          => '',
            'cedula'               => '',
            'fecha_nacimiento'     => '',
            'fotografia_existente' => ''
        ];
        // Sobreescribir con datos guardados si hubo error previo
        if (!empty($datosGuardados)) {
            $usuario = array_merge($usuario, $datosGuardados);
        }
        $accion = 'insertar';
    }

    return [
        'usuario'         => $usuario,
        'accion'          => $accion,
        'datosFormulario' => $datosGuardados,
        'mensaje'         => $mensaje
    ];
}


function valorUsuario($campo, $datosFormulario, $usuario) {
    return htmlspecialchars($datosFormulario[$campo] ?? $usuario[$campo] ?? '');
}


// ==================== VEHICULO ====================

function prepararFormularioVehiculo() {
    // Tomar datos guardados en sesión si hubo errores de validación
    $datosGuardados = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

    // Obtener id_vehiculo desde POST o desde datos guardados
    $id_vehiculo = $_POST['id_vehiculo'] ?? $datosGuardados['id_vehiculo'] ?? null;

    if ($id_vehiculo) {
        // Si es edición, cargar datos de la base o usar los guardados
        if (!empty($datosGuardados)) {
            $vehiculo = $datosGuardados;
        } else {
            $vehiculoDB = obtenerVehiculoPorId($id_vehiculo) ?? [];
            $vehiculo = [
                'placa'    => $vehiculoDB['numero_placa'] ?? '',
                'color'    => $vehiculoDB['color'] ?? '',
                'marca'    => $vehiculoDB['marca'] ?? '',
                'modelo'   => $vehiculoDB['modelo'] ?? '',
                'anno'     => $vehiculoDB['anno'] ?? '',
                'asientos' => $vehiculoDB['capacidad_asientos'] ?? '',
                'fotografia_existente' => $vehiculoDB['fotografia'] ?? ''
            ];
        }
        $accion = 'actualizar';
    } else {
        // Si es nuevo vehículo
        $vehiculo = [
            'placa' => '', 'color' => '', 'marca' => '', 'modelo' => '',
            'anno' => '', 'asientos' => '', 'fotografia_existente' => ''
        ];
        $accion = 'insertar';
    }

    return [
        'vehiculo' => $vehiculo,
        'accion' => $accion,
        'datosFormulario' => $datosGuardados,
        'mensaje' => $mensaje
    ];
}


function valorVehiculo($campo, $datosFormulario, $vehiculo) {
    return htmlspecialchars($datosFormulario[$campo] ?? $vehiculo[$campo] ?? '');
}


// ==================== RIDE ====================

function prepararFormularioRide() {

    $datosGuardados = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

    $id_ride = $_POST['id_ride'] ?? $datosGuardados['id_ride'] ?? null;

    if ($id_ride) {
        
        if (!empty($datosGuardados)) {
            $ride = $datosGuardados;
        } else {
            $ride = obtenerRidePorId($id_ride) ?? [];
        }
        $accion = 'actualizar';
    } else {
        
        $ride = [
        'id_ride'     => '', 'id_vehiculo' => '', 'nombre'      => '',
        'salida'      => '', 'llegada'     => '', 'dia'         => '',   
        'hora'        => '', 'costo'       => '', 'espacios'    => ''
    ];
        $accion = 'insertar';
    }

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




