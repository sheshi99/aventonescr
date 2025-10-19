<?php
include_once 'conexion.php';

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
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $cedula);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    return $usuario;
}


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

function cambiarEstadoUsuario($id, $estado) {
    $conexion = conexionBD();

    $sql = "UPDATE usuarios SET estado = ? WHERE id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "si", $estado, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ["success" => true];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($stmt);
        return ["success" => false, "error" => $error];
    }
}


function insertarUsuario($nombre, $apellido, $cedula, 
                         $fecha_nacimiento, $correo, $telefono, 
                         $fotografia, $contrasena, $rol) {
    $conexion = conexionBD();
    
    $inicializacion = inicializacionDatosUsuario($rol, $contrasena);
    
    $sql = "INSERT INTO usuarios 
        (nombre, apellido, cedula, fecha_nacimiento, 
        correo, telefono, fotografia, contrasena, 
        rol, estado, token_activacion)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $consulta = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($consulta, "sssssssssss",
                           $nombre, $apellido, $cedula, 
                           $fecha_nacimiento, $correo, $telefono,
                           $fotografia, $inicializacion['hash'], 
                           $rol, $inicializacion['estado'], $inicializacion['token']
    );
    if (mysqli_stmt_execute($consulta)) {
        //Obtine el ultimo ID, para enviar el token.
        $ultimo_id = mysqli_insert_id($conexion);
        mysqli_stmt_close($consulta);
        return ["success" => true, "id_usuario" => $ultimo_id, "token" => $inicializacion['token']];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($consulta);
        return ["success" => false, "error" => $error];
    }
}
?>