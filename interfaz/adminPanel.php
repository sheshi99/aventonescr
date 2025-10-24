<?php
session_start();
include_once ("../logica/procesarPanelAdmin.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['id_usuario'])) {
    procesarAccion(); // Activar o desactivar
}

list($rolFiltrado, $usuarios) = obtenerUsuariosFiltrados();
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
        </div>
    </header>

    <main class="admin-main">
        <section class="filtros">
            <form method="POST" class="form-filtro">
                <label for="filtro_rol">Filtrar por rol:</label>
                <select name="filtro_rol" id="filtro_rol" onchange="this.form.submit()">
                    <option value="Seleccione un rol">Seleccione un rol</option>
                    <option value="Chofer" <?php if($rolFiltrado==='Chofer') echo 'selected'; ?>>Chofer</option>
                    <option value="Pasajero" <?php if($rolFiltrado==='Pasajero') echo 'selected'; ?>>Pasajero</option>
                    <option value="Administrador" <?php if($rolFiltrado==='Administrador') echo 'selected'; ?>>Administrador</option>
                </select>
            </form>

            <form action="registroUsuarioAdmin.php" method="get">
                <button type="submit" class="btn-nuevo">➕ Crear Usuario Administrador</button>
            </form>
        </section>

        <section class="tabla-usuarios">
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
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo $u['id_usuario']; ?></td>
                        <td><?php echo $u['nombre'].' '.$u['apellido']; ?></td>
                        <td><?php echo $u['cedula']; ?></td>
                        <td><?php echo $u['correo']; ?></td>
                        <td><?php echo $u['telefono']; ?></td>
                        <td>
                            <span class="estado <?php echo strtolower($u['estado']); ?>">
                                <?php echo $u['estado']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="form-accion">
                                <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                                <input type="hidden" name="accion" value="<?php echo $u['estado']==='Activo' ? 'desactivar' : 'activar'; ?>">
                                <button type="submit" 
                                        class="<?php echo $u['estado']==='Activo' ? 'btn-desactivar' : 'btn-activar'; ?>">
                                    <?php echo $u['estado']==='Activo' ? 'Desactivar' : 'Activar'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
