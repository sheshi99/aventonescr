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

function verificarContrasena($contrasenaIngresada, $hashAlmacenado) {
    return password_verify($contrasenaIngresada, $hashAlmacenado);
}


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
            break;
        default:
            echo "Rol no reconocido.";
    }
}

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
        exit;
    }

    iniciarSesion($usuario);
}

procesarLogin();
