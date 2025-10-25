<?php

include_once ("../datos/usuarios.php");


if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    die("Acceso no permitido.");
}

function procesarAccion() {
    if (isset($_POST['accion'], $_POST['id_usuario'])) {
        $id = $_POST['id_usuario'];
        $accion = $_POST['accion'];

        if (!in_array($accion, ['activar', 'desactivar'])) return;

        $estado = $accion === 'activar' ? 'Activo' : 'Inactivo';
        cambiarEstadoUsuario($id, $estado);

        echo "<script>alert('Estado modificado correctamente'); window.location.href='adminPanel.php';</script>";
        exit;
    }
}
/**
 * Función para obtener el rol filtrado y la lista de usuarios
 */
function obtenerUsuariosFiltrados() {
    $rolesValidos = ['Administrador', 'Chofer', 'Pasajero'];
    $rolFiltrado = null; // por defecto no hay selección
    $usuarios = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filtro_rol'])) {
        $seleccion = $_POST['filtro_rol'];

        if (in_array($seleccion, $rolesValidos)) {
            $rolFiltrado = $seleccion;
            $usuarios = listarUsuariosPorRol($rolFiltrado);
        }
    }

    return [$rolFiltrado, $usuarios];
}
