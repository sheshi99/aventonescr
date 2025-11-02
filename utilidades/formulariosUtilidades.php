<?php

/**
 * --------------------------------------------------------------
 * Archivo: formulariosUtilidades.php
 * Autores: Seidy Alanis y Walbyn González
 * Fecha: 01/11/2025
 * 
 * Descripción:
 * Contiene funciones de utilidad para preparar y manejar formularios
 * de usuarios, vehículos y rides, incluyendo carga de datos existentes,
 * recuperación de datos en caso de errores, determinación de acción
 * (insertar/actualizar) y obtención de valores seguros para los campos.
 * 
 * Estas funciones facilitan la reutilización del código y aseguran
 * consistencia y seguridad en la presentación de los formularios.
 * --------------------------------------------------------------
 */

include_once("../datos/usuarios.php");
include_once("../datos/rides.php");
include_once("../datos/vehiculos.php");


// ==================== USUARIO ====================



function prepararFormularioUsuario() {
    // Recuperar datos guardados tras un error
    $datosGuardados = $_SESSION['datos_formulario'] ?? [];
    $mensaje = $_SESSION['mensaje'] ?? null;

    // Recuperar ID enviado por POST o de los datos guardados
    $id_usuario = $_POST['id_usuario'] ?? $datosGuardados['id_usuario'] ?? null;

    // Limpiar sesión para evitar mostrar datos viejos
    unset($_SESSION['mensaje'], $_SESSION['datos_formulario'], $_SESSION['accion_formulario']);

    // Determinar si es actualización o inserción
    if (!empty($datosGuardados)) {
        // Usar datos guardados tras un error
        $usuario = $datosGuardados;
        $accion = $_SESSION['accion_formulario'] ?? ($id_usuario ? 'actualizar' : 'insertar');
    } elseif ($id_usuario) {
        // Traer datos desde la base de datos
        $usuarioDB = obtenerUsuarioPorId($id_usuario) ?? [];
        $usuario = [
            'id_usuario'           => $usuarioDB['id_usuario'] ?? '',
            'nombre'               => $usuarioDB['nombre'] ?? '',
            'apellido'             => $usuarioDB['apellido'] ?? '',
            'correo'               => $usuarioDB['correo'] ?? '',
            'telefono'             => $usuarioDB['telefono'] ?? '',
            'rol'                  => $usuarioDB['rol'] ?? '',
            'contrasena'           => '', // Nunca mostrar la contraseña real
            'contrasena2'          => '',
            'cedula'               => $usuarioDB['cedula'] ?? '',
            'fecha_nacimiento'     => $usuarioDB['fecha_nacimiento'] ?? '',
            'fotografia_existente' => $usuarioDB['fotografia'] ?? ''
        ];
        $accion = 'actualizar';
    } else {
        // Nuevo registro
        $usuario = [
            'nombre'               => '',
            'apellido'             => '',
            'correo'               => '',
            'telefono'             => '',
            'rol'                  => 'Administrador',
            'contrasena'           => '',
            'contrasena2'          => '',
            'cedula'               => '',
            'fecha_nacimiento'     => '',
            'fotografia_existente' => ''
        ];
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


    $id_vehiculo = $_POST['id_vehiculo'] ?? $datosGuardados['id_vehiculo'] ?? null;

    if ($id_vehiculo) {
    
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




