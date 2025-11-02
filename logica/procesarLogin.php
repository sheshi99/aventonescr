<?php
session_start();
include_once ("../datos/usuarios.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no permitido.");
}

function obtenerDatosFormulario() {
    return [
        'cedula' => $_POST['cedula'] ?? '',
        'contrasena' => $_POST['contrasena'] ?? ''
    ];
}

// Guarda el mensaje en sesión y detiene la ejecución
function Mensaje($mensaje, $tipo = 'error') {
    $_SESSION['mensaje_login'] = ['texto' => $mensaje, 'tipo' => $tipo];
    header("Location: ../interfaz/login.php"); // Redirige al login
    exit;
}

// Verifica la contraseña usando hash
function verificarContrasena($contrasenaIngresada, $hashAlmacenado) {
    return password_verify($contrasenaIngresada, $hashAlmacenado);
}

// Verifica el estado del usuario
function verificarEstado($estado) {
    if ($estado === 'Pendiente') {
        Mensaje("Su cuenta está pendiente de activación.");
    }
    if ($estado === 'Inactivo') {
        Mensaje("Su cuenta está inactiva.");
    }
    return true;
}

// Inicia la sesión y redirige según rol
function iniciarSesion($usuario) {
    $_SESSION['usuario'] = $usuario;

    switch ($usuario['rol']) {
        case 'Administrador':
            header("Location: ../interfaz/adminPanel.php");
            exit;
        case 'Chofer':
            header("Location: ../interfaz/choferPanel.php");
            exit;
        case 'Pasajero':
            header("Location: ../interfaz/pasajeroPanel.php");
            exit;
        default:
            Mensaje("Rol no reconocido.");
    }
}

// Procesa el login
function procesarLogin() {
    $datos = obtenerDatosFormulario();

    $usuario = obtenerUsuarioPorCedula($datos['cedula']);
    if (!$usuario) {
        Mensaje("Usuario no encontrado.");
    }

    if (!verificarContrasena($datos['contrasena'], $usuario['contrasena'])) {
        Mensaje("Contraseña incorrecta.");
    }

    verificarEstado($usuario['estado']);

    iniciarSesion($usuario);
}

procesarLogin();
