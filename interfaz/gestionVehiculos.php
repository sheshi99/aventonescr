<?php
session_start();
include_once("../datos/vehiculos.php");

// Verifica sesión
if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
$vehiculos = obtenerVehiculosPorChofer($id_chofer);

// Datos del usuario para encabezado
$usuario = $_SESSION['usuario'];
$nombre = htmlspecialchars($usuario['nombre']);
$rol = htmlspecialchars($usuario['rol']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vehículos</title>
    <link rel="stylesheet" href="../estilos/estilosTablas.css?v=2">
</head>
<body>
    <div class="gestion-header">
        <div class="gestion-header-left">
            <h1>Gestión de Vehiculos</h1>
        </div>
        <div class="gestion-header-right">
            <form action="choferPanel.php" method="get">
                <button type="submit" class="btn-panel">Ir al Panel</button>
            </form>
        </div>
    </div>


<div class="gestion-main">
    <div class="tabla-gestion">
        <h2>Mis Vehículos</h2>

        <?php if(!empty($_SESSION['mensaje'])): ?>
            <p style="color: <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'red' : 'green' ?>; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
            </p>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Color</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Asientos</th>
                    <th>Fotografía</th>
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
                        <?php if (!empty($vehiculo['fotografia'])): ?>
                            <img src="../logica/<?= htmlspecialchars($vehiculo['fotografia']); ?>"
                                alt="<?= htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']); ?>"
                                class="foto-vehiculo">
                        <?php else: ?>
                            <span>No hay foto</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="../interfaz/formularioVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-editar">Editar</button>
                        </form>

                        <form action="../logica/procesarVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-eliminar" 
                            onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <form action="../interfaz/formularioVehiculo.php" method="post">
            <button type="submit" class="btn-nuevo">Agregar Vehículo</button>
        </form>
    </div>
</div>

</body>
</html>
