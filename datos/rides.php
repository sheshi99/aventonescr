<?php

/*
 * Archivo: rides.php
 * Autores: Seidy Alanis y Walbyn González
 * 
 * Descripción:
 * Funciones para gestionar rides mediante consultas SQL: 
 * insertar, actualizar, eliminar, obtener por ID, consultar según filtros, 
 * calcular espacios disponibles, y ordenar o buscar rides.
 * Todas las funciones implementan manejo de errores mediante try-catch
 * para capturar excepciones y registrar posibles fallos en la base de datos.
 */

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

function rideTieneReservasRealizadas($id_ride) {
    try {
        $conexion = conexionBD();
        $sql = "SELECT COUNT(*) AS total 
                FROM reservas 
                WHERE id_ride = ? ";
        $consulta = mysqli_prepare($conexion, $sql);
        if (!$consulta) {
            throw new Exception("Error al preparar la consulta: " . mysqli_error($conexion));
        }

        mysqli_stmt_bind_param($consulta, "i", $id_ride);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $fila = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return $fila['total'] > 0;

    } catch (Exception $e) {
        error_log("Error en rideTieneReservasRealizadas: " . $e->getMessage());
        return true; 
    }
}


function eliminarRide($id_ride) {
    $conexion = conexionBD();
    try {
        $sql = "DELETE FROM rides WHERE id_ride = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_ride);

        if (!mysqli_stmt_execute($consulta)) {
            $errno = mysqli_errno($conexion);
            $error = mysqli_error($conexion);

            mysqli_stmt_close($consulta);
            mysqli_close($conexion);

            // Error por restricción de clave foránea
            if ($errno == 1451) { 
                throw new Exception("El ride está asociado a otra información 
                                    y no se puede eliminar.");
            } else {
                throw new Exception($error);
            }
        }

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;
    } catch (Exception $e) {
        error_log("Error en eliminarRide: " . $e->getMessage());
        // Aquí puedes devolver el mensaje para mostrar al usuario
        return $e->getMessage();
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


function consultarRides($fecha = '', $salida = '', $llegada = '') {
    $conexion = conexionBD();
    try {
        $sql = "SELECT r.id_ride, r.nombre, r.salida, r.llegada, r.dia, r.hora,
                       r.costo, r.espacios, v.marca, v.modelo, v.anno
                FROM rides r
                JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                WHERE 1=1";

        $params = [];
        $tipos = '';

        if ($fecha !== '') {
            $sql .= " AND r.dia = ?";
            $params[] = $fecha;
            $tipos .= 's';
        }

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

        $sql .= " AND STR_TO_DATE(CONCAT(r.dia,' ',r.hora), '%Y-%m-%d %H:%i:%s') > NOW()";

        $consulta = mysqli_prepare($conexion, $sql);
        if (!$consulta) throw new Exception(mysqli_error($conexion));

        if ($params) {
            mysqli_stmt_bind_param($consulta, $tipos, ...$params);
        }

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
        error_log("Error en consultarRides: " . $e->getMessage());
        mysqli_close($conexion);
        return [];
    }
}

function filtrarEspaciosDisponibles($rides) {
    foreach ($rides as &$fila) {
        try {
            $fila['espacios_disponibles'] = obtenerEspaciosDisponibles($fila['id_ride']);
        } catch (Exception $e) {
            error_log("Error al filtrar espacios disponibles: " . $e->getMessage());
            $fila['espacios_disponibles'] = 0;
        }
    }
    return $rides;
}

function ordenamientoRides($rides, $columna = 'dia', $direccion = 'ASC') {
    $columnas_validas = ['dia', 'salida', 'llegada'];
    $direccion_validas = ['ASC', 'DESC'];

    if (!in_array($columna, $columnas_validas)) $columna = 'dia';
    if (!in_array($direccion, $direccion_validas)) $direccion = 'ASC';

    usort($rides, function($a, $b) use ($columna, $direccion) {
        if ($columna === 'dia') {
            $cmp = strtotime($a['dia'] . ' ' . $a['hora']) <=> strtotime($b['dia'] . ' ' . $b['hora']);
        } else {
            // Comparación de strings insensible a mayúsculas/minúsculas
            $cmp = strcasecmp($a[$columna], $b[$columna]);
        }

        return $direccion === 'ASC' ? $cmp : -$cmp;
    });

    return $rides;
}



function buscarRides($fecha = '', $salida = '', $llegada = '', $columna = 'dia', $direccion = 'ASC') {
    $rides = consultarRides($fecha, $salida, $llegada);
    $rides = filtrarEspaciosDisponibles($rides);
    $rides = ordenamientoRides($rides, $columna, $direccion);
    return $rides;
}

function obtenerEspaciosDisponibles($idRide) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT COUNT(*) AS ocupados FROM reservas 
                WHERE id_ride = ? AND estado = 'Aceptada'";
        $consulta = mysqli_prepare($conexion, $sql);
        if (!$consulta) throw new Exception(mysqli_error($conexion));
        mysqli_stmt_bind_param($consulta, "i", $idRide);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $ocupados = mysqli_fetch_assoc($resultado)['ocupados'] ?? 0;
        mysqli_stmt_close($consulta);

        $sql2 = "SELECT espacios FROM rides WHERE id_ride = ?";
        $stmt2 = mysqli_prepare($conexion, $sql2);
        if (!$stmt2) throw new Exception(mysqli_error($conexion));
        mysqli_stmt_bind_param($stmt2, "i", $idRide);
        mysqli_stmt_execute($stmt2);
        $resultado2 = mysqli_stmt_get_result($stmt2);
        $total = mysqli_fetch_assoc($resultado2)['espacios'] ?? 0;
        mysqli_stmt_close($stmt2);

        mysqli_close($conexion);
        return $total - $ocupados;

    } catch (Exception $e) {
        error_log("Error en obtenerEspaciosDisponibles: " . $e->getMessage());
        mysqli_close($conexion);
        return 0;
    }
}


?>
