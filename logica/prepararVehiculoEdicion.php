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

// Modo edición usando POST (no GET)
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

// Capturar valores previos en caso de error
$valores = $_SESSION['datos_formulario'] ?? [];
$campoError = $_SESSION['mensaje']['campo_error'] ?? null;

// Guardar mensaje y tipo
$mensajeTexto = $_SESSION['mensaje']['texto'] ?? '';
$mensajeTipo = $_SESSION['mensaje']['tipo'] ?? 'info';

// Limpiar la sesión para que no se repita el mensaje
unset($_SESSION['mensaje'], $_SESSION['datos_formulario']);

// Función para obtener el valor correcto de cada campo
function valorCampo($campo, $vehiculo, $valores, $campoError) {
    if ($campoError === $campo) return ''; // Vacío si es el campo con error
    if (isset($valores[$campo])) return htmlspecialchars($valores[$campo]); // Valor ingresado por usuario
    return htmlspecialchars($vehiculo[$campo] ?? ''); // Valor original del vehículo
}
