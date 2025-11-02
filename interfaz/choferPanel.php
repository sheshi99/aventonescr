<?php
session_start();
include_once("../datos/usuarios.php");

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
    <header class="chofer-header">
        <form action="registroUsuario.php" method="POST">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario']['id_usuario'] ?>">
            <button type="submit" class="btn-editarPasajero">✏️</button>
        </form>

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
    <div class="chofer-card">
        <div class="menu-chofer">
            <button onclick="location.href='gestionVehiculos.php'">Gestión de Vehículos</button>
            <button onclick="location.href='gestionRides.php'">Gestión de Rides</button>
            <button onclick="location.href='misReservas.php'">Mis Reservas</button>
        </div>
    </div>
</body>
</html>

