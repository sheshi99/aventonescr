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
    <link rel="stylesheet" href="../Estilos/estilosTablas.css?v=3">
</head>
<body>
<header class="header">
    <div class="header-left">
        <div class="menu-contenedor">
            <input type="checkbox" id="toggle-menu" class="toggle-menu">
            <label for="toggle-menu" class="btn-menu">⋮</label>

            <div class="menu-opciones">
                <form action="cambioContraseña.php" method="get">
                    <input type="hidden" name="cambio" value="1">
                    <button type="submit" class="menu-boton">🔑 Cambiar Contraseña</button>
                </form>

                <form action="registroAdmin.php" method="POST">                
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario']['id_usuario'] ?>">
                    <button type="submit" class="menu-boton">✏️ Editar Perfil</button>
                </form>

            </div>
        </div>
    </div>

    <div class="user-info">
        <h2>Bienvenido a Panel de administración, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <form action="../logica/cerrarSesion.php" method="post" style="display:inline;">
            <button type="submit" class="btn-cerrar">Cerrar</button>
        </form>
    </div>
    
</header>


    <main class="main">
        <?php if(!empty($_SESSION['mensaje'])): ?>
            <p style="color: <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'red' : 'green' ?>; font-weight: bold;">
                <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
            </p>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

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

        <section class="tabla">
            <?php if (!$sinRolSeleccionado): ?>
                <h2>Usuarios <?= htmlspecialchars($rolFiltrado); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Fotografía</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id_usuario']; ?></td>
                                <td><?= htmlspecialchars($usuario['nombre'].' '.$usuario['apellido']); ?></td>
                                <td><?= htmlspecialchars($usuario['cedula']); ?></td>
                                <td><?= htmlspecialchars($usuario['correo']); ?></td>
                                <td><?= htmlspecialchars($usuario['telefono']); ?></td>
                                <td>
                                    <span class="estado <?= strtolower($usuario['estado']); ?>">
                                        <?= $usuario['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($usuario['fotografia'])): ?>
                                        <img src="<?= '../logica/' . htmlspecialchars($usuario['fotografia']); ?>" 
                                             alt="<?= htmlspecialchars($usuario['nombre']); ?>" 
                                             class="foto">
                                    <?php else: ?>
                                        <span>No hay foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" class="form-accion">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario']; ?>">
                                        <input type="hidden" name="accion" value="<?= $usuario
                                        ['estado']==='Activo' ? 'desactivar' : 'activar'; ?>">
                                        <input type="hidden" name="filtro_rol" value="<?= htmlspecialchars($rolFiltrado); ?>"> 
                                        <button type="submit" class="<?= $usuario
                                        ['estado']==='Activo' ? 'btn-rojo' : 'btn-verde'; ?>">
                                            <?= $usuario['estado']==='Activo' ? 'Desactivar' : 'Activar'; ?>
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
