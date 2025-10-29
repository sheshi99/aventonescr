<?php
session_start();
include_once("../datos/rides.php");
include_once("../datos/vehiculos.php");

// Verificar sesión
$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_chofer) {
    header("Location: ../interfaz/login.php");
    exit;
}

// Función para mostrar mensaje y redirigir
function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info', $datosFormulario = []) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];
    $_SESSION['datos_formulario'] = $datosFormulario;
    header("Location: $destino");
    exit;
}

// Obtener datos del formulario
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

// Validar datos del ride
function validarRide($datos) {
    foreach ($datos as $campo => $valor) {
        if (empty($valor)) {
            mostrarMensajeYRedirigir("El campo $campo es obligatorio", "../interfaz/formularioRide.php", "error", $datos);
        }
    }

    if (!is_numeric($datos['costo']) || $datos['costo'] <= 0) {
        mostrarMensajeYRedirigir("Costo inválido", "../interfaz/formularioRide.php", "error", $datos);
    }

    if (!is_numeric($datos['espacios']) || $datos['espacios'] < 1) {
        mostrarMensajeYRedirigir("Espacios inválidos", "../interfaz/formularioRide.php", "error", $datos);
    }
}

function eliminarRideAction($id_ride) {
    if (eliminarRide($id_ride)) {
        mostrarMensajeYRedirigir("✅ Ride eliminado", "../interfaz/gestionRides.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al eliminar", "../interfaz/gestionRides.php", "error");
    }
}

function actualizarRideAction($id_ride) {
    $datos = obtenerDatosRide();
    validarRide($datos);

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
        mostrarMensajeYRedirigir("❌ Error al actualizar", "../interfaz/formularioRide.php", "error", $datos);
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
        mostrarMensajeYRedirigir("❌ Error al registrar", "../interfaz/formularioRide.php", "error", $datos);
    }
}


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
