<?php

/*
 * --------------------------------------------------------------
 * Archivo: pasajeroPanel.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Es la interfaz del panel de pasajero, que permite editar su perfil, cerrar sesión,
 * y ofrece botones para ver sus reservas o buscar nuevos rides, con
 * validación de sesión para que solo un pasajero pueda acceder.
 * --------------------------------------------------------------
 */

session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Pasajero') {
    $_SESSION['mensaje'] = ['texto' => 'Debes iniciar sesión como pasajero para acceder al panel.', 'tipo' => 'error'];
    header("Location: ../interfaz/login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Pasajero</title>
    <link rel="stylesheet" href="../Estilos/estilosPanelUsuarios.css?v=2">
</head>
<body>

    <header class="header">

        <div class="header-left">
            <div class="menu-contenedor">
                <input type="checkbox" id="toggle-menu" class="toggle-menu">
                <label for="toggle-menu" class="btn-menu">⋮</label>

            <div class="espacio-menu-foto">
                <?php if (!empty($_SESSION['usuario']['fotografia'])): ?>
                    <img src="<?= '../logica/' . htmlspecialchars($_SESSION['usuario']['fotografia']); ?>" 
                        alt="<?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>" 
                        class="foto">
                <?php else: ?>
                    <img src="../Estilos/default-user.png" alt="Usuario" class="foto">
                <?php endif; ?>
            </div>

                <div class="menu-opciones">
                    <form action="cambioContraseña.php" method="get">
                        <input type="hidden" name="cambio" value="1">
                        <button type="submit" class="menu-boton">🔑 Cambiar Contraseña</button>
                    </form>
                 
                     <form action="formularioUsuario.php" method="POST">                
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario']['id_usuario'] ?>">
                        <button type="submit" class="menu-boton">✏️ Editar Perfil</button>
                    </form>

                </div>
            </div>
        </div>

        <h2>Bienvenido al Panel de Pasajeros, <?= htmlspecialchars($usuario['nombre']) ?> 
        <?= htmlspecialchars($usuario['apellido']) ?></h2>

        <form action="../logica/cerrarSesion.php" method="post">
            <button type="submit" class="btn-cerrar">Cerrar</button>
        </form>
    </header>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <?php 
            $mensaje = $_SESSION['mensaje']['texto'];
            $tipo = $_SESSION['mensaje']['tipo'];
            unset($_SESSION['mensaje']);
            $color = $tipo === 'success' ? 'green' : ($tipo === 'error' ? 'red' : 'blue');
        ?>
        <p style="color: <?= $color ?>; font-weight: bold; margin-top: 20px;">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <div class="card">
        <div class="menu">

            <form action="misReservas.php" method="post">
                <input type="hidden" name="desde_panel" value="1">
                <button type="submit">Mis Reservas</button>
            </form>

            <form action="index.php" method="post">
                <input type="hidden" name="desde_panel" value="1">
                <button type="submit">Buscar Rides</button>
            </form>
        </div>
    </div>

</body>
</html>
