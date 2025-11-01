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
    <link rel="stylesheet" href="../estilos/estilosPanelAdmin.css?v=2">
</head>
<body>
    <div class="admin-header">
        <div class="admin-header-left">
            <form action="registroUsuario.php" method="get" style="display:inline;">
                <input type="hidden" name="editar" value="1">
                <button type="submit" class="btn-editar"> ✏️ </button>
            </form>
            <h1>Bienvenido <?= $nombre ?> (<?= $rol ?>)</h1>
        </div>
        <div class="admin-header-right">
            <form action="choferPanel.php" method="get">
                <button type="submit" class="btn-panel">Ir al Panel</button>
            </form>
        </div>
    </div>


<div class="admin-main">
    <div class="tabla-usuarios">
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
                        <form action="../interfaz/registroVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-activar">Editar</button>
                        </form>

                        <form action="../logica/procesarGestionVehiculo.php" method="post" class="form-accion">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_vehiculo" value="<?= $vehiculo['id_vehiculo'] ?>">
                            <button type="submit" class="btn-desactivar" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <form action="../interfaz/registroVehiculo.php" method="post">
            <button type="submit" class="btn-nuevo">Agregar Vehículo</button>
        </form>
    </div>
</div>

</body>
</html>
