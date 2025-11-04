<?php

/*
 * Archivo: cambioContraseña.php
 * Autores: Seidy Alanis y Walbyn González
 * 
 * Descripción: Formulario para que el usuario logueado pueda cambiar su contraseña.
 * Muestra mensajes de éxito o error y redirige según el rol del usuario al cancelar.
 */

session_start();

$usuario = $_SESSION['usuario'];
switch ($usuario['rol']) {
    case 'Administrador':
        $urlCancelar = 'adminPanel.php';
        break;
    case 'Chofer':
        $urlCancelar = 'choferPanel.php';
        break;
    case 'Pasajero':
        $urlCancelar = 'pasajeroPanel.php';
        break;
    default:
        $urlCancelar = 'login.php';
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

// Recuperar mensajes si existen
$mensaje = $_SESSION['mensaje']['texto'] ?? '';
$tipo = $_SESSION['mensaje']['tipo'] ?? '';
unset($_SESSION['mensaje']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../Estilos/estilosRegistro.css?v=3">
</head>
<body>
<div class="registro-container">
    <div class="form-card">
        <h2>Cambiar Contraseña</h2>

        <?php if($mensaje): ?>
            <p class="mensaje <?= $tipo === 'error' ? 'error' : 'success' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </p>
        <?php endif; ?>

        <form action="../logica/procesarCambioContraseña.php" method="post">

            <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario']['id_usuario'] ?>">

            <div class="input-group">
                <label>Contraseña actual:</label>
                <input type="password" name="contrasena_actual" class="input-password" required>
            </div>

            <div class="input-group">
                <label>Nueva contraseña:</label>
                <input type="password" name="nueva_contrasena" class="input-password" required>
            </div>

            <div class="input-group">
                <label>Confirmar nueva contraseña:</label>
                <input type="password" name="confirmar_contrasena" class="input-password" required>
            </div>
            <button type="submit" class="btn-registrar">Cambiar Contraseña</button>
        </form>

        <form action="<?= $urlCancelar ?>" method="get" class="form-salir">
            <button type="submit" class="btn-cerrar-x">✖</button>
        </form>
    </div>
</div>
</body>
</html>
