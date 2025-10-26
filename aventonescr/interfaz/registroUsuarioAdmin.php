<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../Estilos/estilosRegistroUsuario.css">
</head>
<body>
    <div class="registro-container">
        <div class="form-card">
            <h2>Registro de Usuario Administrador</h2>

            <form action="../logica/procesarRegistro.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>

                <div class="input-group">
                    <label>Apellido</label>
                    <input type="text" name="apellido" required>
                </div>

                <div class="input-group">
                    <label>Cédula</label>
                    <input type="text" name="cedula" required>
                </div>

                <div class="input-group">
                    <label>Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" required>
                </div>

                <div class="input-group">
                    <label>Correo</label>
                    <input type="email" name="correo" required>
                </div>

                <div class="input-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" required>
                </div>

                <div class="input-group">
                    <label>Fotografía</label>
                    <input type="file" name="fotografia" accept="image/*">
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
                    <select name="rol" required>
                        <option value="Administrador">Administrador</option>
                    </select>
                </div>

                <?php
                session_start();
                if (!empty($_SESSION['mensaje'])) {
                    $mensaje = $_SESSION['mensaje']['texto'];
                    $tipo = $_SESSION['mensaje']['tipo']; // success, error, info

                    // Mapear el tipo a clase CSS
                    $clase = match($tipo) {
                        'success' => 'alert-success',
                        'error'   => 'alert-error',
                        default   => 'alert-info',
                    };

                    echo "<div class='alert {$clase}'>{$mensaje}</div>";

                    // Limpiar la sesión para que no se repita
                    unset($_SESSION['mensaje']);
                }
                ?>

                <button type="submit" class="btn-registrar">Registrar</button>
            </form>
        </div>
    </div>
</body>
</html>
