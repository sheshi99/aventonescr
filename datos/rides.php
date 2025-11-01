<?php
include_once("../configuracion/conexion.php");


function insertarRide($id_chofer, $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios) {
    $conexion = conexionBD();
    try {
        $sql = "INSERT INTO rides (id_chofer, id_vehiculo, nombre, salida, llegada, dia, hora, costo, espacios)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "iisssssdi",
            $id_chofer, $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios
        );
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return true;
    } catch (Exception $e) {
        error_log("Error al insertarRide: " . $e->getMessage());
        return false;
    }
}


function actualizarRide($id_ride, $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios) {
    $conexion = conexionBD();
    try {
        $sql = "UPDATE rides 
                SET id_vehiculo=?, nombre=?, salida=?, llegada=?, dia=?, hora=?, costo=?, espacios=? 
                WHERE id_ride=?";
        $consulta = mysqli_prepare($conexion, $sql);

        mysqli_stmt_bind_param($consulta, "isssssdii",
        $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios, $id_ride
        );

        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return true;
    } catch (Exception $e) {
        error_log("Error en actualizarRide: " . $e->getMessage());
        return false;
    }
}



function eliminarRide($id_ride) {
    $conexion = conexionBD();
    try {
        $sql = "DELETE FROM rides WHERE id_ride = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_ride);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return true;
    } catch (Exception $e) {
        error_log("Error al eliminarRide: " . $e->getMessage());
        return false;
    }
}


function obtenerRidePorId($id_ride) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT * FROM rides WHERE id_ride = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_ride);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $ride = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return $ride;
    } catch (Exception $e) {
        error_log("Error en obtenerRidePorId: " . $e->getMessage());
        return null;
    }
}


function obtenerRidesPorVehiculo($id_vehiculo) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT * FROM rides WHERE id_vehiculo = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_vehiculo);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        
        $rides = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $rides[] = $fila;
        }
        
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return $rides;
    } catch (Exception $e) {
        error_log("Error en obtenerRidesPorVehiculo: " . $e->getMessage());
        return [];
    }
}

function obtenerRidesPorChofer($id_chofer) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT r.*, v.numero_placa AS placa_vehiculo
                FROM rides r
                INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                WHERE r.id_chofer = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_chofer);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        
        $rides = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $rides[] = $fila;
        }
        
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return $rides;
    } catch (Exception $e) {
        error_log("Error al obtenerRidesPorChofer: " . $e->getMessage());
        return [];
    }
}


function buscarRides($salida = '', $llegada = '') {
    $conexion = conexionBD();

    $sql = "SELECT r.id_ride, r.nombre, r.salida, r.llegada, r.dia, r.hora,
                   r.costo, r.espacios, v.marca, v.modelo, v.anno
            FROM rides r
            JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
            WHERE r.espacios > 0";

    // Aplicar filtros dinámicos
    $parametros = [];
    $tipos = '';

    if ($salida !== '') {
        $sql .= " AND r.salida LIKE ?";
        $parametros[] = "%$salida%";
        $tipos .= 's';
    }

    if ($llegada !== '') {
        $sql .= " AND r.llegada LIKE ?";
        $parametros[] = "%$llegada%";
        $tipos .= 's';
    }

    $sql .= " ORDER BY r.dia, r.hora ASC";

    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        error_log("Error preparando la consulta: " . mysqli_error($conexion));
        return [];
    }

    if (!empty($parametros)) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $rides = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $rides[] = $fila;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $rides;
}
?>
