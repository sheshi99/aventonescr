<?php
session_start();
include_once("../datos/vehiculos.php");  
include_once("../datos/rides.php"); 
include_once("../utilidades/formulariosUtilidades.php");    

$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_chofer) {
    header("Location: ../interfaz/login.php");
    exit;
}

$preparacion = prepararFormularioRide();
$ride = $preparacion['ride'];
$accion = $preparacion['accion'];
$datosFormulario = $preparacion['datosFormulario'];
$mensaje = $preparacion['mensaje'];

$vehiculos = obtenerVehiculosPorChofer($id_chofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $accion==='actualizar' ? "Editar Ride" : "Registrar Ride" ?></title>
<link rel="stylesheet" href="../Estilos/estilosRegistro.css?v=2"> <!-- tu CSS existente -->
</head>
<body>

<div class="registro-container">
    <div class="form-card">
        <h2><?= $accion==='actualizar' ? "Editar Ride" : "Registrar Ride" ?></h2>

        <?php if($mensaje): ?>
        <p class="mensaje <?= $mensaje['tipo'] === 'error' ? 'error' : 'success' ?>">
            <?= $mensaje['texto'] ?>
        </p>
        <?php endif; ?>

        <form action="../logica/procesarRide.php" method="post">
            <input type="hidden" name="accion" value="<?= $accion ?>">
            <?php if($accion==='actualizar'): ?>
                <input type="hidden" name="id_ride" value="<?= valor('id_ride',$datosFormulario,$ride) ?>">
            <?php endif; ?>

            <div class="input-group">
                <label>Vehículo:</label>
                <select name="id_vehiculo" required>
                    <option value="">Seleccione un vehículo</option>
                    <?php foreach($vehiculos as $vehiculo): ?>
                        <option value="<?= $vehiculo['id_vehiculo'] ?>"
                            <?= valor('id_vehiculo',$datosFormulario,$ride) == $vehiculo['id_vehiculo'] ? 'selected' : '' ?>>
                            <?= $vehiculo['numero_placa'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required value="<?= valor('nombre',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Salida:</label>
                <input type="text" name="salida" required value="<?= valor('salida',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Llegada:</label>
                <input type="text" name="llegada" required value="<?= valor('llegada',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Día:</label>
                <input type="date" name="dia" value="<?= valor('dia', $datosFormulario, $ride) ?>">
            </div>

            <div class="input-group">
                <label>Hora:</label>
                <input type="time" name="hora" value="<?= valor('hora', $datosFormulario, $ride) ?>">
            </div>

            <div class="input-group">
                <label>Costo:</label>
                <input type="number" name="costo" step="0.01" required value="<?= valor('costo',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Espacios:</label>
                <input type="number" name="espacios" min="1" required value="<?= valor('espacios',$datosFormulario,$ride) ?>">
            </div>

            <button type="submit" class="btn-registrar"><?= $accion==='actualizar' ? "Actualizar Ride" : "Registrar Ride" ?></button>
        </form>
    </div>
</div>

</body>
</html>
