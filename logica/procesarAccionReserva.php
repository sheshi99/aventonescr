<?php
session_start();
include_once("../datos/reservas.php");
include_once("../datos/rides.php"); // necesario para obtenerEspaciosDisponibles

function redirigir($ruta = "../interfaz/misReservas.php") {
    header("Location: $ruta");
    exit;
}

function validarSesion() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../interfaz/login.php");
        exit;
    }
    return $_SESSION['usuario'];
}

function validarParametros() {
    $idReserva = $_POST['id_reserva'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if (!$idReserva || !$accion) {
        redirigir();
    }

    return [$idReserva, $accion];
}

function procesarAccion($usuario, $idReserva, $accion) {
    if ($usuario['rol'] === 'Chofer') {
        if ($accion === 'aceptar') {
            $exito = aceptarReservaChofer($idReserva);
            if (!$exito) {
                $_SESSION['mensaje'] = ['texto' => 'No hay espacios disponibles para aceptar esta reserva.', 'tipo' => 'error'];
            }
        } elseif ($accion === 'rechazar') {
            rechazarReservaChofer($idReserva);
        }
    } elseif ($usuario['rol'] === 'Pasajero') {
        if ($accion === 'cancelar') {
            cancelarReservaPasajero($idReserva, $usuario['id_usuario']);
        }
    }
}

function main() {
    $usuario = validarSesion();
    [$idReserva, $accion] = validarParametros();
    procesarAccion($usuario, $idReserva, $accion);
    redirigir();
}

// --- EJECUCIÓN ---
main();
?>

