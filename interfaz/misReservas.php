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
    <link rel="stylesheet" href="../Estilos/estilosPanelAdmin.css?v=2">
</head>
<body>

    <!-- ===== ENCABEZADO ===== -->
    <header class="admin-header">
        <div class="admin-header-left">
            <h1>Mis Reservas</h1>
        </div>

        <div class="admin-user">
            <?php
            $destinoPanel = ($usuario['rol'] === 'Chofer')
                ? '../interfaz/choferPanel.php'
                : '../interfaz/pasajeroPanel.php';
            ?>
            <form action="<?= $destinoPanel ?>" method="get" style="display:inline;">
                <button type="submit" class="btn-panel">Ir al Panel</button>
            </form>
        </div>
    </header>

    <!-- ===== CONTENIDO PRINCIPAL ===== -->
    <main class="admin-main">
        <section class="tabla-usuarios">

            <!-- ===== RESERVAS ACTIVAS ===== -->
            <h2>Reservas Activas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas['activas'] as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['salida']) ?></td>
                        <td><?= htmlspecialchars($r['llegada']) ?></td>
                        <td><?= htmlspecialchars($r['dia']) ?></td>
                        <td><?= htmlspecialchars($r['hora']) ?></td>
                        <td class="estado <?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></td>
                        <td>
                            <?php if ($usuario['rol'] === 'Chofer' && $r['estado'] === 'Pendiente'): ?>
                                <form action="../logica/procesarAccionReserva.php" method="post">
                                    <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                    <button type="submit" name="accion" value="aceptar" class="btn-activar">Aceptar</button>
                                    <button type="submit" name="accion" value="rechazar" class="btn-desactivar">Rechazar</button>
                                </form>
                            <?php elseif ($usuario['rol'] === 'Pasajero' && in_array($r['estado'], ['Pendiente','Aceptada'])): ?>
                                <form action="../logica/procesarAccionReserva.php" method="post">
                                    <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                    <button type="submit" name="accion" value="cancelar" class="btn-desactivar">Cancelar</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- ===== RESERVAS PASADAS ===== -->
            <h2>Reservas Pasadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas['pasadas'] as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['salida']) ?></td>
                        <td><?= htmlspecialchars($r['llegada']) ?></td>
                        <td><?= htmlspecialchars($r['dia']) ?></td>
                        <td><?= htmlspecialchars($r['hora']) ?></td>
                        <td class="estado <?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
