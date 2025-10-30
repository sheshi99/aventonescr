
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

function obtenerRidesPorChofer($id_chofer) {
    $conexion = conexionBD();
    try {
        // Seleccionamos todos los campos de rides y el número de placa del vehículo
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

function actualizarRide($id_ride, $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios) {
    $conexion = conexionBD();
    try {
        $sql = "UPDATE rides 
                SET id_vehiculo=?, nombre=?, salida=?, llegada=?, dia=?, hora=?, costo=?, espacios=? 
                WHERE id_ride=?";
        $consulta = mysqli_prepare($conexion, $sql);

        // Tipos correctos: i=entero, s=string, d=decimal
        mysqli_stmt_bind_param($consulta, "isssssdii",
            $id_vehiculo, // i
            $nombre,      // s
            $salida,      // s
            $llegada,     // s
            $dia,         // s
            $hora,        // s
            $costo,       // d
            $espacios,    // i
            $id_ride      // i
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


function buscarRides($salida = '', $llegada = '') {
    $conexion = conexionBD(); // Debe devolver un mysqli

    $sql = "SELECT r.id_ride, r.nombre, r.salida, r.llegada, r.dia, r.hora,
                   r.costo, r.espacios, v.marca, v.modelo, v.anno
            FROM rides r
            JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
            WHERE r.espacios > 0";

    // Filtros dinámicos
    if ($salida !== '') {
        $sql .= " AND r.salida LIKE ?";
        $salida = "%$salida%";
    }

    if ($llegada !== '') {
        $sql .= " AND r.llegada LIKE ?";
        $llegada = "%$llegada%";
    }

    $sql .= " ORDER BY r.dia, r.hora ASC";

    $stmt = mysqli_prepare($conexion, $sql);
    if (!$stmt) {
        die("Error preparando la consulta: " . mysqli_error($conexion));
    }

    // Bind según filtros
    if ($salida !== '' && $llegada !== '') {
        mysqli_stmt_bind_param($stmt, "ss", $salida, $llegada);
    } elseif ($salida !== '') {
        mysqli_stmt_bind_param($stmt, "s", $salida);
    } elseif ($llegada !== '') {
        mysqli_stmt_bind_param($stmt, "s", $llegada);
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
