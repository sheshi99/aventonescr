<?php
session_start();
include_once ("../logica/procesarPanelAdmin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['id_usuario'])) {
    procesarAccion(); // Activar o desactivar usuario
}

list($rolFiltrado, $usuarios, $sinRolSeleccionado) = obtenerUsuariosFiltrados();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="../Estilos/estilosPanelAdmin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Panel de Administración</h1>
        <div class="admin-user">
            👤 <?php echo $_SESSION['usuario']['nombre']; ?> (Administrador)
             <a href="../logica/cerrarSesion.php" class="btn-cerrar" style="margin-left: 15px; color: white; text-decoration: none;">
            🔒 Cerrar Sesión
            </a>

        </div>
    </header>

    <main class="admin-main">
        <section class="filtros">
            <form method="POST" class="form-filtro">
                <label for="filtro_rol">Filtrar por rol:</label>
                <select name="filtro_rol" onchange="window.location='?filtro_rol='+this.value;">
                    <option value="Seleccione un rol">Seleccione un rol</option>
                    <option value="Chofer" <?php if($rolFiltrado==='Chofer') echo 'selected'; ?>>Chofer</option>
                    <option value="Pasajero" <?php if($rolFiltrado==='Pasajero') echo 'selected'; ?>>Pasajero</option>
                    <option value="Administrador" <?php if($rolFiltrado==='Administrador') echo 'selected'; ?>>Administrador</option>
                </select>
            </form>

            <form action="registroAdmin.php" method="get">
                <input type="hidden" name="admin" value="1">
                <button type="submit" class="btn-nuevo">➕ Crear Usuario Administrador</button>
            </form>
        </section>

        <section class="tabla-usuarios">
            <?php if (!$sinRolSeleccionado): ?>
                <h2>Usuarios <?php echo htmlspecialchars($rolFiltrado); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id_usuario']; ?></td>
                                <td><?php echo $usuario['nombre'].' '.$usuario['apellido']; ?></td>
                                <td><?php echo $usuario['cedula']; ?></td>
                                <td><?php echo $usuario['correo']; ?></td>
                                <td><?php echo $usuario['telefono']; ?></td>
                                <td>
                                    <span class="estado <?php echo strtolower($usuario['estado']); ?>">
                                        <?php echo $usuario['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" class="form-accion">
                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                        <input type="hidden" name="accion" value="<?php echo $usuario['estado']==='Activo' ? 'desactivar' : 'activar'; ?>">
                                        <input type="hidden" name="filtro_rol" value="<?php echo htmlspecialchars($rolFiltrado); ?>"> 
                                        <button type="submit" class="<?php echo $usuario['estado']==='Activo' ? 'btn-desactivar' : 'btn-activar'; ?>">
                                            <?php echo $usuario['estado']==='Activo' ? 'Desactivar' : 'Activar'; ?>
                                        </button>
                                    </form>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
