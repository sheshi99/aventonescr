<?php
include_once("../logica/prepararVehiculoEdicion.php");

// Obtener mensaje y datos del formulario si existe
$datosFormulario = $_SESSION['datos_formulario'] ?? [];
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></title>
</head>
<body>
<h2><?= $accion === 'actualizar' ? "Editar Vehículo" : "Registrar Vehículo" ?></h2>

<?php if ($mensaje): ?>
    <p style="color: <?= $mensaje['tipo'] === 'error' ? 'red' : 'green' ?>">
        <?= htmlspecialchars($mensaje['texto']) ?>
    </p>
<?php endif; ?>

<form action="../logica/procesarVehiculo.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="<?= $accion ?>">
    <?php if($accion === 'actualizar'): ?>
        <input type="hidden" name="id_vehiculo" value="<?= $id_vehiculo ?>">
    <?php endif; ?>

    <label for="placa">Placa:</label>
    <input type="text" name="placa" id="placa" 
           value="<?= htmlspecialchars($datosFormulario['placa'] ?? $vehiculo['placa']) ?>" required><br><br>

    <label for="color">Color:</label>
    <input type="text" name="color" id="color" 
           value="<?= htmlspecialchars($datosFormulario['color'] ?? $vehiculo['color']) ?>" required><br><br>

    <label for="marca">Marca:</label>
    <input type="text" name="marca" id="marca" 
           value="<?= htmlspecialchars($datosFormulario['marca'] ?? $vehiculo['marca']) ?>" required><br><br>

    <label for="modelo">Modelo:</label>
    <input type="text" name="modelo" id="modelo" 
           value="<?= htmlspecialchars($datosFormulario['modelo'] ?? $vehiculo['modelo']) ?>" required><br><br>

    <label for="anno">Año:</label>
    <input type="number" name="anno" id="anno" min="1900" max="<?= date('Y') ?>" 
           value="<?= htmlspecialchars($datosFormulario['anno'] ?? $vehiculo['anno']) ?>" required><br><br>

    <label for="asientos">Asientos:</label>
    <input type="number" name="asientos" id="asientos" min="1" 
           value="<?= htmlspecialchars($datosFormulario['asientos'] ?? $vehiculo['asientos']) ?>" required><br><br>

    <label for="foto">Foto:</label>
    
    <input type="file" name="foto" id="foto" accept="image/*" required><br><br>

    <button type="submit"><?= $accion === 'actualizar' ? "Actualizar Vehículo" : "Registrar Vehículo" ?></button>
</form>
</body>
</html>

