
/**
 * --------------------------------------------------------------
 * Archivo: procesarRide.php
 * Autores: Seidy Alanis y Walbyn González
 * Fecha: 31/10/2025
 * Descripción:
 * Maneja la lógica para registrar, editar y eliminar rides (viajes),
 * creados por los choferes. Incluye validaciones de vehículo, lugares,
 * día, hora, costo y espacios, mostrando mensajes según el resultado.
 * --------------------------------------------------------------
 */


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
            redirigirMsjRide(
                "El campo $campo es obligatorio", "../interfaz/formularioRide.php",
                "error", $datos, $campo, $id_ride, $id_ride ? 'actualizar' : 'insertar'
            );
        }
    }
}

function validarCostoEspacios($datos, $id_ride = null) {
    if (!is_numeric($datos['costo']) || $datos['costo'] <= 0) {
        redirigirMsjRide(
            "❌ Costo inválido", "../interfaz/formularioRide.php",
            "error", $datos, 'costo', $id_ride, $id_ride ? 'actualizar' : 'insertar'
        );
    }
    if (!is_numeric($datos['espacios']) || $datos['espacios'] < 1) {
        redirigirMsjRide(
            "❌ Espacios inválidos", "../interfaz/formularioRide.php",
            "error", $datos, 'espacios', $id_ride, $id_ride ? 'actualizar' : 'insertar'
        );
    }
}

function validarCapacidadesVehiculo($datos, $id_ride = null) {
    $vehiculo = obtenerVehiculoPorId($datos['id_vehiculo']);
    if (!$vehiculo) {
        redirigirMsjRide(
            "❌ Vehículo no encontrado", "../interfaz/formularioRide.php",
            "error", $datos,'id_vehiculo', $id_ride, $id_ride ? 'actualizar' : 'insertar'
        );
    }
    if ($datos['espacios'] > $vehiculo['capacidad_asientos']) {
        redirigirMsjRide(
            "⚠️ El vehículo solo tiene {$vehiculo['capacidad_asientos']} asientos disponibles.",
            "../interfaz/formularioRide.php","error", $datos,'espacios',
            $id_ride, $id_ride ? 'actualizar' : 'insertar'
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
    $dia = $datos['dia'] ?? '';    
    $hora = $datos['hora'] ?? '';

    if (!$id_vehiculo) return;

    if (vehiculoOcupado($id_vehiculo, $dia, $hora, $id_ride_actual)) {
        redirigirMsjRide(
            "❌ El vehículo ya tiene un ride programado en esa fecha y hora",
            "../interfaz/formularioRide.php", "error", $datos,'hora', $id_ride_actual,
            $id_ride_actual ? 'actualizar' : 'insertar'
        );
    }
}

function validarSalidaLlegada($datos, $id_ride = null) {
    if ($datos['salida'] === $datos['llegada']) {
        redirigirMsjRide(
            "❌ El lugar de salida no puede ser igual al lugar de llegada",
            "../interfaz/formularioRide.php", "error", $datos,'llegada',
            $id_ride, $id_ride ? 'actualizar' : 'insertar'
        );
    }
}


function validarDiaHora($datos, $id_ride = null) {
    $dia = $datos['dia'] ?? '';
    $hora = $datos['hora'] ?? '';

    if (!$dia || !$hora) return; 

    // Obtenemos fecha y hora actual
    $fechaActual = date('Y-m-d');
    $horaActual  = date('H:i');

    if ($dia < $fechaActual) {
        redirigirMsjRide(
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
        redirigirMsjRide(
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

// ==================== ACCIONES ====================

function eliminarRideAction($id_ride) {
    if (eliminarRide($id_ride)) {
        redirigirMsjRide("✅ Ride eliminado", "../interfaz/gestionRides.php", 
                                "success");
    } else {
        redirigirMsjRide("❌ Error al eliminar", "../interfaz/gestionRides.php", 
                                "error");
    }
}

function actualizarRideAction($id_ride) {
    $datos = obtenerDatosRide();
    validarRide($datos, $id_ride);

    $ok = actualizarRide(
        $id_ride, $datos['id_vehiculo'], $datos['nombre'], $datos['salida'], $datos['llegada'],
        $datos['dia'], $datos['hora'], $datos['costo'], $datos['espacios']
    );

    if ($ok) {
        redirigirMsjRide("✅ Ride actualizado", "../interfaz/gestionRides.php", "success");
    } else {
        redirigirMsjRide(
            "❌ Error al actualizar",
            "../interfaz/formularioRide.php","error", $datos,
            null, $id_ride, 'actualizar'
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
        redirigirMsjRide("✅ Ride registrado", "../interfaz/gestionRides.php", "success");
    } else {
        redirigirMsjRide(
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
