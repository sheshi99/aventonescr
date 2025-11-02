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

/**
 * Consulta rides desde la base de datos según filtros de salida y llegada.
 */
function consultarRides($salida = '', $llegada = '') {
    $conexion = conexionBD();

    $sql = "SELECT r.id_ride, r.nombre, r.salida, r.llegada, r.dia, r.hora,
                   r.costo, v.marca, v.modelo, v.anno
            FROM rides r
            JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
            WHERE 1=1";

    $params = [];
    $tipos = '';

    if ($salida !== '') {
        $sql .= " AND r.salida LIKE ?";
        $params[] = "%$salida%";
        $tipos .= 's';
    }

    if ($llegada !== '') {
        $sql .= " AND r.llegada LIKE ?";
        $params[] = "%$llegada%";
        $tipos .= 's';
    }

    // ===== Agregar condición para rides que no han ocurrido aún =====
    $sql .= " AND STR_TO_DATE(CONCAT(r.dia,' ',r.hora), '%Y-%m-%d %H:%i:%s') > NOW()";

    $stmt = mysqli_prepare($conexion, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$params);
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


/**
 * Filtra solo los rides que tienen espacios disponibles.
 */
function filtrarEspaciosDisponibles($rides) {
    $resultado = [];
    foreach ($rides as $fila) {
        $disponibles = obtenerEspaciosDisponibles($fila['id_ride']);
        if ($disponibles > 0) {
            $fila['espacios'] = $disponibles;
            $resultado[] = $fila;
        }
    }
    return $resultado;
}

/**
 * Ordena los rides según columna y dirección.
 * Columnas válidas: 'dia', 'salida', 'llegada'
 * Direcciones válidas: 'ASC', 'DESC'
 */
function ordenamientoRides($rides, $columna = 'dia', $direccion = 'ASC') {
    $columnas_validas = ['dia', 'salida', 'llegada'];
    $direccion_validas = ['ASC', 'DESC'];

    if (!in_array($columna, $columnas_validas)) $columna = 'dia';
    if (!in_array($direccion, $direccion_validas)) $direccion = 'ASC';

    usort($rides, function($a, $b) use ($columna, $direccion) {
        // Primero comparo por la columna elegida
        $cmp = strcmp($a[$columna], $b[$columna]);
        if ($cmp === 0) {
            // Si es la misma fecha/lugar, comparo por hora
            $cmp = strtotime($a['hora']) <=> strtotime($b['hora']);
        }
        return ($direccion === 'ASC') ? $cmp : -$cmp;
    });

    return $rides;
}

/**
 * Función principal para buscar rides filtrados y ordenados
 */
function buscarRides($salida = '', $llegada = '', $columna = 'dia', $direccion = 'ASC') {
    $rides = consultarRides($salida, $llegada);
    $rides = filtrarEspaciosDisponibles($rides);
    $rides = ordenamientoRides($rides, $columna, $direccion);
    return $rides;
}


/**
 * Obtiene la cantidad de espacios disponibles para un ride
 */
function obtenerEspaciosDisponibles($idRide) {
    $conexion = conexionBD();

    // Pasajeros con reserva aceptada
    $sql = "SELECT COUNT(*) AS ocupados FROM reservas 
            WHERE id_ride = ? AND estado = 'Aceptada'";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idRide);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $ocupados = mysqli_fetch_assoc($resultado)['ocupados'] ?? 0;
    mysqli_stmt_close($stmt);

    // Capacidad total del ride
    $sql2 = "SELECT espacios FROM rides WHERE id_ride = ?";
    $stmt2 = mysqli_prepare($conexion, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $idRide);
    mysqli_stmt_execute($stmt2);
    $resultado2 = mysqli_stmt_get_result($stmt2);
    $total = mysqli_fetch_assoc($resultado2)['espacios'] ?? 0;
    mysqli_stmt_close($stmt2);

    mysqli_close($conexion);
    return $total - $ocupados;
}
?>
