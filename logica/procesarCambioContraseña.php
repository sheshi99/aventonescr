<?php

/**
 * --------------------------------------------------------------
 * Archivo: procesarCambioContrasena.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción:
 * Controla el proceso de cambio de contraseña de los usuarios.
 * Valida la contraseña actual, revisa el formato de la nueva,
 * confirma coincidencias, y actualiza la información en la base
 * de datos según el rol del usuario.
 * --------------------------------------------------------------
 */
session_start();
include_once("../datos/usuarios.php");

/* ===== FUNCIONES ===== */

// Verifica que la contraseña actual coincida
function validarContrasenaActual($id_usuario, $contrasena_actual) {
    if (!confirmarContrasena($id_usuario, $contrasena_actual)) {
        return ['success' => false, 'mensaje' => '❌ Contraseña actual incorrecta.'];
    }
    return ['success' => true];
}

// Verifica que la nueva contraseña y su confirmación cumplan los requisitos
function validarNuevaContrasena($contrasena_actual, $nueva_contrasena, $confirmar_contrasena, $id_usuario) {

    // 1️⃣ Verificar contraseña actual
    if (!confirmarContrasena($id_usuario, $contrasena_actual)) {
        $_SESSION['mensaje'] = ['texto' => '❌ La contraseña actual es incorrecta', 'tipo' => 'error'];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    // 2️⃣ Evitar que la nueva sea igual a la actual
    if ($contrasena_actual === $nueva_contrasena) {
        $_SESSION['mensaje'] = ['texto' => '⚠️ La nueva contraseña no puede ser igual a la actual', 'tipo' => 'error'];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    // 3️⃣ Verificar coincidencia
    if ($nueva_contrasena !== $confirmar_contrasena) {
        $_SESSION['mensaje'] = ['texto' => '❌ Las nuevas contraseñas no coinciden', 'tipo' => 'error'];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    // 4️⃣ Validar formato seguro
    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $nueva_contrasena)) {
        $_SESSION['mensaje'] = [
            'texto' => '❌ La contraseña debe tener al menos 8 caracteres, una letra, un número y un carácter especial',
            'tipo' => 'error'
        ];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    return ['success' => true];
}

// Actualiza la contraseña del usuario
function cambiarContrasenaUsuario($id_usuario, $nueva_contrasena) {
    $resultado = actualizarContrasena($id_usuario, $nueva_contrasena);
    if ($resultado['success']) {
        return ['success' => true, 'mensaje' => '✅ Contraseña cambiada correctamente.'];
    }
    return ['success' => false, 'mensaje' => '❌ Error al actualizar la contraseña.'];
}

// Obtiene la URL de redirección según el rol del usuario
function redirigirSegunRol($id_usuario) {
    $usuario = obtenerUsuarioPorId($id_usuario);
    switch ($usuario['rol']) {
        case 'Administrador':
            return '../interfaz/adminPanel.php';
        case 'Chofer':
            return '../interfaz/choferPanel.php';
        case 'Pasajero':
            return '../interfaz/pasajeroPanel.php';
        default:
            return '../interfaz/login.php';
    }
}

/* ===== FUNCIÓN PRINCIPAL ===== */
function cambioContrasena() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    $id_usuario = $_POST['id_usuario'];
    $contrasena_actual = $_POST['contrasena_actual'];
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // 1️⃣ Validar contraseña actual
    $validacion = validarContrasenaActual($id_usuario, $contrasena_actual);
    if (!$validacion['success']) {
        $_SESSION['mensaje'] = ['texto' => $validacion['mensaje'], 'tipo' => 'error'];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    // 2️⃣ Validar nueva contraseña (formato, coincidencia y que no sea igual)
    $validacion = validarNuevaContrasena($contrasena_actual, $nueva_contrasena, $confirmar_contrasena, $id_usuario);
    if (!$validacion['success']) {
        $_SESSION['mensaje'] = ['texto' => $validacion['mensaje'], 'tipo' => 'error'];
        header("Location: ../interfaz/cambioContraseña.php");
        exit;
    }

    // 3️⃣ Cambiar contraseña
    $resultado = cambiarContrasenaUsuario($id_usuario, $nueva_contrasena);
    $_SESSION['mensaje'] = ['texto' => $resultado['mensaje'], 'tipo' => $resultado['success'] ? 'success' : 'error'];

    // 4️⃣ Redirigir según el rol si fue exitosa
    if ($resultado['success']) {
        header("Location: " . redirigirSegunRol($id_usuario));
    } else {
        header("Location: ../interfaz/cambioContraseña.php");
    }
    exit;
}

/* ===== EJECUTAR ===== */
cambioContrasena();
