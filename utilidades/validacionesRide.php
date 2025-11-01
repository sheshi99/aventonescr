<?php
include_once("../datos/vehiculos.php");
include_once("../datos/rides.php");
include_once("mensajes.php"); 



// ==================== VALIDACIONES ====================

function validarCamposObligatorios($datos, $id_ride = null) {
    foreach ($datos as $campo => $valor) {
        if (empty($valor)) {
            mostrarMensajeYRedirigir(
                "El campo $campo es obligatorio",
                "../interfaz/formularioRide.php",
                "error", $datos, $campo, $id_ride,
                $id_ride ? 'actualizar' : 'insertar'
            );
        }
    }
}

function validarCostoEspacios($datos, $id_ride = null) {
    if (!is_numeric($datos['costo']) || $datos['costo'] <= 0) {
        mostrarMensajeYRedirigir(
            "❌ Costo inválido",
            "../interfaz/formularioRide.php",
            "error", $datos, 'costo', $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
    if (!is_numeric($datos['espacios']) || $datos['espacios'] < 1) {
        mostrarMensajeYRedirigir(
            "❌ Espacios inválidos",
            "../interfaz/formularioRide.php",
            "error", $datos, 'espacios', $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
}

function validarCapacidadesVehiculo($datos, $id_ride = null) {
    $vehiculo = obtenerVehiculoPorId($datos['id_vehiculo']);
    if (!$vehiculo) {
        mostrarMensajeYRedirigir(
            "❌ Vehículo no encontrado",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'id_vehiculo',
            $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
    if ($datos['espacios'] > $vehiculo['capacidad_asientos']) {
        mostrarMensajeYRedirigir(
            "⚠️ El vehículo solo tiene {$vehiculo['capacidad_asientos']} asientos disponibles.",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'espacios',
            $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
}

function vehiculoOcupado($id_vehiculo, $dia, $hora, $id_ride_actual = null) {  // CORREGIDO
    $horaNorm = date('H:i', strtotime($hora));
    $diaNorm = date('Y-m-d', strtotime($dia));

    $ridesVehiculo = obtenerRidesPorVehiculo($id_vehiculo);

    foreach ($ridesVehiculo as $ride) {
        if ($id_ride_actual && $ride['id_ride'] == $id_ride_actual) continue;

        $rideDia = date('Y-m-d', strtotime($ride['dia'])); // CORREGIDO
        $rideHora = date('H:i', strtotime($ride['hora']));

        if ($rideDia === $diaNorm && $rideHora === $horaNorm) {
            return true;
        }
    }
    return false;
}

function validarVehiculoDisponible($datos, $id_ride_actual = null) {
    $id_vehiculo = $datos['id_vehiculo'] ?? null;
    $dia = $datos['dia'] ?? '';    // CORREGIDO
    $hora = $datos['hora'] ?? '';

    if (!$id_vehiculo) return;

    if (vehiculoOcupado($id_vehiculo, $dia, $hora, $id_ride_actual)) {
        mostrarMensajeYRedirigir(
            "❌ El vehículo ya tiene un ride programado en esa fecha y hora",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'hora',
            $id_ride_actual,
            $id_ride_actual ? 'actualizar' : 'insertar'
        );
    }
}

function validarSalidaLlegada($datos, $id_ride = null) {
    if ($datos['salida'] === $datos['llegada']) {
        mostrarMensajeYRedirigir(
            "❌ El lugar de salida no puede ser igual al lugar de llegada",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'llegada',
            $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
}


function validarDiaHora($datos, $id_ride = null) {
    $dia = $datos['dia'] ?? '';
    $hora = $datos['hora'] ?? '';

    if (!$dia || !$hora) return; // Si no hay datos, no hacemos nada

    // Obtenemos fecha y hora actual
    $fechaActual = date('Y-m-d');
    $horaActual  = date('H:i');

    if ($dia < $fechaActual) {
        mostrarMensajeYRedirigir(
            "❌ El día del ride no puede ser anterior al día de hoy",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'dia',
            $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }

    if ($dia === $fechaActual && $hora < $horaActual) {
        mostrarMensajeYRedirigir(
            "❌ La hora del ride no puede ser anterior a la hora actual",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            'hora',
            $id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
}


function validarRide($datos, $id_ride_actual = null) {
    validarCamposObligatorios($datos, $id_ride_actual);
    validarCostoEspacios($datos, $id_ride_actual);
    validarVehiculoDisponible($datos, $id_ride_actual);
    validarSalidaLlegada($datos, $id_ride_actual);
    validarCapacidadesVehiculo($datos, $id_ride_actual);
    validarDiaHora($datos, $id_ride_actual); 
}