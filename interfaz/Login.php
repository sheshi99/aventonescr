<?php

/*
 * --------------------------------------------------------------
 * Archivo: login.php
 * Autores: Seidy Alanis y Walbyn González
 * Fecha: 01/11/2025
 * Descripción:
 * Es la página de inicio de sesión, donde el usuario ingresa su cédula y 
 * contraseña para acceder al sistema.
 * Muestra mensajes de error o éxito según la sesión y ofrece un enlace 
 * para registrarse si no tiene cuenta.
 * --------------------------------------------------------------
 */

session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../Estilos/estilosLogin.css?v=3">
</head>
<body>
    <div class="login-container">

        <div class="login-card">
            <form action="../index.php" method="get" class="form-salir">
                <button type="submit" class="btn-cerrar-x">✖</button>
            </form>

            <h1>Iniciar Sesión</h1>
            <form action="../logica/procesarLogin.php" method="POST">

                <?php if(!empty($_SESSION['mensaje_login']['texto'])): ?>
                    <p style="color: <?= ($_SESSION['mensaje_login']['tipo'] ?? '') === 'error' ? 'red' : 'green' ?>; font-weight: bold;">
                        <?= htmlspecialchars($_SESSION['mensaje_login']['texto']) ?>
                    </p>
                    <?php unset($_SESSION['mensaje_login']); ?>
                <?php endif; ?>

                <div class="input-group">
                    <label>Usuario</label>
                    <input type="text" name="cedula" placeholder="Ingrese su cédula" required>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>

            <p class="registro-texto">
                ¿No tienes cuenta?
                <a href="../interfaz/formularioUsuario.php">Regístrate aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
