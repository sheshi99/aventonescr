<?php
session_start();
include_once("../datos/usuarios.php");

// Determinar si estamos editando o registrando
$editar = $_GET['editar'] ?? 0;
$datosUsuario = [];

// Si estamos editando y hay usuario logueado
if ($editar == 1 && isset($_SESSION['usuario'])) {
    $datosUsuario = obtenerUsuarioPorId($_SESSION['usuario']['id_usuario']);
}

// Recuperar datos del formulario desde sesión (si hubo error)
$formData = $_SESSION['form_data'] ?? $datosUsuario ?? [];

// Recuperar mensaje
$mensaje = $_SESSION['mensaje']['texto'] ?? '';
$tipo = $_SESSION['mensaje']['tipo'] ?? 'info';

// Limpiar sesión para evitar mostrar mensajes repetidos
unset($_SESSION['form_data'], $_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $editar ? "Editar Perfil" : "Registrar Administrador" ?></title>
    <link rel="stylesheet" href="../Estilos/estilosRegistroUsuario.css">
</head>
<body>
<div class="registro-container">
    <div class="form-card">
        <h2><?= $editar ? "Editar Perfil" : "Registrar Administrador" ?></h2>

        <!-- Mensaje -->
        <?php if (!empty($mensaje)): 
            $clase = match($tipo) {
                'success' => 'alert-success',
                'error'   => 'alert-error',
                default   => 'alert-info',
            };
        ?>
            <div class="alert <?= $clase ?>"><?= htmlspecialchars($mensaje) ?></div><br>
        <?php endif; ?>

        <form action="../logica/procesarRegistro.php" method="POST" enctype="multipart/form-data">

            <?php if($editar): ?>
                <input type="hidden" name="editar" value="1">
                <input type="hidden" name="id_usuario" value="<?= $formData['id_usuario'] ?? '' ?>">
                <input type="hidden" name="fotografia_existente" value="<?= $formData['fotografia'] ?? '' ?>">
            <?php else: ?>
                <input type="hidden" name="rol" value="Administrador">
            <?php endif; ?>

            <div class="input-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($formData['nombre'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= htmlspecialchars($formData['apellido'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Cédula</label>
                <input type="text" name="cedula" value="<?= htmlspecialchars($formData['cedula'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($formData['fecha_nacimiento'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Correo</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($formData['correo'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($formData['telefono'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Fotografía</label>
                <input type="file" name="fotografia" accept="image/*" <?= $editar ? '' : 'required' ?>>
            </div>

            <?php if(!$editar): ?>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena" value="<?= htmlspecialchars($formData['contrasena'] ?? '') ?>" required>
            </div>

            <div class="input-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="contrasena2" value="<?= htmlspecialchars($formData['contrasena2'] ?? '') ?>" required>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn-registrar"><?= $editar ? "Actualizar" : "Registrar" ?></button>
            <a href="<?= $editar ? 'adminPanel.php' : 'adminPanel.php' ?>" class="btn-volver">
                ⬅ <?= $editar ? "Regresar al Panel" : "Volver al Panel" ?>
            </a>
        </form>
    </div>
</div>
</body>
</html>
