<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

// Funciones específicas
function eliminar($id_vehiculo) {
    return eliminarVehiculo($id_vehiculo);
}

function editar($id_vehiculo) {
    header("Location: formEditarVehiculo.php?id_vehiculo=$id_vehiculo");
    exit;
}

function agregar() {
    header("Location: formAgregarVehiculo.php");
    exit;
}

function guardarEdicion($vehiculo) {
    return modificarVehiculo(
        $vehiculo['id_vehiculo'], $vehiculo['placa'], $vehiculo['color'], $vehiculo['marca'],
        $vehiculo['modelo'], $vehiculo['anno'], $vehiculo['asientos'], $vehiculo['foto'] ?? null
    );
}

function guardarAgregado($vehiculo, $id_chofer) {
    return insertarVehiculo(
        $id_chofer, $vehiculo['placa'], $vehiculo['color'], $vehiculo['marca'],
        $vehiculo['modelo'], $vehiculo['anno'], $vehiculo['asientos'], $vehiculo['foto'] ?? null
    );
}

// Función controladora principal
function procesarVehiculos($id_chofer) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    $accion = $_POST['accion'] ?? '';
    switch ($accion) {
        case 'eliminar':
            $resultado = eliminar($_POST['id_vehiculo']);
            $msg = $resultado ? "Vehículo eliminado" : "Error al eliminar";
            break;

        case 'editar':
            editar($_POST['id_vehiculo']);
            break;

        case 'agregar':
            agregar();
            break;

        case 'guardar_edicion':
            $resultado = guardarEdicion($_POST);
            $msg = $resultado ? "Vehículo actualizado" : "Error al actualizar";
            break;

        case 'guardar_agregado':
            $resultado = guardarAgregado($_POST, $id_chofer);
            $msg = $resultado ? "Vehículo agregado" : "Error al agregar";
            break;

        default:
            $msg = null;
    }

    if (!empty($msg)) {
        header("Location: gestionVehiculos.php?msg=" . urlencode($msg));
        exit;
    }
}

// Llamada a la función controladora
procesarVehiculos($id_chofer);
