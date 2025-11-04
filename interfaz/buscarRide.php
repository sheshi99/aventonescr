<?php

/*
 * --------------------------------------------------------------
 * Archivo: buscarRide.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Este archivo muestra un formulario para que el usuario busque rides por salida y llegada,
 * luego lista los rides disponibles en una tabla con detalles (nombre, vehículo, día, hora,
 * costo, espacios) y permite reservarlos. Además muestra mensajes de éxito
 * o error y tiene un botón para regresar al panel del usuario o salir si es público.
 * --------------------------------------------------------------
 */

session_start();

// Recuperar mensajes y filtros
$mensaje = $_SESSION['mensaje']['texto'] ?? '';
$tipo = $_SESSION['mensaje']['tipo'] ?? '';
$rides = $_SESSION['rides'] ?? [];
$salida = $_SESSION['filtros']['salida'] ?? '';
$llegada = $_SESSION['filtros']['llegada'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['rides'], $_SESSION['filtros']);

// Usuario logueado (puede ser null si es público)
$usuario = $_SESSION['usuario'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Rides</title>
    <link rel="stylesheet" href="../Estilos/estilosBuscarRide.css?v=5">
</head>
<body>

    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="header-content">
            <?php if (!$usuario): ?>
                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-login">Iniciar sesión</a>
                    <a href="formularioUsuario.php" class="btn btn-registrar">Registrarse</a>
                </div>
            <?php else: ?>
                <p class="usuario-nombre">👋 Hola, <?= htmlspecialchars($usuario['nombre'] ?? $usuario['rol']) ?></p>
                <div class="header-right">
                    <?php if ($usuario['rol'] === 'Pasajero'): ?>
                        <form action="../index.php" method="get" style="display:inline;">
                            <button type="submit" class="btn btn-panel"> 🡸 </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- ===== CONTENEDOR PRINCIPAL ===== -->
    <main class="buscar-container">
        <div class="buscar-card">

            <?php if (!$usuario): ?>
                <!-- Botón X en esquina superior derecha dentro del card -->
                <form action="../index.php" method="get" class="form-salir">
                    <button type="submit" class="btn-cerrar-x" title="Salir">✖</button>
                </form>
            <?php endif; ?>

            <h2>Buscar Rides</h2>

            <?php if(!empty($_SESSION['mensaje_esperado'])): ?>
                <p style="color: <?= $_SESSION['mensaje_esperado']['tipo'] === 'error' ? 'red' : 'green' ?>; font-weight: bold;">
                    <?= htmlspecialchars($_SESSION['mensaje_esperado']['texto']) ?>
                </p>
                <?php unset($_SESSION['mensaje_esperado']); ?>
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
                            <th>Espacios disponibles</th>
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
                                <td><?= htmlspecialchars($r['espacios_disponibles']) ?></td>
                                <td>
                                    <?php if ($r['espacios_disponibles'] > 0): ?>
                                        <form method="post" action="../logica/procesarReserva.php">
                                            <input type="hidden" name="id_ride" value="<?= $r['id_ride'] ?>">
                                            <button type="submit" class="btn-reservar">Reservar</button>
                                        </form>
                                    <?php else: ?>
                                        <h2> --- </h2>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($salida !== '' || $llegada !== ''): ?>
            <p class="mensaje info">No se encontraron rides para los filtros ingresados.</p>
        <?php endif; ?>
    </main>

</body>
</html>