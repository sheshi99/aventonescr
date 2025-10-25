<?php

include_once ("../datos/usuarios.php");


if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    die("Acceso no permitido.");
}

function obtenerUsuariosFiltrados() {
    $rolesValidos = ['Administrador', 'Chofer', 'Pasajero'];
    $rolFiltrado = null;
    $usuarios = [];
    $sinRolSeleccionado = true;

    // Buscar el rol desde POST o GET
    $seleccion = $_POST['filtro_rol'] ?? $_GET['filtro_rol'] ?? null;

    if ($seleccion && in_array($seleccion, $rolesValidos)) {
        $rolFiltrado = $seleccion;
        $usuarios = listarUsuariosPorRol($rolFiltrado);
        $sinRolSeleccionado = false;
    }

    return [$rolFiltrado, $usuarios, $sinRolSeleccionado];
}


function procesarAccion() {
    if (isset($_POST['accion'], $_POST['id_usuario'])) {
        $id = $_POST['id_usuario'];
        $accion = $_POST['accion'];

        if (!in_array($accion, ['activar', 'desactivar'])) return;

        $estado = ($accion === 'activar') ? 'Activo' : 'Inactivo';
        cambiarEstadoUsuario($id, $estado);

        // Recupera el rol actual desde POST o GET
        $rolActual = isset($_POST['filtro_rol']) ? urlencode($_POST['filtro_rol']) 
                    : (isset($_GET['filtro_rol']) ? urlencode($_GET['filtro_rol']) : '');

        // Redirige conservando el rol seleccionado
        echo "<script>
                alert('✅ Estado modificado correctamente');
                window.location.href = '../interfaz/adminPanel.php" . ($rolActual ? "?filtro_rol={$rolActual}" : "") . "';
              </script>";
        exit;
    }
}



