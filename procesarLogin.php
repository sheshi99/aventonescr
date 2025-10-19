<?php
session_start();
include_once 'usuarios.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido.");
}

// Función principal que procesa el login
function procesarLogin() {
    $datos = obtenerDatosFormulario();

    $usuario = obtenerUsuarioPorCedula($datos['cedula']);
    if (!$usuario) {
        die("❌ Usuario no encontrado.");
    }

    if (!verificarContrasena($datos['contrasena'], $usuario['contrasena'])) {
        die("❌ Contraseña incorrecta.");
    }

    if (!verificarEstado($usuario['estado'])) {
        die("❌ Su cuenta no está activa.");
    }

    iniciarSesion($usuario);
}

// Función para obtener los datos del formulario
function obtenerDatosFormulario() {
    return [
        'cedula' => $_POST['cedula'] ?? '',
        'contrasena' => $_POST['contrasena'] ?? ''
    ];
}

// Función para verificar la contraseña
function verificarContrasena($contrasenaIngresada, $hashAlmacenado) {
    return password_verify($contrasenaIngresada, $hashAlmacenado);
}

// Función para verificar el estado de la cuenta
function verificarEstado($estado) {
    if ($estado === 'Pendiente') {
        echo "❌ Su cuenta está pendiente de activación.";
        return false;
    }
    if ($estado === 'Inactivo') {
        echo "❌ Su cuenta está inactiva.";
        return false;
    }
    return true;
}

// Función para iniciar sesión y redirigir según rol
function iniciarSesion($usuario) {
    $_SESSION['usuario'] = $usuario;

    switch ($usuario['rol']) {
        case 'Administrador':
            header("Location: adminPanel.php");
            exit;
        case 'Chofer':
            echo "Bienvenido Chofer: " . $usuario['nombre'];
            break;
        case 'Pasajero':
            echo "Bienvenido Pasajero: " . $usuario['nombre'];
            break;
        default:
            echo "Rol no reconocido.";
    }
}

procesarLogin();
