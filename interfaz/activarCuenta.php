<?php
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
            color: #fff;
        }
        .success { background-color: #2196F3; } /* azul */
        .error { background-color: #f44336; }   /* rojo */
        a { 
            display: inline-block; 
            margin-top: 15px; 
            padding: 8px 16px; 
            color: #fff; 
            text-decoration: none; 
            background: #1976D2; 
            border-radius: 5px;
        }
    </style>
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
