<?php
session_start();
include_once("../datos/reservas.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$reservas = obtenerReservasPorUsuario($usuario['id_usuario'], $usuario['rol']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
</head>
<body>
    <form action="../logica/cerrarSesion.php" method="post" style="display:inline;">
        <button type="submit" class="btn-cerrar">Cerrar</button>
    </form>
<h2>Reservas de <?= htmlspecialchars($usuario['rol']) ?></h2>

<?php if (empty($reservas)): ?>
    <p>No hay reservas registradas.</p>
<?php else: ?>
    <table border="1">
        <tr><th>Salida</th><th>Llegada</th><th>Fecha</th><th>Estado</th></tr>
        <?php foreach ($reservas as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['salida']) ?></td>
                <td><?= htmlspecialchars($r['llegada']) ?></td>
                <td><?= htmlspecialchars($r['dia'] . ' ' . $r['hora']) ?></td>
                <td><?= htmlspecialchars($r['estado']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>

