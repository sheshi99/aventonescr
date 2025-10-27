<?php

session_start();
include_once("../datos/usuarios.php");

// ----------------------------
// OBTENER DATOS DEL FORMULARIO
// ----------------------------
function obtenerDatosFormulario() {
    return [
        'editar'           => !empty($_POST['editar']),
        'id_usuario'       => $_POST['id_usuario'] ?? null,
        'nombre'           => trim($_POST['nombre'] ?? ''),
        'apellido'         => trim($_POST['apellido'] ?? ''),
        'cedula'           => trim($_POST['cedula'] ?? ''),
        'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
        'correo'           => trim($_POST['correo'] ?? ''),
        'telefono'         => trim($_POST['telefono'] ?? ''),
        // Solo asignar rol si es registro, en edición se mantiene el rol existente
        'rol'              => $_POST['rol'] ?? null,
        'contrasena'       => $_POST['contrasena'] ?? '',
        'contrasena2'      => $_POST['contrasena2'] ?? '',
        'fotografia_existente' => $_POST['fotografia_existente'] ?? ''
    ];
}

// ----------------------------
// ORIGEN DEL FORMULARIO
// ----------------------------
function origenFormulario($rol = '', $editar = false) {
    if ($editar) return "../interfaz/registroAdmin.php?editar=1";
    return ($rol === 'Administrador') ? "../interfaz/registroAdmin.php" : "../interfaz/registroUsuario.php";
}

// ----------------------------
// MENSAJE Y REDIRECCIÓN
// ----------------------------
function mostrarMensajeYRedirigir($mensaje, $tipo = 'info', $datos = [], $campoError = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    // Limpiar contraseñas si hay error en ellas
    if (in_array($campoError, ['contrasena','contrasena2'])) {
        $datos['contrasena'] = '';
        $datos['contrasena2'] = '';
    }

    // Limpiar campo específico si es otro error
    if ($campoError && !in_array($campoError, ['contrasena','contrasena2']) && isset($datos[$campoError])) {
        $datos[$campoError] = '';
    }

    $_SESSION['form_data'] = $datos;
    header("Location: " . origenFormulario($datos['rol'] ?? '', $datos['editar'] ?? false));
    exit;
}

// ----------------------------
// VALIDACIONES
// ----------------------------
function validarContrasena($contrasena, $contrasena2, $datos) {
    if (!$datos['editar']) {
        if ($contrasena !== $contrasena2) {
            mostrarMensajeYRedirigir("❌ Las contraseñas no coinciden", "error", $datos, 'contrasena2');
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
            mostrarMensajeYRedirigir(
                "❌ La contraseña debe tener al menos 8 caracteres, una letra, un número y un carácter especial",
                "error", $datos, 'contrasena'
            );
        }
    }
}

function validarEdad($rol, $fechaNacimiento, $datos) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();

    if ($nacimiento >= $hoy) {
        mostrarMensajeYRedirigir("❌ La fecha de nacimiento no puede ser futura ni igual a hoy", "error", $datos, 'fecha_nacimiento');
    }

    $edad = $hoy->diff($nacimiento)->y;

    if (in_array(strtolower($rol), ['chofer','administrador']) && $edad < 18) {
        mostrarMensajeYRedirigir("❌ Debe tener al menos 18 años para registrarse como $rol", "error", $datos, 'fecha_nacimiento');
    }

    if (strtolower($rol) === 'pasajero' && $edad < 15) {
        mostrarMensajeYRedirigir("❌ Debe tener al menos 15 años para registrarse como pasajero", "error", $datos, 'fecha_nacimiento');
    }
}

function validarCedula($cedula, $datos) {
    if (!preg_match('/^[0-9]{5,}$/', $cedula)) {
        mostrarMensajeYRedirigir("❌ La cédula debe contener al menos 5 números", "error", $datos, 'cedula');
    }
}

function validarTelefono($telefono, $datos) {
    if (!preg_match('/^[0-9]{8,}$/', $telefono)) {
        mostrarMensajeYRedirigir("❌ El teléfono debe tener al menos 8 números", "error", $datos, 'telefono');
    }
}

function ejecutarValidaciones($datos) {
    $campos = ['nombre','apellido','cedula','fecha_nacimiento','correo','telefono'];

    if (!$datos['editar']) {
        $campos[] = 'rol';
        $campos[] = 'contrasena';
        $campos[] = 'contrasena2';
    }

    foreach ($campos as $campo) {
        if (empty($datos[$campo])) {
            mostrarMensajeYRedirigir("❌ El campo {$campo} es obligatorio", "error", $datos, $campo);
        }
    }

    validarCedula($datos['cedula'], $datos);
    validarTelefono($datos['telefono'], $datos);
    validarContrasena($datos['contrasena'], $datos['contrasena2'], $datos);

    // Edad
    $rolValidar = $datos['rol'];
    if ($datos['editar'] && empty($rolValidar)) {
        $usuarioExistente = obtenerUsuarioPorId($datos['id_usuario']);
        $rolValidar = $usuarioExistente['rol'] ?? 'Pasajero';
    }
    validarEdad($rolValidar, $datos['fecha_nacimiento'], $datos);

    // Verificar usuario existente solo al registrar
    if (!$datos['editar'] && verificarUsuarioExistente($datos['cedula'], $datos['correo'])) {
        mostrarMensajeYRedirigir("❌ Ya existe un usuario con esa cédula o correo", "error", $datos, 'cedula');
    }

    // Fotografía obligatoria solo al registrar
    if (!$datos['editar'] && (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK)) {
        mostrarMensajeYRedirigir("❌ La fotografía es obligatoria", "error", $datos, 'fotografia_existente');
    }
}

