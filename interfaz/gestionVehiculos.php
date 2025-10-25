<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
$vehiculos = obtenerVehiculosPorChofer($id_chofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vehículos</title>
</head>
<body>
<h2>Mis Vehículos</h2>

<?php if(!empty($_SESSION['mensaje'])): ?>
    <p style="color: <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'red' : 'green' ?>">
        <?= $_SESSION['mensaje']['texto'] ?>
    </p>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<table border="1" cellpadding="5">
    <tr>
        <th>Placa</th>
        <th>Color</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Año</th>
        <th>Asientos</th>
        <th>Acciones</th>
    </tr>
    <?php foreach($vehiculos as $vehiculo): ?>
    <tr>
        <td><?= htmlspecialchars($vehiculo['numero_placa']) ?></td>
        <td><?= htmlspecialchars($vehiculo['color']) ?></td>
        <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
        <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
        <td><?= htmlspecialchars($vehiculo['anno']) ?></td>
        <td><?= htmlspecialchars($vehiculo['capacidad_asientos']) ?></td>
        <td>
            <!-- Editar -->
            <form action="../interfaz/registroVehiculo.php" method="post" style="display:inline">
                <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                <button type="submit">Editar</button>
            </form>

            <!-- Eliminar -->
            <form action="../logica/procesarGestionVehiculo.php" method="post" style="display:inline">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                <button type="submit" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<br>
<form action="../interfaz/registroVehiculo.php" method="post">
    <button type="submit">Agregar Vehículo</button>
</form>

</body>
</html>
