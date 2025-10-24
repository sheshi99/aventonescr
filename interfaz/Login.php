<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form action="../logica/procesarLogin.php" method="POST">
        <label>Usuario:</label>
        <input type="text" name="cedula" required><br>
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required><br>
        <input type="submit" value="Ingresar">
    </form>
    <p>¿No tienes cuenta? <a href="../interfaz/registroUsuariosPublicos.php">Regístrate</a></p>
</body>
</html>
