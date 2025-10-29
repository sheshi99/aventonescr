<?php
session_start();
include_once("../datos/vehiculos.php");
include_once("../logica/prepararRide.php"); // Prepara $ride y $accion

// Verificar sesión
$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_chofer) {
    header("Location: ../interfaz/login.php");
    exit;
}

// Obtener vehículos del chofer
$vehiculos = obtenerVehiculosPorChofer($id_chofer);

// Mensajes y datos previos
$datosFormulario = $_SESSION['datos_formulario'] ?? [];
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

// Preparar opciones de vehículos
$vehiculosAsociados = '';
foreach ($vehiculos as $vehiculo) {
    $selected = (!empty($ride['id_vehiculo']) && $ride['id_vehiculo'] == $vehiculo['id_vehiculo']) ? 'selected' : '';
    $vehiculosAsociados .= "<option value='{$vehiculo['id_vehiculo']}' $selected>{$vehiculo['numero_placa']}</option>";
}

// Preparar opciones de días
$dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
$diasOptions = '';
foreach ($dias as $d) {
    $selected = (!empty($ride['dia']) && $ride['dia'] == $d) ? 'selected' : '';
    $diasOptions .= "<option value='$d' $selected>$d</option>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion === 'actualizar' ? "Editar Ride" : "Registrar Ride" ?></title>
</head>
<body>
<h2><?= $accion === 'actualizar' ? "Editar Ride" : "Registrar Ride" ?></h2>

<?php if ($mensaje): ?>
    <p style="color: <?= $mensaje['tipo'] === 'error' ? 'red' : 'green' ?>">
        <?= htmlspecialchars($mensaje['texto']) ?>
    </p>
<?php endif; ?>

<form action="../logica/procesarRide.php" method="post">
    <input type="hidden" name="accion" value="<?= $accion ?>">
    <?php if($accion === 'actualizar'): ?>
        <input type="hidden" name="id_ride" value="<?= $ride['id_ride'] ?>">
    <?php endif; ?>

    <!-- Vehículos -->
    <label>Vehículo:</label>
    <select name="id_vehiculo" required>
        <option value="">Seleccione un vehículo</option>
        <?= $vehiculosAsociados ?>
    </select><br><br>

    <!-- Nombre del Ride -->
    <label>Nombre del Ride:</label>
    <input type="text" name="nombre" required
           value="<?= htmlspecialchars($datosFormulario['nombre'] ?? $ride['nombre'] ?? '') ?>"><br><br>

    <!-- Lugar de salida -->
    <label>Lugar de salida:</label>
    <input type="text" name="salida" required
           value="<?= htmlspecialchars($datosFormulario['salida'] ?? $ride['salida'] ?? '') ?>"><br><br>

    <!-- Lugar de llegada -->
    <label>Lugar de llegada:</label>
    <input type="text" name="llegada" required
           value="<?= htmlspecialchars($datosFormulario['llegada'] ?? $ride['llegada'] ?? '') ?>"><br><br>

    <!-- Día -->
    <label>Día:</label>
    <select name="dia" required>
        <?= $diasOptions ?>
    </select><br><br>

    <!-- Hora -->
    <label>Hora:</label>
    <input type="time" name="hora" required
           value="<?= htmlspecialchars($datosFormulario['hora'] ?? $ride['hora'] ?? '') ?>"><br><br>

    <!-- Costo por espacio -->
    <label>Costo por espacio:</label>
    <input type="number" name="costo" step="0.01" required
           value="<?= htmlspecialchars($datosFormulario['costo'] ?? $ride['costo'] ?? '') ?>"><br><br>

    <!-- Cantidad de espacios -->
    <label>Cantidad de espacios:</label>
    <input type="number" name="espacios" min="1" required
           value="<?= htmlspecialchars($datosFormulario['espacios'] ?? $ride['espacios'] ?? '') ?>"><br><br>

    <button type="submit"><?= $accion === 'actualizar' ? "Actualizar Ride" : "Registrar Ride" ?></button>
</form>
</body>
</html>


