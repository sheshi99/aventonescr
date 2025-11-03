<?php
/*
 * --------------------------------------------------------------
 * Archivo: misReservas.php
 * Autores: Seidy Alanis y Walbyn González
 * 
 * Descripción:
 * Muestra al usuario sus reservas activas y pasadas en tablas, permite cancelar 
 * reservas pendientes o aceptadas (si es pasajero) o aceptar/rechazar reservas 
 * pendientes (si es chofer), y tiene un botón para volver al panel correspondiente 
 * según el rol del usuario.
 * --------------------------------------------------------------
 */
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
    <link rel="stylesheet" href="../Estilos/estilosTablas.css?v=2">
</head>
<body>

    <header class="header">
        <div class="header-left">
            <h1>Mis Reservas</h1>
        </div>

        <div class="user-info">
            <?php
            $destinoPanel = ($usuario['rol'] === 'Chofer')
                ? '../interfaz/choferPanel.php'
                : '../interfaz/pasajeroPanel.php';
            ?>
            <form action="<?= $destinoPanel ?>" method="get" style="display:inline;">
                <button type="submit" class="btn-panel"> 🡸 </button>
            </form>
        </div>
    </header>

    <main class="main">
        <section class="tabla">

            <h2>Reservas Activas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Vehículo</th>
                        <th><?= ($usuario['rol'] === 'Chofer') ? 'Pasajero' : 'Chofer' ?></th>
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
                        <td><?= htmlspecialchars($r['numero_placa']) ?> (<?= htmlspecialchars($r['marca']) ?> <?= htmlspecialchars($r['modelo']) ?> <?= htmlspecialchars($r['anno']) ?>)</td>
                        <td>
                            <?php if ($usuario['rol'] === 'Chofer'): ?>
                                <?= htmlspecialchars($r['pasajero_nombre'] . ' ' . $r['pasajero_apellido']) ?>
                            <?php else: ?>
                                <?= htmlspecialchars($r['chofer_nombre'] . ' ' . $r['chofer_apellido']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="estado <?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></td>
                        <td>
                            <?php if ($usuario['rol'] === 'Chofer' && $r['estado'] === 'Pendiente'): ?>
                                <form action="../logica/procesarAccionReserva.php" method="post">
                                    <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                    <button type="submit" name="accion" value="aceptar" class="btn-verde">Aceptar</button>
                                    <button type="submit" name="accion" value="rechazar" class="btn-rojo">Rechazar</button>
                                </form>
                            <?php elseif ($usuario['rol'] === 'Pasajero' && in_array($r['estado'], ['Pendiente','Aceptada'])): ?>
                                <form action="../logica/procesarAccionReserva.php" method="post">
                                    <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                    <button type="submit" name="accion" value="cancelar" class="btn-rojo">Cancelar</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Reservas Pasadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Salida</th>
                        <th>Llegada</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Vehículo</th>
                        <th><?= ($usuario['rol'] === 'Chofer') ? 'Pasajero' : 'Chofer' ?></th>
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

                        <td><?= htmlspecialchars($r['numero_placa']) ?> (<?= htmlspecialchars($r['marca']) ?> <?= htmlspecialchars($r['modelo']) ?> <?= htmlspecialchars($r['anno']) ?>)</td>
                        <td>
                            <?php if ($usuario['rol'] === 'Chofer'): ?>
                                <?= htmlspecialchars($r['pasajero_nombre'] . ' ' . $r['pasajero_apellido']) ?>
                            <?php else: ?>
                                <?= htmlspecialchars($r['chofer_nombre'] . ' ' . $r['chofer_apellido']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="estado <?= strtolower($r['estado']) ?>"><?= htmlspecialchars($r['estado']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
