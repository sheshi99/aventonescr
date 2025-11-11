<?php

/*
 * --------------------------------------------------------------
 * Archivo: reservas.php
 * Autores: Seidy Alanis y Walbyn González
 * 
 * Descripción:
 * Funciones para gestionar reservas mediante SQL:
 * insertar reservas, obtener reservas por usuario, actualizar estado,
 * aceptar/rechazar por chofer y cancelar por pasajero.
 * Todas las funciones implementan manejo de errores mediante try-catch
 * para capturar excepciones y registrar posibles fallos en la base de datos.
 * --------------------------------------------------------------
 */

//C:\Aventones\httpdocs\aventonescr\configuracion\conexion.php
// DIR captura la ruta del archivo.
include_once(__DIR__ . '/../configuracion/conexion.php');
include_once(__DIR__ . '/rides.php');


include_once("../configuracion/conexion.php");
include_once("rides.php"); 

function insertarReserva($idRide, $idPasajero) {
    $conexion = conexionBD();
    $sql = "INSERT INTO reservas (id_ride, id_pasajero, estado) VALUES (?, ?, 'Pendiente')";
    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) return false;

    mysqli_stmt_bind_param($stmt, "ii", $idRide, $idPasajero);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $ok;
}


function obtenerReservasPorUsuarioSQL($idUsuario, $rol) {
    $conexion = conexionBD();
    
    if ($rol === 'Pasajero') {
        $sql = "SELECT 
                    r.id_reserva, ri.nombre AS ride_nombre, ri.salida, ri.llegada,
                    ri.dia, ri.hora, ri.costo, r.estado,
                    ri.vehiculo_placa, ri.vehiculo_marca, ri.vehiculo_modelo, ri.vehiculo_anio,
                    u.nombre AS chofer_nombre, u.apellido AS chofer_apellido
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                JOIN usuarios u ON ri.id_chofer = u.id_usuario
                WHERE r.id_pasajero = ?
                ORDER BY ri.dia DESC, ri.hora DESC";
    } else {
        $sql = "SELECT 
                    r.id_reserva, ri.nombre AS ride_nombre, ri.salida, ri.llegada,
                    ri.dia, ri.hora, ri.costo, r.estado,
                    ri.vehiculo_placa, ri.vehiculo_marca, ri.vehiculo_modelo, ri.vehiculo_anio,
                    u.nombre AS pasajero_nombre, u.apellido AS pasajero_apellido
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                JOIN usuarios u ON r.id_pasajero = u.id_usuario
                WHERE ri.id_chofer = ?
                ORDER BY ri.dia DESC, ri.hora DESC";
    }

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $reservas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $reservas[] = $fila;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    return $reservas;
}

function clasificacionReservas($reservas) {
    $activas = [];
    $pasadas = [];
    $now = date('Y-m-d H:i:s');

    foreach ($reservas as $fila) {
        $fechaHoraRide = $fila['dia'] . ' ' . $fila['hora'];
        if ($fechaHoraRide >= $now && in_array($fila['estado'], ['Pendiente', 'Aceptada'])) {
            $activas[] = $fila;
        } else {
            if ($fila['estado'] === 'Aceptada' && $fechaHoraRide < $now) {
                $fila['estado'] = 'Realizado';
            }
            $pasadas[] = $fila;
        }
    }

    return ['activas' => $activas, 'pasadas' => $pasadas];
}


function obtenerReservasPorUsuario($idUsuario, $rol) {
    $reservas = obtenerReservasPorUsuarioSQL($idUsuario, $rol);
    return clasificacionReservas($reservas);
}

function obtenerReservaPorId($id_reserva) {
    $conexion = conexionBD();
    $sql = "SELECT * FROM reservas WHERE id_reserva = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_reserva);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reserva = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $reserva;
}

function actualizarEstadoReserva($idReserva, $nuevoEstado) {
    $conexion = conexionBD();
    $sql = "UPDATE reservas SET estado = ? WHERE id_reserva = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoEstado, $idReserva);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $ok;
}


function aceptarReservaChofer($idReserva) {
    $reserva = obtenerReservaPorId($idReserva);
    if (!$reserva) return false;

    $espacios = obtenerEspaciosDisponibles($reserva['id_ride']);
    if ($espacios < 1) return false; // no hay cupos disponibles

    return actualizarEstadoReserva($idReserva, 'Aceptada');
}

function rechazarReservaChofer($idReserva) {
    return actualizarEstadoReserva($idReserva, 'Rechazada');
}


function cancelarReservaPasajero($idReserva, $idPasajero) {
    $conexion = conexionBD();
    $sql = "UPDATE reservas 
            SET estado = 'Cancelada'
            WHERE id_reserva = ? 
              AND id_pasajero = ? 
              AND estado IN ('Pendiente', 'Aceptada')";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idReserva, $idPasajero);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $ok;
}
?>

