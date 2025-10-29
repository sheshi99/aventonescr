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
    <!-- Enlace a tu hoja de estilos -->
    <link rel="stylesheet" href="../Estilos/estilosRegistroUsuario.css">
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
                       value="<?= htmlspecialchars($datosFormulario['placa'] ?? $vehiculo['placa']) ?>" required>
            </div>

            <div class="input-group">
                <label for="color">Color:</label>
                <input type="text" name="color" id="color"
                       value="<?= htmlspecialchars($datosFormulario['color'] ?? $vehiculo['color']) ?>" required>
            </div>

            <div class="input-group">
                <label for="marca">Marca:</label>
                <input type="text" name="marca" id="marca"
                       value="<?= htmlspecialchars($datosFormulario['marca'] ?? $vehiculo['marca']) ?>" required>
            </div>

            <div class="input-group">
                <label for="modelo">Modelo:</label>
                <input type="text" name="modelo" id="modelo"
                       value="<?= htmlspecialchars($datosFormulario['modelo'] ?? $vehiculo['modelo']) ?>" required>
            </div>

            <div class="input-group">
              <label for="anno">Año:</label>
              <input type="number" name="anno" id="anno" min="1900" max="<?= date('Y') ?>"
                     value="<?= htmlspecialchars($datosFormulario['anno'] ?? $vehiculo['anno']) ?>" required>
            </div>

            <div class="input-group">
              <label for="asientos">Asientos:</label>
              <input type="number" name="asientos" id="asientos" min="1"
                     value="<?= htmlspecialchars($datosFormulario['asientos'] ?? $vehiculo['asientos']) ?>" required>
            </div>


            <div class="input-group">
                <label for="foto">Foto:</label>
                <input type="file" name="fotografia" id="foto" accept="image/*" <?= $accion === 'actualizar' ? '' : 'required' ?>>
            </div>

            <button type="submit" class="btn-registrar">
                <?= $accion === 'actualizar' ? "Actualizar Vehículo" : "Registrar Vehículo" ?>
            </button>
        </form>
    </div>
</div>

</body>
</html>
