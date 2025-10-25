<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="../Estilos/estilosLogin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>Iniciar Sesión</h1>
            <form action="../logica/procesarLogin.php" method="POST">
                <div class="input-group">
                    <label>Usuario</label>
                    <input type="text" name="cedula" placeholder="Ingrese su cédula" required>
                </div>

                <div class="input-group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>

            <p class="registro-texto">
                ¿No tienes cuenta?
                <a href="../interfaz/registroUsuario.php">Regístrate aquí</a>
            </p>
        </div>
    </div>
</body>
</html>
