<?php
session_start();

include_once("../datos/usuarios.php");
include_once("../utilidades/formulariosUtilidades.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

// ----------------------------
// Determinar si es edición o registro
// ----------------------------
$editar = $_GET['editar'] ?? 0;

// Preparar datos para el formulario
$preparacion = prepararFormularioUsuario(); // Reutilizamos la misma función
$usuario = $preparacion['usuario'];
$accion = $preparacion['accion']; // 'insertar' o 'actualizar'
$mensaje = $preparacion['mensaje'];
$datosFormulario = $preparacion['datosFormulario'];

// Determinar URL para el botón "volver" según rol
$rolUsuario = $_SESSION['usuario']['rol'] ?? null;
if ($rolUsuario === 'Chofer') {
    $urlVolver = 'choferPanel.php';
} elseif ($rolUsuario === 'Pasajero') {
    $urlVolver = 'pasajeroPanel.php';
} else {
    $urlVolver = 'Login.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion === 'actualizar' ? "Editar Usuario" : "Registrar Usuario" ?></title>
    <link rel="stylesheet" href="../Estilos/estilosRegistro.css">
</head>
<body>
<div class="registro-container">
    <div class="form-card">
        <h2><?= $accion === 'actualizar' ? "Editar Usuario" : "Registrar Usuario" ?></h2>

        <!-- Mensaje -->
        <?php if (!empty($mensaje)): 
            $clase = match($mensaje['tipo'] ?? 'info') {
                'success' => 'alert-success',
                'error'   => 'alert-error',
                default   => 'alert-info',
            };
        ?>
            <div class="alert <?= $clase ?>"><?= htmlspecialchars($mensaje['texto']) ?></div><br>
        <?php endif; ?>

        <form action="../logica/procesarRegistro.php" method="POST" enctype="multipart/form-data">

            <!-- Hidden inputs -->
            <?php if($accion === 'actualizar'): ?>
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars(valorUsuario('id_usuario', $datosFormulario, $usuario)) ?>">
                <input type="hidden" name="fotografia_existente" value="<?= htmlspecialchars(valorUsuario('fotografia', $datosFormulario, $usuario)) ?>">
                <input type="hidden" name="accion" value="actualizar">
            <?php else: ?>
                <input type="hidden" name="rol" value="<?= htmlspecialchars(valorUsuario('rol', $datosFormulario, $usuario) ?? 'Pasajero') ?>">
                <input type="hidden" name="accion" value="insertar">
            <?php endif; ?>

            <!-- Campos del formulario -->
            <div class="input-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars(valorUsuario('nombre', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= htmlspecialchars(valorUsuario('apellido', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Cédula</label>
                <input type="text" name="cedula" value="<?= htmlspecialchars(valorUsuario('cedula', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars(valorUsuario('fecha_nacimiento', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Correo</label>
                <input type="email" name="correo" value="<?= htmlspecialchars(valorUsuario('correo', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars(valorUsuario('telefono', $datosFormulario, $usuario)) ?>" required>
            </div>

            <div class="input-group">
                <label>Fotografía</label>
                <input type="file" name="fotografia" accept="image/*" <?= $accion === 'insertar' ? 'required' : '' ?>>
            </div>

            <?php if($accion === 'insertar'): ?>
                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" value="<?= htmlspecialchars(valorUsuario('contrasena', $datosFormulario, $usuario)) ?>" required>
                </div>

                <div class="input-group">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="contrasena2" value="<?= htmlspecialchars(valorUsuario('contrasena2', $datosFormulario, $usuario)) ?>" required>
                </div>

                <div class="input-group">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="Chofer" <?= (valorUsuario('rol', $datosFormulario, $usuario) ?? '') === 'Chofer' ? 'selected' : '' ?>>Chofer</option>
                        <option value="Pasajero" <?= (valorUsuario('rol', $datosFormulario, $usuario) ?? '') === 'Pasajero' ? 'selected' : '' ?>>Pasajero</option>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-registrar"><?= $accion === 'actualizar' ? "Actualizar" : "Registrar" ?></button>
            <a href="<?= $urlVolver ?>" class="btn-volver">⬅ Volver</a>
        </form>
    </div>
</div>
</body>
</html>
