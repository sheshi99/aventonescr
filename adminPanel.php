<?php
session_start();
include_once 'usuarios.php';
include_once 'procesarPanelAdmin.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    die("Acceso no permitido.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'], $_POST['id_usuario'])) {
        procesarAccion(); // Activar/Desactivar usuario
    }
    if (isset($_POST['filtro_rol'])) {
        $rolFiltrado = $_POST['filtro_rol'];
    }
} 

// Por defecto mostramos "Choferes" si no hay filtro
$rolFiltrado = $rolFiltrado ?? 'Chofer';
$usuarios = listarUsuariosPorRol($rolFiltrado);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?> (Administrador)</h1>

    <!-- Filtro por rol -->
    <form method="POST">
        <label>Filtrar por rol:</label>
        <select name="filtro_rol" onchange="this.form.submit()">
            <option value="Chofer" <?php if($rolFiltrado==='Chofer') echo 'selected'; ?>>Chofer</option>
            <option value="Pasajero" <?php if($rolFiltrado==='Pasajero') echo 'selected'; ?>>Pasajero</option>
            <option value="Administrador" <?php if($rolFiltrado==='Administrador') echo 'selected'; ?>>Administrador</option>
        </select>
    </form>

    <!-- Botón para ir al registro de Administrador -->
    <form action="registroUsuarioAdmin.php" method="get" style="margin-top: 10px;">
        <button type="submit">Crear Usuario Administrador</button>
    </form>

    <!-- Tabla de usuarios filtrada -->
    <h2>Usuarios <?php echo $rolFiltrado; ?></h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?php echo $u['id_usuario']; ?></td>
            <td><?php echo $u['nombre'].' '.$u['apellido']; ?></td>
            <td><?php echo $u['cedula']; ?></td>
            <td><?php echo $u['correo']; ?></td>
            <td><?php echo $u['telefono']; ?></td>
            <td><?php echo $u['estado']; ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                    <input type="hidden" name="accion" value="<?php echo $u['estado']==='Activo' ? 'desactivar' : 'activar'; ?>">
                    <input type="submit" value="<?php echo $u['estado']==='Activo' ? 'Desactivar' : 'Activar'; ?>">
                </form>

                <?php if($u['rol'] === 'Administrador'): ?>
                    <form method="POST" action="registroUsuarioAdmin.php" style="display:inline;">
                        <input type="hidden" name="editar_id" value="<?php echo $u['id_usuario']; ?>">
                        <input type="submit" value="Editar">
                    </form>

                    <form method="POST" action="procesarPanelAdmin.php" style="display:inline;">
                        <input type="hidden" name="eliminar_id" value="<?php echo $u['id_usuario']; ?>">
                        <input type="submit" value="Eliminar" onclick="return confirm('¿Seguro que quieres eliminar este admin?');">
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
