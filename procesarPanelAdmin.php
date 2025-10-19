<?php
include_once 'usuarios.php';

// Verificar que hay un admin logueado
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    die("Acceso no permitido.");
}

// Procesar acciones POST
function procesarAccion() {
    if (isset($_POST['accion'], $_POST['id_usuario'])) {
        $id = $_POST['id_usuario'];
        $accion = $_POST['accion'];

        if (!in_array($accion, ['activar', 'desactivar'])) return;

        $estado = $accion === 'activar' ? 'Activo' : 'Inactivo';
        cambiarEstadoUsuario($id, $estado);

        echo "<script>alert('Estado modificado correctamente'); window.location.href='panelAdmin.php';</script>";
        exit;
    }
}


