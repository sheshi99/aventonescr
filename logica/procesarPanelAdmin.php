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


