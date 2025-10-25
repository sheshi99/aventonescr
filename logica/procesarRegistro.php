<?php
session_start();
include_once("../datos/usuarios.php");


function obtenerDatosFormulario() {
    return [
        'nombre'           => trim($_POST['nombre'] ?? ''),
        'apellido'         => trim($_POST['apellido'] ?? ''),
        'cedula'           => trim($_POST['cedula'] ?? ''),
        'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
        'correo'           => trim($_POST['correo'] ?? ''),
        'telefono'         => trim($_POST['telefono'] ?? ''),
        'rol'              => trim($_POST['rol'] ?? ''), 
        'contrasena'       => $_POST['contrasena'] ?? '',
        'contrasena2'      => $_POST['contrasena2'] ?? ''
    ];
}


function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info') {
    $_SESSION['mensaje'] = [
        'texto' => $mensaje,
        'tipo'  => $tipo
    ];
    header("Location: $destino");
    exit;
}


function validarContrasena($contrasena, $contrasena2) {
    if ($contrasena !== $contrasena2) {
        mostrarMensajeYRedirigir(
            "❌ Las contraseñas no coinciden", 
            "../interfaz/registroUsuarioAdmin.php", 
            "error"
        );
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
        mostrarMensajeYRedirigir(
            "❌ La contraseña debe tener al menos 8 caracteres, una letra, un número 
            y un carácter especial", "../interfaz/registroUsuarioAdmin.php", 
            "error"
        );
    }
}


function ejecutarValidaciones($datos) {
    $campos = ['nombre','apellido','cedula','fecha_nacimiento','correo',
               'telefono','rol','contrasena','contrasena2'];

    foreach ($campos as $campo) {
        if (empty($datos[$campo])) {
            mostrarMensajeYRedirigir("❌ Debe completar todos los campos", 
                "../interfaz/registroUsuarioAdmin.php", "error");
        }
    }

    validarContrasena($datos['contrasena'], $datos['contrasena2']);

    if (verificarUsuarioExistente($datos['cedula'], $datos['correo'])) {
        mostrarMensajeYRedirigir("❌ Ya existe un usuario con esa cédula o correo", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }

    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        mostrarMensajeYRedirigir("❌ La fotografía es obligatoria", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }

    return true;
}


function procesarFotografia() {
    $archivo = $_FILES['fotografia'];
    $nombre = basename($archivo['name']);
    $ruta = 'uploads/';
    if (!is_dir($ruta)) mkdir($ruta, 0777, true);
    $destino = $ruta . $nombre;

    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','gif'];
    if (!in_array($ext, $permitidas)) {
        mostrarMensajeYRedirigir("❌ Formato no permitido. Solo JPG, PNG o GIF", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }

    if ($archivo['size'] > 2*1024*1024) {
        mostrarMensajeYRedirigir("❌ La fotografía supera los 2MB", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        mostrarMensajeYRedirigir("❌ Error al guardar la fotografía", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }

    return $destino;
}


function registrar() {
    $datos = obtenerDatosFormulario();
    ejecutarValidaciones($datos);
    $fotografia = procesarFotografia();

    $resultado = insertarUsuario(
        $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $fotografia, $datos['contrasena'], $datos['rol']
    );

    if ($resultado['success']) {
        $destino = strtolower(trim($datos['rol'])) === 'administrador'
            ? '../interfaz/adminPanel.php'
            : '../interfaz/login.php';
        mostrarMensajeYRedirigir("✅ Usuario registrado con éxito!", $destino, "success");
    } else {
        mostrarMensajeYRedirigir("❌ Error al registrar usuario", 
            "../interfaz/registroUsuarioAdmin.php", "error");
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registrar();
} else {
    echo "Acceso no permitido.";
}
?>


