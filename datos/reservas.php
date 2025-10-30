<?php
include_once("../configuracion/conexion.php");

function insertarReserva($idRide, $idPasajero) {
    $conexion = conexionBD(); // tu función de conexión

    $sql = "INSERT INTO reservas (id_ride, id_pasajero, estado) VALUES (?, ?, 'Pendiente')";

    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        return false;
    }

    // Vinculamos los parámetros: dos enteros
    mysqli_stmt_bind_param($stmt, "ii", $idRide, $idPasajero);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    return $ok;
}

function obtenerReservasPorUsuario($idUsuario, $rol) {
    $conexion = conexionBD(); 

    if ($rol === 'Pasajero') {
        $sql = "SELECT r.id_reserva, ri.salida, ri.llegada, ri.dia, ri.hora, r.estado
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                WHERE r.id_pasajero = ?
                ORDER BY r.id_reserva DESC";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    } else { // Chofer
        $sql = "SELECT r.id_reserva, ri.salida, ri.llegada, ri.dia, ri.hora, r.estado
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                WHERE ri.id_chofer = ?
                ORDER BY r.id_reserva DESC";

        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    }

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



function actualizarEstadoReserva($idReserva, $nuevoEstado) {
    $conexion = conexionBD(); // tu función de conexión MySQL

    $sql = "UPDATE reservas SET estado = ? WHERE id_reserva = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoEstado, $idReserva);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    return $ok;
}

?>



