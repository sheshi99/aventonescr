<?php
include_once 'conexion.php';

function inicializacionDatosUsuario($rol, $contrasena) {
    if ($rol === 'Administrador') {
        $estado = 'Activo';
        $token = null;
    } else {
        $estado = 'Pendiente';
        $token = bin2hex(random_bytes(16));
    }
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    return ['estado' => $estado, 'token' => $token, 'hash' => $hash];
}


function insertarUsuario($nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, 
                         $fotografia, $contrasena, $rol) {
    $conexion = conexionBD();
    
    $inicializacion = inicializacionDatosUsuario($rol, $contrasena);
    
    $sql = "INSERT INTO usuarios (nombre, apellido, cedula, 
                                  fecha_nacimiento, correo, telefono, fotografia,
                                  contrasena, rol, estado, token_activacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "sssssssssss",
        $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $fotografia,
        $inicializacion['hash'], $rol, $inicializacion['estado'], $inicializacion['token']
    );

    if (mysqli_stmt_execute($consulta)) {
        mysqli_stmt_close($consulta);
        return ["success" => true, "token" => $inicializacion['token']];

    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($consulta);
        return [ "success" => false, "error" => $error];
    }
}


function listarUsuariosPorRol($rol) {
    $conexion = conexionBD();
    if (empty($rol)) {
        return [];
    }else{
        $sql = "SELECT * FROM usuarios WHERE rol = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "s", $rol);

        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);

        $usuarios = [];
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $usuarios[] = $fila;
        }
        mysqli_stmt_close($consulta);
        return $usuarios;        
    }
}

function obtenerUsuarioPorCedula($cedula) {
    $conexion = conexionBD();
    $sql = "SELECT * FROM usuarios WHERE cedula = ?";
    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "s", $cedula);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($consulta);
    return $usuario;
}


function cambiarEstadoUsuario($id, $estado) {
    $conexion = conexionBD();

    $sql = "UPDATE usuarios SET estado = ? WHERE id_usuario = ?";
    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "si", $estado, $id);

    if (mysqli_stmt_execute($consulta)) {
        mysqli_stmt_close($consulta);
        return ["success" => true];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($consulta);
        return ["success" => false, "error" => $error];
    }
}


?>