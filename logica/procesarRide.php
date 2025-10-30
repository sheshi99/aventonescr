<?php
session_start();
include_once("../datos/rides.php");
include_once("../datos/vehiculos.php");
include_once("../utilidades/mensajes.php");

$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_chofer) {
    header("Location: ../interfaz/login.php");
    exit;
}

function obtenerDatosRide() {
    return [
        'id_vehiculo' => trim($_POST['id_vehiculo'] ?? ''),
        'nombre'      => trim($_POST['nombre'] ?? ''),
        'salida'      => trim($_POST['salida'] ?? ''),
        'llegada'     => trim($_POST['llegada'] ?? ''),
        'dia'         => trim($_POST['dia'] ?? ''),
        'hora'        => trim($_POST['hora'] ?? ''),
        'costo'       => trim($_POST['costo'] ?? ''),
        'espacios'    => trim($_POST['espacios'] ?? '')
    ];
}

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
            "error",$datos,'costo',$id_ride,
            $id_ride ? 'actualizar' : 'insertar'
        );
    }
    if (!is_numeric($datos['espacios']) || $datos['espacios'] < 1) {
        mostrarMensajeYRedirigir(
            "❌ Espacios inválidos",
            "../interfaz/formularioRide.php",
            "error",$datos,'espacios',$id_ride,
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

function vehiculoOcupado($id_vehiculo, $dia, $hora, $id_ride_actual = null) {
    foreach (obtenerRidesPorVehiculo($id_vehiculo) as $ride) {
        if ($id_ride_actual && $ride['id_ride'] == $id_ride_actual) continue;
        if ($ride['dia'] === $dia && $ride['hora'] === $hora) return true;
    }
    return false;
}

function validarVehiculoDisponible($datos, $id_ride_actual = null) {
    if (vehiculoOcupado($datos['id_vehiculo'], $datos['dia'], $datos['hora'], $id_ride_actual)) {
        mostrarMensajeYRedirigir(
            "❌ El vehículo ya tiene un ride programado en ese día y hora",
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

function validarRide($datos, $id_ride_actual = null) {
    validarCamposObligatorios($datos, $id_ride_actual);
    validarCostoEspacios($datos, $id_ride_actual);
    validarVehiculoDisponible($datos, $id_ride_actual);
    validarSalidaLlegada($datos, $id_ride_actual);
    validarCapacidadesVehiculo($datos, $id_ride_actual);
}

// ==================== ACCIONES ====================

function eliminarRideAction($id_ride) {
    if (eliminarRide($id_ride)) {
        mostrarMensajeYRedirigir("✅ Ride eliminado", "../interfaz/gestionRides.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al eliminar", "../interfaz/gestionRides.php", "error");
    }
}

function actualizarRideAction($id_ride) {
    $datos = obtenerDatosRide();
    validarRide($datos, $id_ride);

    $ok = actualizarRide(
        $id_ride,
        $datos['id_vehiculo'],
        $datos['nombre'],
        $datos['salida'],
        $datos['llegada'],
        $datos['dia'],
        $datos['hora'],
        $datos['costo'],
        $datos['espacios']
    );

    if ($ok) {
        mostrarMensajeYRedirigir("✅ Ride actualizado", "../interfaz/gestionRides.php", "success");
    } else {
        mostrarMensajeYRedirigir(
            "❌ Error al actualizar",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            null,
            $id_ride,
            'actualizar'
        );
    }
}

function insertarRideAction($id_chofer) {
    $datos = obtenerDatosRide();
    validarRide($datos);

    $ok = insertarRide(
        $id_chofer,
        $datos['id_vehiculo'],
        $datos['nombre'],
        $datos['salida'],
        $datos['llegada'],
        $datos['dia'],
        $datos['hora'],
        $datos['costo'],
        $datos['espacios']
    );

    if ($ok) {
        mostrarMensajeYRedirigir("✅ Ride registrado", "../interfaz/gestionRides.php", "success");
    } else {
        mostrarMensajeYRedirigir(
            "❌ Error al registrar",
            "../interfaz/formularioRide.php",
            "error",
            $datos,
            null,
            null,
            'insertar'
        );
    }
}

// ==================== EJECUTAR ACCIÓN SEGÚN POST ====================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? 'insertar';
    $id_ride = $_POST['id_ride'] ?? null;

    switch ($accion) {
        case 'eliminar':
            if ($id_ride) eliminarRideAction($id_ride);
            break;

        case 'actualizar':
            if ($id_ride) actualizarRideAction($id_ride);
            break;

        case 'insertar':
        default:
            insertarRideAction($id_chofer);
            break;
    }
} else {
    die("Acceso no permitido");
}
?>
