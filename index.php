<?php


/*
 * Archivo: index.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción: 
 * Muestra los rides disponibles para los usuarios, permite filtrar por fecha,
 * salida y llegada, y reservar un ride si hay espacios disponibles. 
 * También gestiona mensajes de reserva y ordenamiento.
 */

session_start();

define('BASE_PATH', __DIR__ . '/');

include_once(BASE_PATH . 'datos/rides.php');


$usuario = $_SESSION['usuario'] ?? null;


$mensajeReserva = $_SESSION['mensaje_reserva'] ?? '';
unset($_SESSION['mensaje_reserva']);


$filtros = $_SESSION['filtros_orden'] ?? [
    'fecha' => '',
    'salida' => '',
    'llegada' => '',
    'direccion' => 'ASC'
];


if (!empty($_SESSION['rides_filtrados'])) {
    $rides = $_SESSION['rides_filtrados'];
} else {
    $fecha = $filtros['fecha'] ?? '';
    $salida = $filtros['salida'] ?? '';
    $llegada = $filtros['llegada'] ?? '';
    $direccion = $filtros['direccion'] ?? 'ASC';

    // Solo filtrar si el usuario realmente ingresó algo
    if ($fecha !== '' || $salida !== '' || $llegada !== '') {
        $rides = obtenerRidesFiltrados($fecha, $salida, $llegada, $direccion);
    } else {
        // Sin filtros: obtener todos los rides futuros
        $rides = consultarRides(); 
        $rides = filtrarEspaciosDisponibles($rides);
        $rides = ordenamientoRides($rides, 'dia', $direccion);
    }
}


unset($_SESSION['rides_filtrados']);
unset($_SESSION['filtros_orden']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagina Web</title>
    <link rel="stylesheet" href="../Estilos/estilosBuscarRide.css?v=3">
</head>
<body>

<header class="main-header">
    <div class="header-content">
        <?php if (!$usuario): ?>
            <div class="auth-buttons">
                <a href="/interfaz/Login.php" class="btn btn-login">Iniciar sesión</a>
                <a href="/interfaz/formularioUsuario.php?publico=1" class="btn btn-registrar">Registrarme</a>
            </div>
        <?php else: ?>
            <p class="usuario-nombre">👋 Hola, <?= htmlspecialchars($usuario['nombre'] ?? $usuario['rol']) ?></p>
            <div class="header-right">
                <?php if ($usuario['rol'] === 'Pasajero'): ?>
                    <form action="/interfaz/pasajeroPanel.php" method="get" style="display:inline;">
                        <button type="submit" class="btn btn-panel"> 🡸 </button>
                    </form>
                <?php endif; ?>
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
                <form action="/interfaz/buscarRide.php" method="get">
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
                <label for="campo_orden">Ordenar por:</label>
                <select name="campo_orden" id="campo_orden">
                    <option value="dia" <?= ($filtros['campo_orden'] ?? '') === 'dia' ? 'selected' : '' ?>>Fecha</option>
                    <option value="salida" <?= ($filtros['campo_orden'] ?? '') === 'salida' ? 'selected' : '' ?>>Lugar de salida</option>
                    <option value="llegada" <?= ($filtros['campo_orden'] ?? '') === 'llegada' ? 'selected' : '' ?>>Lugar de llegada</option>
                </select>
            </div>
            <div class="filtro-campo">
                <label for="orden_direccion">Dirección:</label>
                <select name="orden_direccion" id="orden_direccion">
                    <option value="ASC" <?= ($filtros['direccion'] ?? '') === 'ASC' ? 'selected' : '' ?>>Ascendente</option>
                    <option value="DESC" <?= ($filtros['direccion'] ?? '') === 'DESC' ? 'selected' : '' ?>>Descendente</option>
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