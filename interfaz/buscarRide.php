<?php
session_start();
include_once("../datos/rides.php");

// Recuperar mensajes y filtros
$mensaje = $_SESSION['mensaje']['texto'] ?? '';
$tipo = $_SESSION['mensaje']['tipo'] ?? '';
$rides = $_SESSION['rides'] ?? [];
$salida = $_SESSION['filtros']['salida'] ?? '';
$llegada = $_SESSION['filtros']['llegada'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['rides'], $_SESSION['filtros']);

// Obtener usuario logueado (puede ser null)
$usuario = $_SESSION['usuario'] ?? null;

// Mensaje de reserva
$mensajeReserva = $_SESSION['mensaje_reserva'] ?? '';
unset($_SESSION['mensaje_reserva']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Rides</title>
    <link rel="stylesheet" href="../Estilos/estilosBuscarRide.css">
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="header-content">
            <?php if (!$usuario): ?>
                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-login">Iniciar sesión</a>
                    <a href="registro.php" class="btn btn-registrar">Registrarse</a>
                </div>
            <?php else: ?>
                <p class="usuario-nombre">👋 Hola, <?= htmlspecialchars($usuario['nombre'] ?? $usuario['rol']) ?></p>
                <div class="header-right">
                    <?php if ($usuario['rol'] === 'Pasajero'): ?>
                        <!-- 🔹 Convertido en form con botón -->
                        <form action="pasajeroPanel.php" method="get" style="display:inline;">
                            <button type="submit" class="btn btn-panel">Ir al Panel</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- ===== CONTENEDOR PRINCIPAL ===== -->
    <main class="buscar-container">
        <div class="buscar-card">
            <h2>Buscar Rides</h2>

            <?php if ($mensaje): ?>
                <p class="mensaje <?= $tipo ?>"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>

            <?php if ($mensajeReserva): ?>
                <p class="mensaje <?= $mensajeReserva['tipo'] ?>"><?= htmlspecialchars($mensajeReserva['texto']) ?></p>
            <?php endif; ?>

            <form method="post" action="../logica/procesarBusquedaRide.php" class="form-busqueda">
                <div class="input-group">
                    <label>Salida:</label>
                    <input type="text" name="salida" value="<?= htmlspecialchars($salida) ?>" placeholder="Ej: San José">
                </div>
                <div class="input-group">
                    <label>Llegada:</label>
                    <input type="text" name="llegada" value="<?= htmlspecialchars($llegada) ?>" placeholder="Ej: Cartago">
                </div>
                <button type="submit" class="btn-buscar">Buscar</button>
            </form>
        </div>

        <?php if (!empty($rides)): ?>
            <div class="tabla-container">
                <h3>Rides disponibles</h3>
                <table>
                    <thead>
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
                    </thead>
                    <tbody>
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
                                        <input type="hidden" name="id_ride" value="<?= $r['id_ride'] ?>">
                                        <button type="submit" class="btn-reservar">Reservar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
