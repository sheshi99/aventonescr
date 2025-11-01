<?php
session_start();

// Validar que haya un usuario logueado y que sea pasajero
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
    <link rel="stylesheet" href="../Estilos/estilosPanelChofer.css?v=2">
</head>
<body>
    <!-- Header -->
    <header class="chofer-header">
        <h2>Bienvenido al Panel de Pasajeros, <?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellido']) ?></h2>
        <form action="../logica/cerrarSesion.php" method="post">
            <button type="submit" class="btn-cerrar">Cerrar</button>
        </form>
    </header>

        <!-- Mensaje opcional -->
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

    <!-- Tarjeta principal -->
    <div class="chofer-card">
        <div class="menu-chofer">
            <!-- Botón Mis Reservas -->
            <form action="misReservas.php" method="post">
                <input type="hidden" name="desde_panel" value="1">
                <button type="submit">Mis Reservas</button>
            </form>

            <!-- Botón Buscar Rides -->
            <form action="buscarRide.php" method="post">
                <input type="hidden" name="desde_panel" value="1">
                <button type="submit">Buscar Rides</button>
            </form>
        </div>
    </div>

</body>
</html>
