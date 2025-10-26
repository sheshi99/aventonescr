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
    <link rel="stylesheet" href="../estilos/estilosPanelChofer.css">
</head>
<body>
    <!-- Header con bienvenida -->
    <header class="chofer-header">
        <h2>Bienvenido, <?= htmlspecialchars($nombre_chofer) ?></h2>
    </header>

    <!-- Tarjeta principal con botones -->
    <div class="chofer-card">
        <div class="menu-chofer">
            <button onclick="location.href='gestionVehiculos.php'">Gestión de Vehículos</button>
            <button onclick="location.href='gestionRides.php'">Gestión de Rides</button>
        </div>
    </div>
</body>
</html>

