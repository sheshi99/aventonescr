<?php
session_start();


if (!isset($_SESSION['usuario']['rol']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="../Estilos/estilosRegistroUsuario.css">
</head>
<body>
<div class="registro-container">
    <div class="form-card">
        <h2>Registrar Administrador</h2>


                 <?php
            if (!empty($_SESSION['mensaje'])) {
                $mensaje = $_SESSION['mensaje']['texto'];
                $tipo = $_SESSION['mensaje']['tipo'];
                $clase = match($tipo) {
                    'success' => 'alert-success',
                    'error'   => 'alert-error',
                    default   => 'alert-info',
                };
                echo "<div class='alert {$clase}'>{$mensaje}</div>. <br>";
               
                unset($_SESSION['mensaje']);
            }
            ?>

        <form action="../logica/procesarRegistro.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="rol" value="Administrador">

            <div class="input-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($_SESSION['form_data']['nombre'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= htmlspecialchars($_SESSION['form_data']['apellido'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Cédula</label>
                <input type="text" name="cedula" value="<?= htmlspecialchars($_SESSION['form_data']['cedula'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($_SESSION['form_data']['fecha_nacimiento'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Correo</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($_SESSION['form_data']['correo'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($_SESSION['form_data']['telefono'] ?? '') ?>" required>
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

            <button type="submit" class="btn-registrar">Registrar</button>
            <a href="adminPanel.php" class="btn-volver">⬅ Regresar al Panel</a>
        </form>
    </div>
</div>
<?php unset($_SESSION['form_data']); ?>
</body>
</html>
