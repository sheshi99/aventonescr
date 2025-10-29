<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

// Inicializar valores
$vehiculo = [
    'placa' => '',
    'color' => '',
    'marca' => '',
    'modelo' => '',
    'anno' => '',
    'asientos' => ''
];
$accion = 'guardar';
$id_vehiculo = null;


if (!empty($_POST['id_vehiculo'])) {
    $id_vehiculo = $_POST['id_vehiculo'];
    $vehiculoDB = obtenerVehiculoPorId($id_vehiculo);

    if ($vehiculoDB) {
        $vehiculo = [
            'placa'    => $vehiculoDB['numero_placa'] ?? '',
            'color'    => $vehiculoDB['color'] ?? '',
            'marca'    => $vehiculoDB['marca'] ?? '',
            'modelo'   => $vehiculoDB['modelo'] ?? '',
            'anno'     => $vehiculoDB['anno'] ?? '',
            'asientos' => $vehiculoDB['capacidad_asientos'] ?? ''
        ];
        $accion = 'actualizar';
    }
}
?>