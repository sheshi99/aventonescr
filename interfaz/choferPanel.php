<?php

/*
 * --------------------------------------------------------------
 * Archivo: choferPanel.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Es la interfaz del panel del chofer, que muestra un mensaje de bienvenida, permite
 * editar el perfil, cerrar sesión y acceder a las secciones de gestión de vehículos, 
 * gestión de rides y ver sus reservas.
 * --------------------------------------------------------------
 */

session_start();

// Verificar sesión
if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
$nombre_chofer = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Chofer';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Chofer</title>
    <link rel="stylesheet" href="../Estilos/estilosPanelUsuarios.css?v=2">
</head>
<body>
    <!-- Header con bienvenida -->
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

        <h2>Bienvenido al Panel de Chofer, <?= htmlspecialchars($nombre_chofer) ?></h2>

        <form action="../logica/cerrarSesion.php" method="post">
            <button type="submit" class="btn-cerrar">Cerrar</button>
        </form>
    </header>

            <?php if(!empty($_SESSION['mensaje'])): ?>
                <p style="color: <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'red' : 'green' ?>; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
                </p>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

    <!-- Tarjeta principal con botones -->
    <div class="card">
        <div class="menu">
            <button onclick="location.href='gestionVehiculos.php'">Gestión de Vehículos</button>
            <button onclick="location.href='gestionRides.php'">Gestión de Rides</button>
            <button onclick="location.href='misReservas.php'">Mis Reservas</button>
        </div>
    </div>
</body>
</html>

