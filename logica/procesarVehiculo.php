<?php
session_start();
include_once("../datos/vehiculos.php");

if (!isset($_SESSION['usuario']['id_usuario'])) {
    header("Location: ../interfaz/login.php");
    exit;
}

$id_chofer = $_SESSION['usuario']['id_usuario'];

// Mostrar mensaje y redirigir, pasando los datos del formulario
function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info', $datosFormulario = [], $campoError = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo, 'campo_error' => $campoError];
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
        'asientos'=> trim($_POST['asientos'] ?? '')
    ];
}

// Validar datos
function validarDatos($datos) {
    foreach ($datos as $campo => $valor) {
        if (empty($valor)) {
            mostrarMensajeYRedirigir(
                "❌ El campo $campo es obligatorio",
                "../interfaz/registroVehiculo.php",
                "error",
                $datos,
                $campo
            );
        }
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

// Procesar foto
function procesarFoto() {
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) return null;
    $archivo = $_FILES['foto'];
    $carpeta = '../imagenes_vehiculos/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);
    $nombreArchivo = time() . "_" . basename($archivo['name']);
    $destino = $carpeta . $nombreArchivo;
    move_uploaded_file($archivo['tmp_name'], $destino);
    return $destino;
}

// Guardar o actualizar vehículo
function guardarVehiculo($id_chofer) {
    $datos = obtenerDatosFormulario();
    validarDatos($datos);
    $foto = procesarFoto();

    $accion = $_POST['accion'] ?? 'guardar';
    if ($accion === 'actualizar' && !empty($_POST['id_vehiculo'])) {
        $id_vehiculo = $_POST['id_vehiculo'];
        $resultado = actualizarVehiculo($id_vehiculo, $datos['placa'], $datos['color'],$datos['marca'], 
                                        $datos['modelo'], $datos['anno'], $datos['asientos'], $foto);

        if ($resultado) mostrarMensajeYRedirigir("✅ Vehículo actualizado", "../interfaz/gestionVehiculos.php", "success");
        else mostrarMensajeYRedirigir("❌ Error al actualizar", "../interfaz/registroVehiculo.php", "error", $datos);
    } else {
        $resultado = insertarVehiculo($id_chofer, $datos['placa'], $datos['color'], $datos['marca'], 
                                      $datos['modelo'], $datos['anno'], $datos['asientos'], $foto);
        if ($resultado) mostrarMensajeYRedirigir("✅ Vehículo registrado", "../interfaz/gestionVehiculos.php", "success");
        else mostrarMensajeYRedirigir("❌ Error al registrar", "../interfaz/registroVehiculo.php", "error", $datos);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    guardarVehiculo($id_chofer);
} else {
    die("Acceso no permitido");
}
