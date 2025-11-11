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


include_once(__DIR__ . "/../configuracion/conexion.php");


function obtenerDatosVehiculo($id_vehiculo) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT numero_placa AS placa, marca, modelo, anno 
                FROM vehiculos 
                WHERE id_vehiculo = ?";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_vehiculo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $vehiculo = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);
        return $vehiculo;
    } catch (Exception $e) {
        error_log("Error en obtenerDatosVehiculo: " . $e->getMessage());
        mysqli_close($conexion);
        return null;
    }
}

function insertarRide($id_chofer, $id_vehiculo, $nombre, $salida, 
                      $llegada, $dia, $hora, $costo, $espacios) {
    $conexion = conexionBD();
    try {
       
        $vehiculo = obtenerDatosVehiculo($id_vehiculo);

        $sql = "INSERT INTO rides 
                (id_chofer, id_vehiculo, nombre, salida, llegada, dia, hora, costo, espacios,
                 vehiculo_placa, vehiculo_marca, vehiculo_modelo, vehiculo_anio)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "iisssssdiisss",
            $id_chofer, $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, 
            $costo, $espacios,
            $vehiculo['placa'], $vehiculo['marca'], $vehiculo['modelo'], $vehiculo['anno']
        );
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return true;
    } catch (Exception $e) {
        error_log("Error al insertarRide: " . $e->getMessage());
        mysqli_close($conexion);
        return false;
    }
}

function rideTieneReservasHistoricas($id_ride) {
    $conexion = conexionBD();
    $sql = "SELECT COUNT(*) AS total 
            FROM reservas 
            WHERE id_ride = ? 
              AND estado IN ('Cancelada', 'Rechazada')";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_ride);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    return $fila['total'] > 0;
}

function actualizarRide($id_ride, $id_vehiculo, $nombre, $salida, $llegada, $dia, 
                        $hora, $costo, $espacios) {

    $conexion = conexionBD();

    try {
        if (rideTieneReservasHistoricas($id_ride)) {
            // Solo actualizar campos editables, no tocamos snapshot del vehículo
            $sql = "UPDATE rides 
                    SET nombre=?, salida=?, llegada=?, dia=?, hora=?, costo=?, espacios=? 
                    WHERE id_ride=?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "sssssdii", 
                $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios, $id_ride
            );
        } else {
            // Actualizar todos los campos incluyendo snapshot del vehículo
            $vehiculo = obtenerDatosVehiculo($id_vehiculo);

            $sql = "UPDATE rides 
                    SET id_vehiculo=?, nombre=?, salida=?, llegada=?, dia=?, hora=?, 
                        costo=?, espacios=?, vehiculo_placa=?, vehiculo_marca=?, 
                        vehiculo_modelo=?, vehiculo_anio=? 
                    WHERE id_ride=?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "isssssdissssi",
                $id_vehiculo, $nombre, $salida, $llegada, $dia, $hora, $costo, $espacios,
                $vehiculo['placa'], $vehiculo['marca'], $vehiculo['modelo'], $vehiculo['anno'],
                $id_ride
            );
        }

        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return true;

    } catch (Exception $e) {
        error_log("Error en actualizarRide: " . $e->getMessage());
        mysqli_close($conexion);
        return false;
    }
}




function rideTieneReservasAceptadas($id_ride) {
    try {
        $conexion = conexionBD();
        $sql = "SELECT COUNT(*) AS total 
                FROM reservas 
                WHERE id_ride = ? AND estado = 'aceptada'";
                
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
        error_log("Error en rideTieneReservasAceptadas: " . $e->getMessage());
        mysqli_close($conexion);
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
        mysqli_close($conexion);
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
        mysqli_close($conexion);
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
        mysqli_close($conexion);
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
        mysqli_close($conexion);
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
            $cmp = strtotime($a['dia'] . ' ' . $a['hora']) <=> strtotime($b['dia'] . ' ' . 
                            $b['hora']);
        } else {
            // Comparación de strings insensible a mayúsculas/minúsculas
            $cmp = strcasecmp($a[$columna], $b[$columna]);
        }

        return $direccion === 'ASC' ? $cmp : -$cmp;
    });

    return $rides;
}


function buscarRides($fecha = '', $salida = '', $llegada = '', $columna = 'dia', 
                    $direccion = 'ASC') {
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
        $consulta2 = mysqli_prepare($conexion, $sql2);
        if (!$consulta2) throw new Exception(mysqli_error($conexion));
        mysqli_stmt_bind_param($consulta2, "i", $idRide);
        mysqli_stmt_execute($consulta2);
        $resultado2 = mysqli_stmt_get_result($consulta2);
        $total = mysqli_fetch_assoc($resultado2)['espacios'] ?? 0;
        mysqli_stmt_close($consulta2);

        mysqli_close($conexion);
        return $total - $ocupados;

    } catch (Exception $e) {
        error_log("Error en obtenerEspaciosDisponibles: " . $e->getMessage());
        mysqli_close($conexion);
        return 0;
    }
}


?>
