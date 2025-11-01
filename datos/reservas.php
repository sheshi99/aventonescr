<?php
include_once("../configuracion/conexion.php");
include_once("rides.php"); // Necesario para obtenerEspaciosDisponibles

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

function obtenerReservasPorUsuario($idUsuario, $rol) {
    $conexion = conexionBD();

    if ($rol === 'Pasajero') {
        $sql = "SELECT r.id_reserva, ri.nombre, ri.salida, ri.llegada, ri.dia, ri.hora, r.estado
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                WHERE r.id_pasajero = ?
                ORDER BY ri.dia DESC, ri.hora DESC";
    } else { // Chofer
        $sql = "SELECT r.id_reserva, ri.nombre, ri.salida, ri.llegada, ri.dia, ri.hora, r.estado
                FROM reservas r
                JOIN rides ri ON r.id_ride = ri.id_ride
                WHERE ri.id_chofer = ?
                ORDER BY ri.dia DESC, ri.hora DESC";
    }

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $activas = [];
    $pasadas = [];
    $now = date('Y-m-d H:i:s');

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $fechaHoraRide = $fila['dia'] . ' ' . $fila['hora'];

        if ($fechaHoraRide >= $now && in_array($fila['estado'], ['Pendiente', 'Aceptada'])) {
            // Reservas activas
            $activas[] = $fila;
        } else {
            // Reservas pasadas
            if ($fila['estado'] === 'Aceptada' && $fechaHoraRide < $now) {
                $fila['estado'] = 'Realizado';
            }
            // Las canceladas o rechazadas mantienen su estado
            $pasadas[] = $fila;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    return ['activas' => $activas, 'pasadas' => $pasadas];
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

/* ✅ Chofer acepta o rechaza con validación de espacios */
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

/* ✅ Pasajero cancela */
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

