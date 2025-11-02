<?php
session_start();
include_once("../datos/rides.php");

// Usuario logueado (puede ser null)
$usuario = $_SESSION['usuario'] ?? null;

// Mensajes de reserva u ordenamiento
$mensajeReserva = $_SESSION['mensaje_reserva'] ?? '';
unset($_SESSION['mensaje_reserva']);

// Si hay filtros guardados en sesión, usarlos
$filtros = $_SESSION['filtros_orden'] ?? [
    'fecha' => '',
    'salida' => '',
    'llegada' => '',
    'direccion' => 'ASC'
];

// Obtener rides según filtros, si no hay filtros mostrar todos
if (!empty($_SESSION['rides_filtrados'])) {
    $rides = $_SESSION['rides_filtrados'];
} else {
    // Si no hay filtros aplicados, pasar todos los rides futuros
    $rides = buscarRides('', '', '', 'ASC'); // Sin filtros, solo orden por día ascendente
}

// Limpiar filtros y rides de sesión para que al refrescar se reinicien
unset($_SESSION['rides_filtrados']);
unset($_SESSION['filtros_orden']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rides Disponibles</title>
    <link rel="stylesheet" href="../Estilos/estilosBuscarRide.css?v=7">
</head>
<body>

<header class="main-header">
    <div class="header-content">
        <?php if (!$usuario): ?>
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-login">Iniciar sesión</a>
                <a href="formularioUsuario.php?publico=1" class="btn btn-registrar">Registrarse</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="buscar-container">

    <div class="buscar-card">
        <div class="card-header">
            <div>
                <h2>Rides Disponibles</h2>
                <p>Estos son los rides próximos que tienen espacios disponibles:</p>
            </div>
            <div class="boton-buscar-card">
                <form action="buscarRide.php" method="get">
                    <button type="submit" class="btn-buscar">Buscar Rides Por Ubicación</button>
                </form>
            </div>
        </div>

        <?php if ($mensajeReserva): ?>
            <div class="mensaje <?= htmlspecialchars($mensajeReserva['tipo']) ?>">
                <?= htmlspecialchars($mensajeReserva['texto']) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ===== Ordenamiento y filtrado ===== -->
    <div class="buscar-card">
        <form method="post" action="../logica/procesarIndexRide.php" class="ordenamiento-horizontal">
            <div class="filtro-campo">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?= htmlspecialchars($filtros['fecha']) ?>">
            </div>
            <div class="filtro-campo">
                <label for="salida">Lugar de salida:</label>
                <input type="text" id="salida" name="salida" placeholder="Origen" value="<?= htmlspecialchars($filtros['salida']) ?>">
            </div>
            <div class="filtro-campo">
                <label for="llegada">Lugar de llegada:</label>
                <input type="text" id="llegada" name="llegada" placeholder="Destino" value="<?= htmlspecialchars($filtros['llegada']) ?>">
            </div>
            <div class="filtro-campo">
                <label for="orden_direccion">Orden:</label>
                <select name="orden_direccion" id="orden_direccion">
                    <option value="ASC" <?= ($filtros['direccion'] === 'ASC') ? 'selected' : '' ?>>Ascendente</option>
                    <option value="DESC" <?= ($filtros['direccion'] === 'DESC') ? 'selected' : '' ?>>Descendente</option>
                </select>
            </div>
            <div class="filtro-campo boton-ordenar">
                <button type="submit" class="btn-ordenar">Ordenar</button>
            </div>
        </form>
    </div>

    <!-- ===== Tabla de rides ===== -->
    <?php if (!empty($rides)): ?>
        <div class="tabla-container">
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
                            <td><?= $r['espacios_disponibles'] > 0 ? $r['espacios_disponibles'] : 'Completo' ?></td>
                            <td>
                                <?php if ($r['espacios_disponibles'] > 0): ?>
                                    <form method="post" action="../logica/procesarIndexRide.php">
                                        <input type="hidden" name="id_ride" value="<?= $r['id_ride'] ?>">
                                        <button type="submit" class="btn-reservar">Reservar</button>
                                    </form>
                                <?php else: ?>
                                    ---
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="text-align:center; margin-top:10px;">No hay rides disponibles para mostrar.</p>
    <?php endif; ?>

</main>

</body>
</html>
