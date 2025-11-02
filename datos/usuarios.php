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
    try {
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

        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);

        if ($inicializacion['token']) {
            enviarCorreoActivacion($correo, $nombre, $inicializacion['token'],$rol);
        }

        mysqli_close($conexion);
        return ["success" => true, "token" => $inicializacion['token']];
    } catch (Exception $e) {
        error_log("Error en insertarUsuario: " . $e->getMessage());
        return ["success" => false, "error" => $e->getMessage()];
    }
}

function activarUsuarioPorToken($token) {
    try {
        $conexion = conexionBD();

        $sql = "SELECT * FROM usuarios WHERE token_activacion = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "s", $token);
        mysqli_stmt_execute($consulta);
        $result = mysqli_stmt_get_result($consulta);
        $usuario = mysqli_fetch_assoc($result);
        mysqli_stmt_close($consulta);

        if (!$usuario) {
            mysqli_close($conexion);
            return false;
        }

        $sql = "UPDATE usuarios SET estado = 'Activo', token_activacion = NULL WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $usuario['id_usuario']);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return true;
    } catch (Exception $e) {
        error_log("Error en activarUsuarioPorToken: " . $e->getMessage());
        return false;
    }
}

function listarUsuariosPorRol($rol) {
    try {
        if (empty($rol)) return [];

        $conexion = conexionBD();
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
        mysqli_close($conexion);
        return $usuarios;
    } catch (Exception $e) {
        error_log("Error en listarUsuariosPorRol: " . $e->getMessage());
        return [];
    }
}

function verificarUsuarioExistente($cedula, $correo) {
    try {
        $conexion = conexionBD();
        $sql = "SELECT COUNT(*) AS encontrado FROM usuarios WHERE cedula = ? OR correo = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "ss", $cedula, $correo);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $fila = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return $fila['encontrado'] > 0;
    } catch (Exception $e) {
        error_log("Error en verificarUsuarioExistente: " . $e->getMessage());
        return true; // Asumir que existe para no permitir duplicados si falla
    }
}

function obtenerUsuarioPorCedula($cedula) {
    try {
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
    } catch (Exception $e) {
        error_log("Error en obtenerUsuarioPorCedula: " . $e->getMessage());
        return null;
    }
}

function obtenerUsuarioPorId($id_usuario) {
    try {
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
    } catch (Exception $e) {
        error_log("Error en obtenerUsuarioPorId: " . $e->getMessage());
        return null;
    }
}



function cambiarEstadoUsuario($id, $estado) {
    try {
        $conexion = conexionBD();
        $sql = "UPDATE usuarios SET estado = ? WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "si", $estado, $id);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return ["success" => true];
    } catch (Exception $e) {
        error_log("Error en cambiarEstadoUsuario: " . $e->getMessage());
        return ["success" => false, "error" => $e->getMessage()];
    }
}

function editarUsuario($id_usuario, $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $fotografia = null) {
    try {
        $conexion = conexionBD();

        if ($fotografia) {
            $sql = "UPDATE usuarios 
                    SET nombre = ?, apellido = ?, cedula = ?, fecha_nacimiento = ?, correo = ?, telefono = ?, rol = ?, fotografia = ?
                    WHERE id_usuario = ?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "ssssssssi", 
                $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $fotografia, $id_usuario
            );
        } else {
            $sql = "UPDATE usuarios 
                    SET nombre = ?, apellido = ?, cedula = ?, fecha_nacimiento = ?, correo = ?, telefono = ?, rol = ?
                    WHERE id_usuario = ?";
            $consulta = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($consulta, "sssssssi", 
                $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $rol, $id_usuario
            );
        }

        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);
        return ["success" => true];
    } catch (Exception $e) {
        error_log("Error en editarUsuario: " . $e->getMessage());
        return ["success" => false, "error" => $e->getMessage()];
    }
}

// Verifica si la contraseña proporcionada coincide con la del usuario
function confirmarContrasena($id_usuario, $contrasena) {
    try {
        $conexion = conexionBD();
        $sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "i", $id_usuario);
        mysqli_stmt_execute($consulta);
        $resultado = mysqli_stmt_get_result($consulta);
        $usuario = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        if (!$usuario) return false;

        return password_verify($contrasena, $usuario['contrasena']);
    } catch (Exception $e) {
        error_log("Error en verificarContrasena: " . $e->getMessage());
        return false;
    }
}

// Actualiza la contraseña del usuario
function actualizarContrasena($id_usuario, $nueva_contrasena) {
    try {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

        $conexion = conexionBD();
        $sql = "UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?";
        $consulta = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($consulta, "si", $hash, $id_usuario);
        mysqli_stmt_execute($consulta);
        mysqli_stmt_close($consulta);
        mysqli_close($conexion);

        return ["success" => true];
    } catch (Exception $e) {
        error_log("Error en actualizarContrasena: " . $e->getMessage());
        return ["success" => false, "error" => $e->getMessage()];
    }
}

?>
