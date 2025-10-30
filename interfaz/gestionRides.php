<?php
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
    <link rel="stylesheet" href="../estilos/estilosPanelAdmin.css">
</head>
<body>
    <form action="../logica/cerrarSesion.php" method="post" style="display:inline;">
           <button type="submit" class="btn-cerrar">Cerrar</button>
    </form>
<div class="admin-main">
    <div class="tabla-usuarios">
        <h2>Mis Rides</h2>

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
                    <th>Costo</th>
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
                    <td><?= htmlspecialchars($ride['dia']) ?></td>
                    <td><?= htmlspecialchars($ride['hora']) ?></td>
                    <td><?= htmlspecialchars($ride['costo']) ?></td>
                    <td><?= htmlspecialchars($ride['espacios']) ?></td>
                    <td>
                        <form action="../interfaz/formularioRide.php" method="post" class="form-accion">
                            <input type="hidden" name="id_ride" value="<?= $ride['id_ride'] ?>">
                            <button type="submit" class="btn-activar">Editar</button>
                        </form>

                        <form action="../logica/procesarGestionRide.php" method="post" class="form-accion">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_ride" value="<?= $ride['id_ride'] ?>">
                            <button type="submit" class="btn-desactivar" onclick="return confirm('¿Seguro que desea eliminar este ride?')">Eliminar</button>
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
