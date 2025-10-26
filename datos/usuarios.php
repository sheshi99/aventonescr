<?php
include_once ("../configuracion/conexion.php");
include_once ("../configuracion/correo.php");


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

    $sql = "INSERT INTO usuarios (nombre, apellido, cedula, fecha_nacimiento, correo, 
                                  telefono, fotografia, contrasena, rol, estado, token_activacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param( $consulta, "sssssssssss",
        $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $fotografia,
        $inicializacion['hash'], $rol, $inicializacion['estado'], $inicializacion['token']
    );

    if (mysqli_stmt_execute( $consulta)) {
        mysqli_stmt_close( $consulta);

        // Enviar correo solo si hay token
        if ($inicializacion['token']) {
            enviarCorreoActivacion($correo, $nombre, $inicializacion['token']);
        }

        return ["success" => true, "token" => $inicializacion['token']];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close( $consulta);
        return ["success" => false, "error" => $error];
    }
}


function activarUsuarioPorToken($token) {
    $conexion = conexionBD();

    // 1. BUSCAR el usuario por token
    $sql = "SELECT * FROM usuarios WHERE token_activacion = ?";
    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "s", $token);
    mysqli_stmt_execute($consulta);
    
    
    $result = mysqli_stmt_get_result( $consulta); 
    $usuario = mysqli_fetch_assoc($result);

    mysqli_stmt_close($consulta); 

    if (!$usuario) {
        mysqli_close($conexion); 
        return false; 
    }

    $sql = "UPDATE usuarios SET estado = 'Activo', token_activacion = NULL WHERE id_usuario = ?";
    $consulta = mysqli_prepare($conexion, $sql); 
    
    mysqli_stmt_bind_param($consulta, "i", $usuario['id_usuario']);
    
    if (mysqli_stmt_execute($consulta)) {
        mysqli_stmt_close($consulta);
        mysqli_close($conexion); 
        return true;
    } else {
        error_log("Error al activar usuario: " . mysqli_error($conexion));
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return false;
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

function verificarUsuarioExistente($cedula, $correo) {
    $conexion = conexionBD();

    $sql = "SELECT COUNT(*) AS encontrado 
            FROM usuarios 
            WHERE cedula = ? OR correo = ?";
    
    $consulta = mysqli_prepare($conexion, $sql);

    mysqli_stmt_bind_param($consulta, "ss", $cedula, $correo);

    mysqli_stmt_execute($consulta);

    $resultado = mysqli_stmt_get_result($consulta);
    $fila = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($consulta);
    mysqli_close($conexion);

    return $fila['encontrado'] > 0; 
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
    mysqli_close($conexion);
    return $usuario;
}

function obtenerUsuarioPorId($id_usuario) {
    $conexion = conexionBD();
    $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "i", $id_usuario);
    mysqli_stmt_execute($consulta);
    $resultado = mysqli_stmt_get_result($consulta);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($consulta);
    mysqli_close($conexion);
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

function editarUsuario($id_usuario, $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $fotografia = null) {
    $conexion = conexionBD();

    // Si se envió una nueva fotografía, actualiza todo (incluida la foto)
    if ($fotografia) {
        $sql = "UPDATE usuarios 
                SET nombre = ?, apellido = ?, cedula = ?, fecha_nacimiento = ?, correo = ?, telefono = ?, rol = ?, fotografia = ?
                WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "ssssssssi", 
            $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $fotografia, $id_usuario
        );
    } 
    // Si no se cambió la foto, no se actualiza ese campo
    else {
        $sql = "UPDATE usuarios 
                SET nombre = ?, apellido = ?, cedula = ?, fecha_nacimiento = ?, correo = ?, telefono = ?, rol = ?
                WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "sssssssi", 
            $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $id_usuario
        );
    }

    if (mysqli_stmt_execute($consulta)) {
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return ["success" => true];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return ["success" => false, "error" => $error];
    }
}


?>