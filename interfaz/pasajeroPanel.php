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
</head>
<body>
<h2>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellido']) ?></h2>

<p>Desde aquí puedes gestionar tus rides y reservas.</p>

<!-- Botón a la gestión de reservas -->
<form action="formularioReserva.php" method="post">
    <!-- Puedes enviar un indicador opcional si quieres -->
    <input type="hidden" name="desde_panel" value="1">
    <button type="submit">Mis Reservas</button>
</form>


<!-- Botón para buscar rides usando POST -->
<form action="buscarRide.php" method="post">
    <input type="hidden" name="desde_panel" value="1">
    <button type="submit">Buscar Rides</button>
</form>

<form action="../logica/cerrarSesion.php" method="post" style="display:inline;">
    <button type="submit" class="btn-cerrar">Cerrar</button>
</form>

<!-- Mensaje opcional -->
<?php if (isset($_SESSION['mensaje'])): ?>
    <?php 
        $mensaje = $_SESSION['mensaje']['texto'];
        $tipo = $_SESSION['mensaje']['tipo'];
        unset($_SESSION['mensaje']);
        $color = $tipo === 'success' ? 'green' : ($tipo === 'error' ? 'red' : 'blue');
    ?>
    <p style="color: <?= $color ?>; font-weight: bold;"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

</body>
</html>
