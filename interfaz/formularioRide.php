<?php
/*
 * --------------------------------------------------------------
 * Archivo: formularioRides.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Es la interfaz de registro y edición de rides para choferes, que permite 
 * crear un nuevo ride o actualizar uno existente. También muestra mensajes 
 * de éxito o error según la acción realizada.
 * --------------------------------------------------------------
 */

session_start();
include_once("../datos/vehiculos.php");  
include_once("../datos/rides.php"); 
include_once("../utilidades/formulariosUtilidades.php");    

$id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
if (!$id_chofer) {
    header("Location: ../interfaz/login.php");
    exit;
}

$preparacion = prepararFormularioRide();
$ride = $preparacion['ride'];
$accion = $preparacion['accion'];
$datosFormulario = $preparacion['datosFormulario'];
$mensaje = $preparacion['mensaje'];

$vehiculos = obtenerVehiculosPorChofer($id_chofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= $accion==='actualizar' ? "Editar Ride" : "Registrar Ride" ?></title>
<link rel="stylesheet" href="../Estilos/estilosRegistro.css?v=3">
</head>
<body>

<div class="registro-container">
    <div class="form-card">

        <!-- Botón tipo X arriba a la derecha -->
        <form action="gestionRides.php" method="get" class="form-salir">
            <button type="submit" class="btn-cerrar-x" title="Volver a la gestión de rides">✖</button>
        </form>

        <h2><?= $accion==='actualizar' ? "Editar Ride" : "Registrar Ride" ?></h2>

        <?php if($mensaje): ?>
        <p class="mensaje <?= $mensaje['tipo'] === 'error' ? 'error' : 'success' ?>">
            <?= $mensaje['texto'] ?>
        </p>
        <?php endif; ?>

        <form action="../logica/procesarRide.php" method="post">
            <input type="hidden" name="accion" value="<?= $accion ?>">
            <?php if($accion==='actualizar'): ?>
                <input type="hidden" name="id_ride" value="<?= valor('id_ride',$datosFormulario,$ride) ?>">
            <?php endif; ?>

            
            <div class="input-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" required value="<?= valor('nombre',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Salida:</label>
                <input type="text" name="salida" required value="<?= valor('salida',$datosFormulario,$ride) ?>" placeholder="Ej: San José, Pavas">
            </div>

            <div class="input-group">
                <label>Llegada:</label>
                <input type="text" name="llegada" required value="<?= valor('llegada',$datosFormulario,$ride) ?>" placeholder="Ej: Puntarenas, Uvita">
            </div>

            <div class="input-group">
                <label>Día:</label>
                <input type="date" name="dia" value="<?= valor('dia', $datosFormulario, $ride) ?>">
            </div>

            <div class="input-group">
                <label>Hora:</label>
                <input type="time" name="hora" value="<?= valor('hora', $datosFormulario, $ride) ?>">
            </div>

            <div class="input-group">
                <label>Costo por espacio:</label>
                <input type="number" name="costo" step="0.01" required value="<?= valor('costo',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Espacios:</label>
                <input type="number" name="espacios" min="1" required value="<?= valor('espacios',$datosFormulario,$ride) ?>">
            </div>

            <div class="input-group">
                <label>Vehículo:</label>
                <select name="id_vehiculo" required>
                    <option value="">Seleccione un vehículo</option>
                   <?php foreach($vehiculos as $vehiculo): ?>
                        <option value="<?= $vehiculo['id_vehiculo'] ?>"
                            <?= valor('id_vehiculo', $datosFormulario, $ride) == $vehiculo['id_vehiculo'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vehiculo['numero_placa']) ?> - 
                            <?= htmlspecialchars($vehiculo['marca']) ?> 
                            <?= htmlspecialchars($vehiculo['modelo']) ?> 
                            (<?= htmlspecialchars($vehiculo['anno']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-registrar">
                <?= $accion==='actualizar' ? "Actualizar Ride" : "Registrar Ride" ?>
            </button>
        </form>
    </div>
</div>

</body>
</html>

