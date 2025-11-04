<?php

/*
 * --------------------------------------------------------------
 * Archivo: activarCuenta.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Activa la cuenta de un usuario mediante un token y muestra un 
 * mensaje de éxito o error.
 * --------------------------------------------------------------
 */

include_once ("../logica/funcionesInterfaz.php");

$mensaje = '';
$tipo = '';

if (!isset($_GET['token'])) {
    $mensaje = "❌ Token no proporcionado.";
    $tipo = 'error';
} else {
    $token = trim($_GET['token']);
$activado = activarUsuarioPorToken($token);  
 

    if ($activado) {
        $mensaje = "✅ Cuenta activada correctamente. Ya puedes iniciar sesión.";
        $tipo = 'success';
    } else {
        $mensaje = "❌ Token inválido o ya usado.";
        $tipo = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Activación de Cuenta</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background: #f0f8ff; 
        }
        .mensaje {
            padding: 20px 30px;
            border-radius: 8px;
            text-align: center;
            background-color: #555; /* gris neutro */
            color: #fff;
        }
        a { 
            display: inline-block; 
            margin-top: 15px; 
            padding: 8px 16px; 
            color: #fff; 
            text-decoration: none; 
            background: #333; /* gris oscuro */
            border-radius: 5px;
            transition: background 0.3s;
        }
        a:hover {
            background: #111; /* gris más oscuro al pasar el mouse */
        }
    </style>
</head>
<body>
    <div class="mensaje">
        <p><?= $mensaje ?></p>
        <a href="Login.php">Ir al Login</a>
    </div>
</body>
</html>

