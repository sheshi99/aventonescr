<?php

/*
 * Archivo: vehiculos.php
 * Autores: Seidy Alanis y Walbyn González
 * 
 * Descripción:
 * Funciones para gestionar vehículos mediante consultas SQL: 
 * insertar, actualizar, eliminar, obtener por ID, obtener por chofer y verificar placas.
 * Todas las funciones implementan manejo de errores mediante try-catch
 * para capturar excepciones y registrar posibles fallos en la base de datos.
 */

include_once(__DIR__ . "/../configuracion/conexion.php");


function insertarVehiculo($id_chofer, $placa, $color, $marca, $modelo, $anno, $asientos, $foto) {

    $conexion = conexionBD();
    try {

        $sql = "INSERT INTO vehiculos (id_chofer, numero_placa, color, marca, modelo, anno, capacidad_asientos, fotografia)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "issssiis",
            $id_chofer, $placa, $color, $marca, $modelo, $anno, $asientos, $foto
        );

        mysqli_stmt_execute($consulta);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;
    } catch (Exception $e) {
        error_log("Error en insertarVehiculo: " . $e->getMessage());
        return false;
    }
}

function placaExiste($placa, $id_vehiculo = null) {
    try {
        $conexion = conexionBD();

        if ($id_vehiculo) {
            // Al actualizar: excluye el vehículo actual
            $sql = "SELECT COUNT(*) AS existe FROM vehiculos WHERE numero_placa = ? AND id_vehiculo != ?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "si", $placa, $id_vehiculo);
        } else {
            // Al registrar: verifica toda la tabla
            $sql = "SELECT COUNT(*) AS existe FROM vehiculos WHERE numero_placa = ?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "s", $placa);
        }

        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $fila = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return $fila['existe'] > 0;
    } catch (Exception $e) {
        error_log("Error en placaExiste: " . $e->getMessage());
        return false; // o true, según prefieras manejar el error
    }
}



function obtenerVehiculosPorChofer($id_chofer) {

    // Conexión
    $conexion = conexionBD();
    try {

        // Consulta SQL
        $sql = "SELECT * FROM vehiculos WHERE id_chofer = ?";
        $consulta = mysqli_prepare($conexion, $sql);

        // Asignar parámetro
        mysqli_stmt_bind_param($consulta, "i", $id_chofer);

        // Ejecutar consulta
        mysqli_stmt_execute($consulta);

        // Obtener resultados
        $resultado = mysqli_stmt_get_result($consulta);

        $vehiculos = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $vehiculos[] = $fila;
        }

        // Cerrar conexiones
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return $vehiculos;

    } catch (Exception $e) {
        error_log("Error en obtenerVehiculosPorChofer: " . $e->getMessage());
        return [];
    }
}

function actualizarVehiculo($id_vehiculo, $placa, $color, $marca, $modelo, $anno, $asientos, $foto) {
    $conexion = conexionBD();
    try {
        $sql = "UPDATE vehiculos 
                SET numero_placa = ?, color = ?, marca = ?, modelo = ?, anno = ?, capacidad_asientos = ?, fotografia = ?
                WHERE id_vehiculo = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "ssssiisi", 
            $placa, $color, $marca, $modelo, $anno, $asientos, $foto, $id_vehiculo
        );

        mysqli_stmt_execute($consulta);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;
    } catch (Exception $e) {
        error_log("Error en actualizarVehiculo: " . $e->getMessage());
        return false;
    }
}


function obtenerVehiculoPorId($id_vehiculo) {
    $conexion = conexionBD();
    try {
        $sql = "SELECT * FROM vehiculos WHERE id_vehiculo = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_vehiculo);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $vehiculo = mysqli_fetch_assoc($resultado); // Devuelve un solo registro o null

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return $vehiculo; // Array asociativo o null si no existe
    } catch (Exception $e) {
        error_log("Error en obtenerVehiculoPorId: " . $e->getMessage());
        return null;
    }
}


function eliminarVehiculo($id_vehiculo) {
    $conexion = conexionBD();
    try {
        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_vehiculo);

        if (!mysqli_stmt_execute($consulta)) {
            $errno = mysqli_errno($conexion);
            $error = mysqli_error($conexion);

            mysqli_stmt_close($consulta);
            mysqli_close($conexion);

            // Error por restricción de clave foránea
            if ($errno == 1451) { 
                throw new Exception("El vehículo está está asociado a otra información
                 y no se puede eliminar.");
            } else {
                throw new Exception($error);
            }
        }

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;

    } catch (Exception $e) {
        error_log("Error en eliminarVehiculo: " . $e->getMessage());
        // Re-lanzamos la excepción para PHP mostrar mensaje adecuado
        throw $e;
    }
}


?>
