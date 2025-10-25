<?php
include_once("../configuracion/conexion.php");

function insertarVehiculo($id_chofer, $placa, $color, $marca, $modelo, $anno, $asientos, $foto) {

    $conexion = conexionBD();
    try {

        $sql = "INSERT INTO vehiculos (id_chofer, numero_placa, color, marca, modelo, anno, capacidad_asientos, fotografia)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "isssiiis",
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

        mysqli_stmt_execute($consulta);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;
    } catch (Exception $e) {
        error_log("Error en eliminarVehiculo: " . $e->getMessage());
        return false;
    }
}

?>
