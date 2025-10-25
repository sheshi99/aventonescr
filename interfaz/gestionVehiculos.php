<?php
session_start();
include_once("../datos/vehiculos.php"); // Tus funciones: insertarVehiculo, obtenerVehiculosPorChofer, etc.

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
$nombre_chofer = $_SESSION['usuario']['nombre'] ?? 'Chofer';

// Obtener vehículos del chofer
$vehiculos = obtenerVehiculosPorChofer($id_chofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vehículos</title>
    <link rel="stylesheet" href="../estilos/estilos.css">
</head>
<body>
    <h2>Gestión de Vehículos de <?= htmlspecialchars($nombre_chofer) ?></h2>

    <button onclick="location.href='agregarVehiculo.php'">Agregar Vehículo</button>

    <table border="1">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Asientos</th>
                <th>Foto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($vehiculos as $vehiculo): ?>
            <tr>
                <td><?= htmlspecialchars($vehiculo['numero_placa']) ?></td>
                <td><?= htmlspecialchars($vehiculo['color']) ?></td>
                <td><?= htmlspecialchars($vehiculo['marca']) ?></td>
                <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                <td><?= htmlspecialchars($vehiculo['anno']) ?></td>
                <td><?= htmlspecialchars($vehiculo['capacidad_asientos']) ?></td>
                <td>
                    <?php if($vehiculo['fotografia']): ?>
                        <img src="<?= htmlspecialchars($vehiculo['fotografia']) ?>" width="80">
                    <?php endif; ?>
                </td>
                <td>
                    <form style="display:inline;" action="procesarGestionVehiculos.php" method="post">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                        <button type="submit">Editar</button>
                    </form>

                    <form style="display:inline;" action="procesarGestionVehiculos.php" method="post" 
                          onsubmit="return confirm('¿Está seguro de eliminar este vehículo?');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
