<?php
include_once 'conexion.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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



function enviarCorreoActivacion($correo, $nombre, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';   
        $mail->SMTPAuth = true;
        $mail->Username = 'seidyalanis@gmail.com';
        $mail->Password = 'qvaa tmxk roeh mcoe';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Depuración SMTP (para ver errores)
        $mail->SMTPDebug = 2; // 0 = desactivado, 1 = cliente, 2 = cliente y servidor

        // Remitente y destinatario
        $mail->setFrom('seidyalanis@gmail.com', 'AventonesCR');
        $mail->addAddress($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Activación de cuenta';
        $url = $url = "http://localhost/aventonescr/activarCuenta.php?token=$token";
        $mail->Body = "Hola $nombre,<br><br>
                       Para activar tu cuenta haz clic en el siguiente enlace:<br>
                       <a href='$url'>$url</a><br><br>Gracias.";

        $mail->send();
        echo "Correo enviado correctamente a $correo";
        return true;

    } catch (Exception $e) {
        echo "Error al enviar correo: " . $mail->ErrorInfo;
        return false;
    }
}



function insertarUsuario($nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, 
                         $fotografia, $contrasena, $rol) {
    $conexion = conexionBD();
    $inicializacion = inicializacionDatosUsuario($rol, $contrasena);

    $sql = "INSERT INTO usuarios (nombre, apellido, cedula, fecha_nacimiento, correo, 
                                  telefono, fotografia, contrasena, rol, estado, token_activacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssss",
        $nombre, $apellido, $cedula, $fecha_nacimiento, $correo, $telefono, $fotografia,
        $inicializacion['hash'], $rol, $inicializacion['estado'], $inicializacion['token']
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Enviar correo solo si hay token
        if ($inicializacion['token']) {
            enviarCorreoActivacion($correo, $nombre, $inicializacion['token']);
        }

        return ["success" => true, "token" => $inicializacion['token']];
    } else {
        $error = mysqli_error($conexion);
        mysqli_stmt_close($stmt);
        return ["success" => false, "error" => $error];
    }
}


function activarUsuarioPorToken($token) {
    $conexion = conexionBD();
    $sql = "SELECT * FROM usuarios WHERE token_activacion = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$usuario) return false;

    // Activar cuenta
    $sql = "UPDATE usuarios SET estado = 'Activo', token_activacion = NULL WHERE id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $usuario['id_usuario']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return true;
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