// ----------------------------
// PROCESAR FOTOGRAFÍA
// ----------------------------
function procesarFotografia($cedula, $nombre, $apellido, $rol, $datos) {
    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return $datos['fotografia_existente'] ?? null;
    }

    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/';
    $rolRuta = $baseRuta . strtolower($rol) . '/';
    if (!is_dir($rolRuta)) mkdir($rolRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        mostrarMensajeYRedirigir("❌ Formato no permitido. Solo JPG, PNG o GIF", "error", $datos, 'fotografia_existente');
    }

    if ($archivo['size'] > 2*1024*1024) {
        mostrarMensajeYRedirigir("❌ La fotografía supera los 2MB", "error", $datos, 'fotografia_existente');
    }

    $nombreLimpio = preg_replace('/[^A-Za-z0-9]/', '', $nombre);
    $apellidoLimpio = preg_replace('/[^A-Za-z0-9]/', '', $apellido);
    $nombreArchivo = "{$cedula}_{$nombreLimpio}{$apellidoLimpio}.{$ext}";
    $destino = $rolRuta . $nombreArchivo;

    if (file_exists($destino)) unlink($destino);
    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        mostrarMensajeYRedirigir("❌ Error al guardar la fotografía", "error", $datos, 'fotografia_existente');
    }

    return $destino;
}

// ----------------------------
// ACTUALIZAR USUARIO
// ----------------------------
function actualizarUsuario($datos, $fotografia) {
    if (empty($datos['rol'])) {
        $usuarioExistente = obtenerUsuarioPorId($datos['id_usuario']);
        $datos['rol'] = $usuarioExistente['rol'] ?? 'Pasajero';
    }

    $resultado = editarUsuario(
        $datos['id_usuario'], $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $datos['rol'], $fotografia
    );

    if ($resultado['success']) {
        if ($_SESSION['usuario']['id_usuario'] == $datos['id_usuario']) {
            $_SESSION['usuario']['nombre'] = $datos['nombre'];
            $_SESSION['usuario']['apellido'] = $datos['apellido'];
            $_SESSION['usuario']['cedula'] = $datos['cedula'];
            $_SESSION['usuario']['correo'] = $datos['correo'];
            $_SESSION['usuario']['telefono'] = $datos['telefono'];
            $_SESSION['usuario']['fotografia'] = $fotografia;
        }

        $_SESSION['mensaje'] = ['texto' => '✅ Usuario actualizado con éxito', 'tipo' => 'success'];
        header("Location: ../interfaz/gestionVehiculos.php");
        exit;
    } else {
        mostrarMensajeYRedirigir("❌ Error al actualizar usuario", "error", $datos);
    }
}

// ----------------------------
// REGISTRAR USUARIO NUEVO
// ----------------------------
function registrarUsuario($datos, $fotografia) {
    $datos['rol'] = $datos['rol'] ?? 'Pasajero';
    $resultado = insertarUsuario(
        $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $fotografia, $datos['contrasena'], $datos['rol']
    );

    if ($resultado['success']) {
        if (strtolower($datos['rol']) === 'administrador') {
            mostrarMensajeYRedirigir("✅ Usuario registrado con éxito", "success", $datos);
        } else {
            $_SESSION['mensaje'] = ['texto'=>'✅ Usuario registrado con éxito','tipo'=>'success'];
            header("Location: ../interfaz/login.php");
            exit;
        }
    } else {
        mostrarMensajeYRedirigir("❌ Error al registrar usuario", "error", $datos);
    }
}

// ----------------------------
// FUNCION PRINCIPAL
// ----------------------------
function gestionarRegistro() {
    $datos = obtenerDatosFormulario();
    ejecutarValidaciones($datos);
    $fotografia = procesarFotografia($datos['cedula'], $datos['nombre'], $datos['apellido'], $datos['rol'] ?? 'Pasajero', $datos);

    if ($datos['editar']) {
        actualizarUsuario($datos, $fotografia);
    } else {
        registrarUsuario($datos, $fotografia);
    }
}

// ----------------------------
// EJECUCIÓN
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    gestionarRegistro();
} else {
    die ('Acceso no permitido');
}