<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Vehículo</title>
    <link rel="stylesheet" href="../estilos/estilos.css">
</head>
<body>
<h2>Agregar Vehículo</h2>

<form action="procesarGestionVehiculos.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar_agregado">

    <label for="placa">Placa:</label>
    <input type="text" name="placa" id="placa" required><br><br>

    <label for="color">Color:</label>
    <input type="text" name="color" id="color" required><br><br>

    <label for="marca">Marca:</label>
    <input type="text" name="marca" id="marca" required><br><br>

    <label for="modelo">Modelo:</label>
    <input type="text" name="modelo" id="modelo" required><br><br>

    <label for="anno">Año:</label>
    <input type="number" name="anno" id="anno" min="1900" max="<?= date('Y') ?>" required><br><br>

    <label for="asientos">Asientos:</label>
    <input type="number" name="asientos" id="asientos" min="1" required><br><br>

    <label for="foto">Foto (opcional):</label>
    <input type="file" name="foto" id="foto" accept="image/*"><br><br>

    <button type="submit">Agregar Vehículo</button>
</form>

<form action="gestionVehiculos.php" method="post" style="margin-top:10px;">
    <button type="submit">Volver a la lista</button>
</form>
</body>
</html>
