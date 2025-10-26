<?php

function conexionBD() {
    $host = "localhost";
    $usuario = "admin";
    $password = "access_BD_90";
    $base_de_datos = "aventones";

    try {
        $conexion = new mysqli($host, $usuario, $password, $base_de_datos);

        if ($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }

        return $conexion;
    } catch (Exception $e) {
        die("Error fatal: " . $e->getMessage());
    }
}
$conexion = conexionBD();

?>