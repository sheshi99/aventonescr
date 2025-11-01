<?php
session_start();
include_once("../utilidades/formulariosUtilidades.php"); 

// Preparar datos del formulario y mensaje
$formulario = prepararFormularioVehiculo();
$vehiculo = $formulario['vehiculo'];
$accion = $formulario['accion'];
$datosFormulario = $formulario['datosFormulario'];
$mensaje = $formulario['mensaje'];
$id_vehiculo = $_POST['id_vehiculo'] ?? $vehiculo['id_vehiculo'] ?? null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></title>
    <link rel="stylesheet" href="../Estilos/estilosRegistro.css?v=2">
</head>
<body>

<div class="registro-container">
    <div class="form-card">
        <h2><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></h2>

        <?php if ($mensaje): ?>
            <p class="mensaje <?= $mensaje['tipo'] === 'error' ? 'error' : 'exito' ?>">
                <?= htmlspecialchars($mensaje['texto']) ?>
            </p>
        <?php endif; ?>

        <form action="../logica/procesarVehiculo.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="<?= $accion ?>">
            <?php if ($accion === 'actualizar'): ?>
                <input type="hidden" name="id_vehiculo" value="<?= $id_vehiculo ?>">
            <?php endif; ?>

            <div class="input-group">
                <label for="placa">Placa:</label>
                <input type="text" name="placa" id="placa"
                       value="<?= valorVehiculo('placa', $datosFormulario, $vehiculo) ?>" required>
            </div>

            <div class="input-group">
                <label for="color">Color:</label>
                <input type="text" name="color" id="color"
                       value="<?= valorVehiculo('color', $datosFormulario, $vehiculo) ?>" required>
            </div>

            <div class="input-group">
                <label for="marca">Marca:</label>
                <input type="text" name="marca" id="marca"
                       value="<?= valorVehiculo('marca', $datosFormulario, $vehiculo) ?>" required>
            </div>

            <div class="input-group">
                <label for="modelo">Modelo:</label>
                <input type="text" name="modelo" id="modelo"
                       value="<?= valorVehiculo('modelo', $datosFormulario, $vehiculo) ?>" required>
            </div>

            <div class="input-group">
                <label for="anno">Año:</label>
                <input type="number" name="anno" id="anno" min="1900" max="<?= date('Y') ?>"
                       value="<?= valorVehiculo('anno', $datosFormulario, $vehiculo) ?>" required>
            </div>

            <div class="input-group">
                <label for="asientos">Asientos:</label>
                <input type="number" name="asientos" id="asientos" min="1"
                       value="<?= valorVehiculo('asientos', $datosFormulario, $vehiculo) ?>" required>
            </div>
     
            <div class="input-group">
                <label for="foto">Foto:</label>
                <input type="file" name="fotografia" id="foto" accept="image/*" <?= $accion === 'actualizar' ? '' : 'required' ?>>
            </div>

            <button type="submit" class="btn-registrar">
                <?= $accion === 'actualizar' ? "Actualizar Vehículo" : "Registrar Vehículo" ?>
            </button>


        </form>

        
            <form action="choferPanel.php" method="get" style="display:inline;">
                <button type="submit" class="btn-salir">Salir</button>
            </form>
    </div>
</div>

</body>
</html>
