<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Crear Usuario Administrador</h2>
    <form action="procesarRegistro.php" method="POST" enctype="multipart/form-data">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br>

        <label>Cédula:</label><br>
        <input type="text" name="cedula" required><br>

        <label>Fecha de Nacimiento:</label><br>
        <input type="date" name="fecha_nacimiento" required><br>

        <label>Correo:</label><br>
        <input type="email" name="correo" required><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" required><br>

        <label>Fotografía:</label><br>
        <input type="file" name="fotografia" accept="image/*"><br>

        <label>Contraseña:</label><br>
        <input type="password" name="contrasena" required><br>

        <label>Confirmar Contraseña:</label><br>
        <input type="password" name="contrasena2" required><br>

        <label>Rol:</label><br>
        <input type="text" name="rol" value="Administrador" readonly required><br>

        <input type="submit" value="Crear">
    </form>
</body>