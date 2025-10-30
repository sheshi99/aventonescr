<?php
session_start();

$mensaje = $_SESSION['mensaje']['texto'] ?? '';
$tipo = $_SESSION['mensaje']['tipo'] ?? '';
$rides = $_SESSION['rides'] ?? [];
$salida = $_SESSION['filtros']['salida'] ?? '';
$llegada = $_SESSION['filtros']['llegada'] ?? '';

unset($_SESSION['mensaje'], $_SESSION['rides'], $_SESSION['filtros']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Rides</title>
</head>
<body>

<h2>Buscar Rides</h2>

<?php if ($mensaje): ?>
<p><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<form method="post" action="../logica/procesarBusquedaRide.php">
    <label>Salida:</label>
    <input type="text" name="salida" value="<?= htmlspecialchars($salida) ?>">

    <label>Llegada:</label>
    <input type="text" name="llegada" value="<?= htmlspecialchars($llegada) ?>">

    <button type="submit">Buscar</button>
</form>

<?php if (!empty($rides)): ?>
    <h3>Rides disponibles</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Nombre</th>
            <th>Salida</th>
            <th>Llegada</th>
            <th>Día</th>
            <th>Hora</th>
            <th>Vehículo</th>
            <th>Costo</th>
            <th>Espacios</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($rides as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['nombre']) ?></td>
                <td><?= htmlspecialchars($r['salida']) ?></td>
                <td><?= htmlspecialchars($r['llegada']) ?></td>
                <td><?= htmlspecialchars($r['dia']) ?></td>
                <td><?= htmlspecialchars($r['hora']) ?></td>
                <td><?= htmlspecialchars($r['marca'] . ' ' . $r['modelo'] . ' (' . $r['anno'] . ')') ?></td>
                <td><?= htmlspecialchars($r['costo']) ?></td>
                <td><?= htmlspecialchars($r['espacios']) ?></td>
                <td>
                    <form method="post" action="../logica/procesarReserva.php">
                        <input type="hidden" name="accion" value="reservar">
                        <input type="hidden" name="id_ride" value="<?= $r['id_ride'] ?>">
                        <button type="submit">Reservar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php elseif ($salida !== '' || $llegada !== ''): ?>
    <p>No se encontraron rides.</p>
<?php endif; ?>

</body>
</html>
