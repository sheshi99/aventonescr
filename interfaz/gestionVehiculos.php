<?php

/*
 * --------------------------------------------------------------
 * Archivo: gestionVehiculos.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Es la interfaz de gestión de vehículos para choferes, que muestra todos los vehículos
 * registrados por el chofer, permite editarlos o eliminarlos, y ofrece
 * un botón para agregar un nuevo vehículo. También muestra mensajes de éxito o 
 * error según las acciones realizadas.
 * --------------------------------------------------------------
 */

session_start();

define('BASE_PATH', __DIR__ . '/');

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
    <div class="header">
        <div class="header-left">
            <h1>Gestión de Vehiculos</h1>
        </div>
        <div class="header-right">
            <form action="choferPanel.php" method="get">
                <button type="submit" class="btn-panel"> 🡸 </button>
            </form>
        </div>
    </div>


<div class="main">
    <div class="tabla">
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
                                class="foto">
                        <?php else: ?>
                            <span>No hay foto</span>
                        <?php endif; ?>
                    </td>
                    <td>
                    <?php if (!vehiculoTieneRides($vehiculo['id_vehiculo'])): ?>
                        <form action="../interfaz/formularioVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-verde">Editar</button>
                        </form>
                    <?php endif; ?>

                        <form action="../logica/procesarVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-rojo" 
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
