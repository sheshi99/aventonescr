<?php
session_start();
include_once("../datos/rides.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info', $datosFormulario = [], $campoError = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo, 'campo_error' => $campoError];

    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    $_SESSION['datos_formulario'] = $datosFormulario;
    header("Location: $destino");
    exit;
}

function obtenerDatosRide() {
    return [
        'id_vehiculo' => trim($_POST['id_vehiculo'] ?? ''),
        'nombre'     => trim($_POST['nombre'] ?? ''),
        'salida'     => trim($_POST['salida'] ?? ''),
        'llegada'    => trim($_POST['llegada'] ?? ''),
        'dia'        => trim($_POST['dia'] ?? ''),
        'hora'       => trim($_POST['hora'] ?? ''),
        'costo'      => trim($_POST['costo'] ?? ''),
        'espacios'   => trim($_POST['espacios'] ?? '')
    ];
}

function validarRide($datos) {
    foreach ($datos as $campo => $valor) {
        if (empty($valor)) {
            mostrarMensajeYRedirigir(
                "El campo $campo es obligatorio",
                "../interfaz/formularioRide.php",
                "error",
                $datos,
                $campo
            );
        }
    }
    if (!is_numeric($datos['costo']) || $datos['costo'] <= 0) {
        mostrarMensajeYRedirigir("Costo inválido", "../interfaz/formularioRide.php", "error", $datos, 'costo');
    }
    if (!is_numeric($datos['espacios']) || $datos['espacios'] < 1) {
        mostrarMensajeYRedirigir("Espacios inválidos", "../interfaz/formularioRide.php", "error", $datos, 'espacios');
    }
}

function guardarRide($id_chofer) {
    $datos = obtenerDatosRide();
    validarRide($datos);

    $accion = $_POST['accion'] ?? 'insertar';

    if ($accion === 'actualizar' && !empty($_POST['id_ride'])) {
        $id_ride = $_POST['id_ride'];
        $ok = actualizarRide($id_ride, $datos['id_vehiculo'], $datos['nombre'], $datos['salida'], 
                             $datos['llegada'], $datos['dia'], $datos['hora'], $datos['costo'], $datos['espacios']);
        if ($ok) {
            mostrarMensajeYRedirigir("Ride actualizado", "../interfaz/gestionRides.php", "success");
        } else {
            mostrarMensajeYRedirigir("Error al actualizar", "../interfaz/formularioRide.php", "error", $datos);
        }
    } else {
        $ok = insertarRide($id_chofer, $datos['id_vehiculo'], $datos['nombre'], $datos['salida'], 
                           $datos['llegada'], $datos['dia'], $datos['hora'], $datos['costo'], $datos['espacios']);
        if ($ok) {
            mostrarMensajeYRedirigir("Ride registrado", "../interfaz/gestionRides.php", "success");
        } else {
            mostrarMensajeYRedirigir("Error al registrar", "../interfaz/formularioRide.php", "error", $datos);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    guardarRide($id_chofer);
} else {
    die("Acceso no permitido");
}
