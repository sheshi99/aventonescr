<?php

include_once("../datos/usuarios.php");

// ===== Función de mensaje y redirección =====
function mensajeYRedirigir($texto, $tipo = 'error', $url = '../interfaz/adminPanel.php') {
    $_SESSION['mensaje'] = ['texto' => $texto, 'tipo' => $tipo];
    header("Location: $url");
    exit;
}

// ===== Obtener usuarios filtrados =====
function obtenerUsuariosFiltrados() {
    $rolesValidos = ['Administrador', 'Chofer', 'Pasajero'];
    $rolFiltrado = null;
    $usuarios = [];
    $sinRolSeleccionado = true;

    $seleccion = $_POST['filtro_rol'] ?? $_GET['filtro_rol'] ?? null;

    if ($seleccion && in_array($seleccion, $rolesValidos)) {
        $rolFiltrado = $seleccion;
        $usuarios = listarUsuariosPorRol($rolFiltrado);
        $sinRolSeleccionado = false;
    }

    return [$rolFiltrado, $usuarios, $sinRolSeleccionado];
}

// ===== Procesar acción de desactivar =====
function procesarAccion() {
    if (isset($_POST['accion'], $_POST['id_usuario'])) {
        $id = $_POST['id_usuario'];
        $accion = $_POST['accion'];

        // Solo se permite desactivar
        if ($accion !== 'desactivar') {
            mensajeYRedirigir("Acción no permitida.", "error");
        }

        $usuario = obtenerUsuarioPorId($id);
        if (!$usuario) {
            mensajeYRedirigir("Usuario no encontrado.", "error");
        }

        if ($usuario['estado'] !== 'Activo' && $usuario['estado'] !== 'Pendiente') {
        mensajeYRedirigir("Error al desactivar usuario", "error");
        }

        // Cambiar estado a Inactivo
        cambiarEstadoUsuario($id, 'Inactivo');
        mensajeYRedirigir("✅ Usuario desactivado correctamente.", "success");
    }
}

// ===== Ejecutar si viene acción =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['id_usuario'])) {
    procesarAccion();
}
?>


