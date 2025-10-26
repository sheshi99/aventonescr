<?php
include_once("../logica/prepararVehiculoEdicion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></title>
</head>
<body>
<h2><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></h2>

<?php if (!empty($mensajeTexto)): ?>
    <p style="color: <?= ($mensajeTipo === 'error') ? 'red' : 'green' ?>">
        <?= htmlspecialchars($mensajeTexto) ?>
    </p>
<?php endif; ?>

<form action="../logica/procesarVehiculo.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="<?= htmlspecialchars($accion) ?>">
    <?php if ($accion === 'actualizar'): ?>
        <input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars($id_vehiculo) ?>">
    <?php endif; ?>

    <label for="placa">Placa:</label>
    <input type="text" name="placa" id="placa"
           value="<?= valorCampo('placa', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="color">Color:</label>
    <input type="text" name="color" id="color"
           value="<?= valorCampo('color', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="marca">Marca:</label>
    <input type="text" name="marca" id="marca"
           value="<?= valorCampo('marca', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="modelo">Modelo:</label>
    <input type="text" name="modelo" id="modelo"
           value="<?= valorCampo('modelo', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="anno">Año:</label>
    <input type="number" name="anno" id="anno" min="1900" max="<?= date('Y') ?>"
           value="<?= valorCampo('anno', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="asientos">Asientos:</label>
    <input type="number" name="asientos" id="asientos" min="1"
           value="<?= valorCampo('asientos', $vehiculo, $valores, $campoError) ?>" required><br><br>

    <label for="foto">Foto:</label>
    <input type="file" name="foto" id="foto" accept="image/*" required><br><br>

    <button type="submit"><?= $accion === 'actualizar' ? "Actualizar Vehículo" : "Registrar Vehículo" ?></button>
</form>
</body>
</html>
