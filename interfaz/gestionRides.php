<?php

/*
 * --------------------------------------------------------------
 * Archivo: gestionRides.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Es la interfaz de gestión de rides para choferes, que muestra todos 
 * los rides registrados por el chofer.
 * Permite editar, eliminar rides existentes y ofrece un botón para agregar
 * un nuevo ride, mostrando además mensajes de éxito o error según las acciones 
 * realizadas.
 * --------------------------------------------------------------
 */

session_start();
include_once("../datos/rides.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];
$rides = obtenerRidesPorChofer($id_chofer); // Debes tener esta función en datos/rides.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Rides</title>
    <link rel="stylesheet" href="../estilos/estilosTablas.css?v=2">
</head>
<body>
   
    <div class="header">
        <div class="header-left">
            <h1>Gestión Rides</h1>
        </div>
        <div class="header-right">
            <form action="choferPanel.php" method="get">
                <button type="submit" class="btn-panel"> 🡸 </button>
            </form>   
        </div>
    </div>


    <div class="main">
        <div class="tabla">

            <?php if(!empty($_SESSION['mensaje'])): ?>
                <p style="color: <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'red' : 'green' ?>;">
                    <?= $_SESSION['mensaje']['texto'] ?>
                </p>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>
            

            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Vehículo</th>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Día</th>
                        <th>Hora</th>
                        <th>Costo Espacio</th>
                        <th>Espacios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($rides as $ride): ?>
                <tr>
                    <td><?= htmlspecialchars($ride['nombre']) ?></td>
                    <td><?= htmlspecialchars($ride['placa_vehiculo'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($ride['salida']) ?></td>
                    <td><?= htmlspecialchars($ride['llegada']) ?></td>
                    <td><?= !empty($ride['dia']) ? date('d/m/Y', strtotime($ride['dia'])) : '' ?></td>
                    <td><?= !empty($ride['hora']) ? date('H:i', strtotime($ride['hora'])) : '' ?></td>
                    <td><?= htmlspecialchars($ride['costo']) ?></td>
                    <td><?= htmlspecialchars($ride['espacios']) ?></td>
                    <td>
                    <?php if (!rideTieneReservasRealizadas($ride['id_ride'])): ?>
                        <form action="../interfaz/formularioRide.php" method="post" class="form-accion">
                            <input type="hidden" name="id_ride" value="<?= $ride['id_ride'] ?>">
                            <button type="submit" class="btn-verde">Editar</button>
                        </form>
                    <?php endif; ?>

                        <!-- Botón eliminar -->
                        <form action="../logica/procesarRide.php" method="post" class="form-accion">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_ride" value="<?= $ride['id_ride'] ?>">
                            <button type="submit" class="btn-rojo" 
                            onclick="return confirm('¿Seguro que desea eliminar este ride?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

            <br>
            <form action="formularioRide.php" method="post">
                <button type="submit" class="btn-nuevo">Agregar Ride</button>
            </form>
        </div>
    </div>
</body>
</html>
