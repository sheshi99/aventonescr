<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

// Mostrar mensaje y redirigir
function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info', $datosFormulario = [], $campoError = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo, 'campo_error' => $campoError];

    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    $_SESSION['datos_formulario'] = $datosFormulario;
    header("Location: $destino");
    exit;
}

// Obtener datos del formulario
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

// Validar datos
function validarDatos($datos) {
    foreach ($datos as $campo => $valor) {
        if ($campo !== 'fotografia_existente' && empty($valor)) {
            mostrarMensajeYRedirigir(
                "❌ El campo $campo es obligatorio",
                "../interfaz/registroVehiculo.php",
                "error",
                $datos,
                $campo
            );
        }
    }
    $id_vehiculo = $_POST['id_vehiculo'] ?? null; // Para actualizar
    if (placaExiste($datos['placa'], $id_vehiculo)) {
        mostrarMensajeYRedirigir("❌ La placa ya está registrada", "../interfaz/registroVehiculo.php", "error", $datos, 'placa');
    }

    if (!preg_match('/^[A-Z0-9\-]{1,20}$/i', $datos['placa'])) {
        mostrarMensajeYRedirigir("❌ La placa no es válida", "../interfaz/registroVehiculo.php", "error", $datos, 'placa');
    }

    if (!is_numeric($datos['anno']) || $datos['anno'] < 1900 || $datos['anno'] > date('Y')) {
        mostrarMensajeYRedirigir("❌ Año no válido", "../interfaz/registroVehiculo.php", "error", $datos, 'anno');
    }

    if (!is_numeric($datos['asientos']) || $datos['asientos'] < 1) {
        mostrarMensajeYRedirigir("❌ Cantidad de asientos no válida", "../interfaz/registroVehiculo.php", "error", $datos, 'asientos');
    }
}

// Procesar foto del vehículo
function procesarFotografiaVehiculo($datos) {
    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return $datos['fotografia_existente'] ?? null;
    }

    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/vehiculos/';

    if (!is_dir($baseRuta)) mkdir($baseRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        mostrarMensajeYRedirigir("❌ Formato no permitido. Solo JPG, PNG o GIF", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    if ($archivo['size'] > 2*1024*1024) {
        mostrarMensajeYRedirigir("❌ La fotografía supera los 2MB", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    $nombreArchivo = preg_replace('/[^A-Za-z0-9]/', '', $datos['placa']) . '.' . $ext;
    $destino = $baseRuta . $nombreArchivo;

    if (file_exists($destino)) unlink($destino);

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        mostrarMensajeYRedirigir("❌ Error al guardar la fotografía", "../interfaz/registroVehiculo.php", "error", $datos, 'fotografia_existente');
    }

    return $destino;
}

function actualizarVehiculoDB($id_vehiculo, $datos, $foto) {
    if (empty($foto)) {
        $vehiculoDB = obtenerVehiculoPorId($id_vehiculo);
        $foto = $vehiculoDB['fotografia'] ?? null;
    }

    $resultado = actualizarVehiculo(
        $id_vehiculo, $datos['placa'], $datos['color'], $datos['marca'],
        $datos['modelo'], $datos['anno'], $datos['asientos'], $foto
    );

    if ($resultado) {
        mostrarMensajeYRedirigir("✅ Vehículo actualizado", "../interfaz/gestionVehiculos.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al actualizar", "../interfaz/registroVehiculo.php", "error", $datos);
    }
}

function registrarVehiculo($id_chofer, $datos, $foto) {
    $resultado = insertarVehiculo(
        $id_chofer, $datos['placa'], $datos['color'], $datos['marca'],
        $datos['modelo'], $datos['anno'], $datos['asientos'], $foto
    );

    if ($resultado) {
        mostrarMensajeYRedirigir("✅ Vehículo registrado", "../interfaz/gestionVehiculos.php", "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al registrar", "../interfaz/registroVehiculo.php", "error", $datos);
    }
}

function gestionarVehiculo($id_chofer) {
    $datos = obtenerDatosFormulario();
    validarDatos($datos);
    $datos['anno'] = (int)$datos['anno'];
    $datos['asientos'] = (int)$datos['asientos'];
    $foto = procesarFotografiaVehiculo($datos);

    $accion = $_POST['accion'] ?? 'guardar';

    if ($accion === 'actualizar' && !empty($_POST['id_vehiculo'])) {
        actualizarVehiculo($_POST['id_vehiculo'], $datos, $foto);
    } else {
        registrarVehiculo($id_chofer, $datos, $foto);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_chofer = $_SESSION['usuario']['id_usuario'] ?? null;
    gestionarVehiculo($id_chofer);
}else {
    die ('Acceso no permitido');
}
