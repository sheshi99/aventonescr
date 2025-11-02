<?php

/*
 * --------------------------------------------------------------
 * Archivo: actiivarCuenta.php
 * Autores: Seidy Alanis y Walbyn González
 * Fecha: 01/11/2025
 * Descripción:
 * Activa la cuenta de un usuario mediante un token y muestra un mensaje de éxito o error.
 * --------------------------------------------------------------
 */

include_once ("../datos/usuarios.php");

$mensaje = '';
$tipo = '';

if (!isset($_GET['token'])) {
    $mensaje = "❌ Token no proporcionado.";
    $tipo = 'error';
} else {
    $token = $_GET['token'];
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
    <link rel="stylesheet" href="../Estilos/estilosActivacion.css">
</head>
<body>
    <div class="mensaje <?= $tipo ?>">
        <p><?= $mensaje ?></p>
        <?php if ($tipo === 'success'): ?>
            <a href="login.php">Ir al Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
