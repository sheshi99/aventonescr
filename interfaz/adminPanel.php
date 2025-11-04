<?php
/*
 * Archivo: adminPanel.php
 * Autores: Seidy Alanis y Walbyn González
 *
 * Descripción: Panel de administración que permite filtrar y listar usuarios
 * por rol, mostrar información básica, editar perfil, cambiar contraseña
 * y desactivar usuarios. Implementa seguridad con htmlspecialchars
 * y mensajes de sesión para feedback al administrador.
 */

session_start();
include_once("../logica/procesarPanelAdmin.php");

// Obtener usuarios filtrados
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
            <!-- Botón del menú desplegable -->
            <input type="checkbox" id="toggle-menu" class="toggle-menu">
            <label for="toggle-menu" class="btn-menu">⋮</label>

            <!-- Foto del usuario al lado del menú con espacio -->
            <div class="espacio-menu-foto">
                <?php if (!empty($_SESSION['usuario']['fotografia'])): ?>
                    <img src="<?= '../logica/' . htmlspecialchars($_SESSION['usuario']['fotografia']); ?>" 
                        alt="<?= htmlspecialchars($_SESSION['usuario']['nombre']); ?>" 
                        class="foto">
                <?php else: ?>
                    <img src="../Estilos/default-user.png" alt="Usuario" class="foto">
                <?php endif; ?>
            </div>

            <!-- Opciones del menú desplegable -->
            <div class="menu-opciones">
                <form action="cambioContraseña.php" method="get">
                    <input type="hidden" name="cambio" value="1">
                    <button type="submit" class="menu-boton">🔑 Cambiar Contraseña</button>
                </form>
                <form action="formularioAdmin.php" method="POST"> 
                    <input type="hidden" name="accion" value="actualizar"> 
                    <input type="hidden" name="id_usuario" value="<?= $_SESSION['usuario']['id_usuario'] ?>"> 
                    <button type="submit" class="menu-boton">✏️ Editar Perfil</button>         
                </form>
            </div>
        </div>
    </div>

    <div class="user-info">
        <h2>Bienvenido al Panel de Administración, <?= htmlspecialchars($_SESSION['usuario']['nombre']); ?></h2>
        <form action="../logica/cerrarSesion.php" method="post" class="form-cerrar-sesion">
            <button type="submit" class="btn-cerrar">Cerrar</button>
        </form>
    </div>
</header>

<main class="main">
    <?php if (!empty($_SESSION['mensaje'])): ?>
        <p class="mensaje <?= $_SESSION['mensaje']['tipo'] === 'error' ? 'error' : 'success' ?>">
            <?= htmlspecialchars($_SESSION['mensaje']['texto']) ?>
        </p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <section class="filtros">
        <form method="POST" class="form-filtro">
            <label for="filtro_rol">Filtrar por rol:</label>
            <select name="filtro_rol" onchange="window.location='?filtro_rol='+this.value;">
                <option value="Seleccione un rol">Seleccione un rol</option>
                <option value="Chofer" <?= $rolFiltrado==='Chofer' ? 'selected' : '' ?>>Chofer</option>
                <option value="Pasajero" <?= $rolFiltrado==='Pasajero' ? 'selected' : '' ?>>Pasajero</option>
                <option value="Administrador" <?= $rolFiltrado==='Administrador' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </form>
    </section>

    <section class="tabla">
        <?php if (!$sinRolSeleccionado): ?>
            <h2>Usuarios <?= htmlspecialchars($rolFiltrado); ?></h2>
            <table>
                <thead>
                    <tr>
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
                                <?php if ($usuario['estado'] === 'Activo' || ($_SESSION['usuario']['rol'] === 'Administrador' && $usuario['estado'] === 'Pendiente')): ?>
                                    <form method="POST" class="form-accion">
                                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario']; ?>">
                                        <input type="hidden" name="accion" value="desactivar">
                                        <input type="hidden" name="filtro_rol" value="<?= htmlspecialchars($rolFiltrado); ?>"> 
                                        <button type="submit" class="btn-rojo">Desactivar</button>
                                    </form>
                                <?php else: ?>
                                    <span>----</span>
                                <?php endif; ?>
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
