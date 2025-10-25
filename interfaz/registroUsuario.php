<?php
session_start();

$soloAdmin = (isset($_SESSION['usuario']['rol']) 
&& $_SESSION['usuario']['rol'] === 'Administrador');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $soloAdmin ? "Registrar Administrador" : "Registro de Usuario" ?></title>
    <link rel="stylesheet" href="../Estilos/estilosRegistroUsuario.css">
</head>
<body>
    <div class="registro-container">
        <div class="form-card">
            <h2><?= $soloAdmin ? "Registrar Administrador" : "Registro de Usuario" ?></h2>

            <form action="../logica/procesarRegistro.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['apellido'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Cédula</label>
                    <input type="text" name="cedula" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['cedula'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['fecha_nacimiento'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Correo</label>
                    <input type="email" name="correo" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['correo'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['telefono'] ?? '') ?>" required>
                </div>

                <div class="input-group">
                    <label>Fotografía</label>
                    <input type="file" name="fotografia" accept="image/*" required>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" required>
                </div>

                <div class="input-group">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="contrasena2" required>
                </div>

                <div class="input-group">
                    <label>Rol</label>
                    <?php if ($soloAdmin): ?>
                        <input type="hidden" name="rol" value="Administrador">
                        <input type="text" value="Administrador" readonly>
                    <?php else: ?>
                        <select name="rol" required>
                            <option value="">Seleccione un rol</option>
                            <option value="Chofer" <?= (($_SESSION['form_data']['rol'] ?? '') === 'Chofer') ? 'selected' : '' ?>>Chofer</option>
                            <option value="Pasajero" <?= (($_SESSION['form_data']['rol'] ?? '') === 'Pasajero') ? 'selected' : '' ?>>Pasajero</option>
                        </select>
                    <?php endif; ?>
                </div>

                <?php
                // Mostrar mensaje de éxito o error
                if (!empty($_SESSION['mensaje'])) {
                    $mensaje = $_SESSION['mensaje']['texto'];
                    $tipo = $_SESSION['mensaje']['tipo'];

                    $clase = match($tipo) {
                        'success' => 'alert-success',
                        'error'   => 'alert-error',
                        default   => 'alert-info',
                    };

                    echo "<div class='alert {$clase}'>{$mensaje}</div>";
                    unset($_SESSION['mensaje']);
                }
                ?>

                <button type="submit" class="btn-registrar">Registrar</button>

                <?php if ($soloAdmin): ?>
                    <a href="adminPanel.php" class="btn-volver">⬅ Regresar al Panel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php
    // Limpiar datos del formulario solo después de mostrarlos
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']);
    ?>
</body>
</html>
