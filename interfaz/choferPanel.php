<?php
session_start();
include_once("../datos/usuarios.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
$nombre_chofer = $_SESSION['usuario']['nombre'] ?? 'Chofer';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Chofer</title>
    <link rel="stylesheet" href="../estilos/estilos.css">
</head>
<body>
    <h2>Bienvenido, <?= htmlspecialchars($nombre_chofer) ?></h2>

    <div class="menu-chofer">
        <button onclick="location.href='gestionVehiculos.php'">Gestión de Vehículos</button>
        <button onclick="location.href='gestionRides.php'">Gestión de Rides</button>
    </div>
</body>
</html>
