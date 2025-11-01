<?php
session_start();
include_once("../datos/vehiculos.php");
include_once("../utilidades/mensajes.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];


function obtenerDatosFormulario() {
    return [
        'placa'   => trim($_POST['placa'] ?? ''),
        'color'   => trim($_POST['color'] ?? ''),
        'marca'   => trim($_POST['marca'] ?? ''),
        'modelo'  => trim($_POST['modelo'] ?? ''),
        'anno'    => trim($_POST['anno'] ?? ''),
        'asientos'=> trim($_POST['asientos'] ?? ''),
        'fotografia_existente' => $_POST['fotografia_existente'] ?? null
    ];
}

// ==================== VALIDACIONES ====================

function validarDatos($datos) {
    foreach ($datos as $campo => $valor) {
        if ($campo !== 'fotografia_existente' && empty($valor)) {
            redirigirMsjVehiculo(
                "❌ El campo $campo es obligatorio",
                "../interfaz/registroVehiculo.php",
                "error", $datos, $campo
            );
        }
    }
    $id_vehiculo = $_POST['id_vehiculo'] ?? null; // Para actualizar
    if (placaExiste($datos['placa'], $id_vehiculo)) {
        redirigirMsjVehiculo("❌ La placa ya está registrada", "../interfaz/registroVehiculo.php",
         "error", $datos, 'placa');
    }

    if (!preg_match('/^[A-Z0-9\-]{1,20}$/i', $datos['placa'])) {
        redirigirMsjVehiculo("❌ La placa no es válida", "../interfaz/registroVehiculo.php", 
        "error", $datos, 'placa');
    }

    if (!is_numeric($datos['anno']) || $datos['anno'] < 1900 || $datos['anno'] > date('Y')) {
        redirigirMsjVehiculo("❌ Año no válido", "../interfaz/registroVehiculo.php", 
        "error", $datos, 'anno');
    }

    if (!is_numeric($datos['asientos']) || $datos['asientos'] < 1) {
        redirigirMsjVehiculo("❌ Cantidad de asientos no válida", 
                            "../interfaz/registroVehiculo.php", "error", $datos, 'asientos');
    }
}


function procesarFotografiaVehiculo($datos) {
    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return $datos['fotografia_existente'] ?? null;
    }

    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/vehiculos/';

    if (!is_dir($baseRuta)) mkdir($baseRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        redirigirMsjVehiculo("❌ Formato no permitido. Solo JPG, PNG o GIF", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    if ($archivo['size'] > 2*1024*1024) {
        redirigirMsjVehiculo("❌ La fotografía supera los 2MB", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    $nombreArchivo = preg_replace('/[^A-Za-z0-9]/', '', $datos['placa']) . '.' . $ext;
    $destino = $baseRuta . $nombreArchivo;

    if (file_exists($destino)) unlink($destino);

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        redirigirMsjVehiculo("❌ Error al guardar la fotografía", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    return $destino;
}

// ==================== ACCIONES ====================

function actualizarVehiculoAction($id_vehiculo, $datos, $foto) {
    if (empty($foto)) {
        $vehiculoDB = obtenerVehiculoPorId($id_vehiculo);
        $foto = $vehiculoDB['fotografia'] ?? null;
    }

  
    $resultado = actualizarVehiculo(
        $id_vehiculo, $datos['placa'], $datos['color'], $datos['marca'],
        $datos['modelo'], $datos['anno'], $datos['asientos'], $foto
    );
    

    if ($resultado) {
        redirigirMsjVehiculo("✅ Vehículo actualizado", "../interfaz/gestionVehiculos.php", 
                            "success");
    } else {
        redirigirMsjVehiculo("❌ Error al actualizar", "../interfaz/registroVehiculo.php", 
                            "error", $datos);
    }
}

function registrarVehiculoAction($id_chofer, $datos, $foto) {
    $resultado = insertarVehiculo(
        $id_chofer, $datos['placa'], $datos['color'], $datos['marca'],
        $datos['modelo'], $datos['anno'], $datos['asientos'], $foto
    );

    if ($resultado) {
        redirigirMsjVehiculo("✅ Vehículo registrado", "../interfaz/gestionVehiculos.php", 
                            "success");
    } else {
        redirigirMsjVehiculo("❌ Error al registrar", "../interfaz/registroVehiculo.php", 
                            "error", $datos);
    }
}

function eliminarVehiculoAction($id_vehiculo) {
    try {
        if (eliminarVehiculo($id_vehiculo)) {
            redirigirMsjVehiculo("✅ Vehículo eliminado", "../interfaz/gestionVehiculos.php", 
                                "success");
        }
    } catch (Exception $e) {
        redirigirMsjVehiculo("❌ No se pudo eliminar: " . $e->getMessage(), 
                            "../interfaz/gestionVehiculos.php", "error");
    }
}


function gestionarVehiculo($id_chofer) {
$accion = $_POST['accion'] ?? 'guardar';
$id_vehiculo = $_POST['id_vehiculo'] ?? null;

switch($accion) {
    case 'eliminar':
    if ($id_vehiculo !== null && $id_vehiculo !== '') {
        eliminarVehiculoAction($id_vehiculo);
    } else {
        redirigirMsjVehiculo("❌ ID de vehículo inválido", "../interfaz/gestionVehiculos.php", 
        "error");
    }
    break;


    case 'actualizar':
        $datos = obtenerDatosFormulario();
        validarDatos($datos);
        $datos['anno'] = (int)$datos['anno'];
        $datos['asientos'] = (int)$datos['asientos'];
        $foto = procesarFotografiaVehiculo($datos);
        actualizarVehiculoAction($id_vehiculo, $datos, $foto);
        break;

    case 'guardar':
    default:
        $datos = obtenerDatosFormulario();
        validarDatos($datos);
        $datos['anno'] = (int)$datos['anno'];
        $datos['asientos'] = (int)$datos['asientos'];
        $foto = procesarFotografiaVehiculo($datos);
        registrarVehiculoAction($id_chofer, $datos, $foto);
        break;
    }
}

// ==================== EJECUTAR ACCIÓN SEGÚN POST ====================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
    gestionarVehiculo($id_chofer);
} else {
    die('Acceso no permitido');
}
?>
