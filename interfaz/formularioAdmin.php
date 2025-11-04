<?php

/*
 * Archivo: formularioAdmin.php
* Autores: Seidy Alanis y Walbyn González
 * Descripción: Formulario para registrar o editar un administrador. 
 * Permite mostrar mensajes de error o éxito y manejar campos según si 
 * se inserta o actualiza.
 */

session_start();

include_once("../logica/funcionesInterfaz.php");
include_once("../utilidades/formulariosUtilidades.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}


$preparacion = prepararFormularioUsuario();
$usuario = $preparacion['usuario'];
$accion = $preparacion['accion'];
$mensaje = $preparacion['mensaje'];
$datosFormulario = $preparacion['datosFormulario'];
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $accion==='actualizar' ? "Editar Administrador" : "Registrar Administrador" ?></title>
    <link rel="stylesheet" href="../Estilos/estilosRegistro.css">
</head>
<body>
<div class="registro-container">
    <div class="form-card">


        <form action="adminPanel.php" method="get" class="form-salir">
            <button type="submit" class="btn-cerrar-x">✖</button>
        </form>

        <h2><?= $accion === 'actualizar' ? "Editar Administrador" : "Registrar Administrador" ?></h2>

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

        <form action="../logica/procesarUsuarios.php" method="POST" enctype="multipart/form-data">
            
            <!-- Hidden inputs -->
            <?php if($accion === 'actualizar'): ?>
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars(valorUsuario('id_usuario', $datosFormulario, $usuario)) ?>">
                <input type="hidden" name="fotografia_existente" value="<?= htmlspecialchars(valorUsuario('fotografia_existente', $datosFormulario, $usuario)) ?>">
                <input type="hidden" name="accion" value="actualizar">
            <?php else: ?>
                <input type="hidden" name="rol" value="Administrador">
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
                <input type="text" name="cedula" value="<?= htmlspecialchars(valorUsuario('cedula', $datosFormulario, $usuario)) ?>" required required placeholder="Ej: 205670234">
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
                <input type="text" name="telefono" value="<?= htmlspecialchars(valorUsuario('telefono', $datosFormulario, $usuario)) ?>" required required placeholder="Ej: 88080987">
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
            <?php endif; ?>

            <button type="submit" class="btn-registrar"><?= $accion === 'actualizar' ? "Actualizar" : "Registrar" ?></button>
          
        </form>
    </div>
</div>
</body>
</html>

