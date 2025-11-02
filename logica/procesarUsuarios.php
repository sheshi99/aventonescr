<?php
session_start();
include_once("../datos/usuarios.php");
include_once("../utilidades/mensajes.php");


function obtenerDatosFormulario() {
    return [
        'id_usuario'           => $_POST['id_usuario'] ?? null,
        'nombre'               => trim($_POST['nombre'] ?? ''),
        'apellido'             => trim($_POST['apellido'] ?? ''),
        'cedula'               => trim($_POST['cedula'] ?? ''),
        'fecha_nacimiento'     => trim($_POST['fecha_nacimiento'] ?? ''),
        'correo'               => trim($_POST['correo'] ?? ''),
        'telefono'             => trim($_POST['telefono'] ?? ''),
        'rol'                  => $_POST['rol'] ?? null,
        'contrasena'           => $_POST['contrasena'] ?? '',
        'contrasena2'          => $_POST['contrasena2'] ?? '',
        'fotografia_existente' => $_POST['fotografia_existente'] ?? ''
    ];
}



function origenFormulario($datos) {
    $rol = $datos['rol'] ?? $_SESSION['usuario']['rol'] ?? '';
    return $rol === 'Administrador' ? "../interfaz/registroAdmin.php" 
                                    : "../interfaz/registroUsuario.php";
}


function panelSegunRol($datos) {
    // Obtener rol del formulario o de la sesión si no existe
    $rol = $datos['rol'] ?? $_SESSION['usuario']['rol'] ?? '';

    switch (strtolower($rol)) {
        case 'administrador':
            return "../interfaz/adminPanel.php";
        case 'chofer':
            return "../interfaz/choferPanel.php";
        case 'pasajero':
            return "../interfaz/pasajeroPanel.php";
        default:
            return "../interfaz/login.php"; // Por seguridad, si no hay rol
    }
}


// ----------------------------
// VALIDACIONES
// ----------------------------


function validarContrasena($contrasena, $contrasena2, $datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    if (!$id_usuario) { 
        if ($contrasena !== $contrasena2) {
            redirigirMsjUsuario(
                "❌ Las contraseñas no coinciden",
                origenFormulario($datos),
                "error",
                $datos,
                'contrasena2',
                $id_usuario,
                'insertar'
            );
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
            redirigirMsjUsuario(
                "❌ La contraseña debe tener al menos 8 caracteres, una letra, un número y un carácter especial",
                origenFormulario($datos),
                "error",
                $datos,
                'contrasena',
                $id_usuario,
                'insertar'
            );
        }
    }
}

function validarEdad($rol, $fechaNacimiento, $datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    $accion = $id_usuario ? 'actualizar' : 'insertar';

    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();

    if ($nacimiento >= $hoy) {
        redirigirMsjUsuario(
            "❌ La fecha de nacimiento no puede ser futura ni igual a hoy",
            origenFormulario($datos),
            "error",
            $datos,
            'fecha_nacimiento',
            $id_usuario,
            $accion
        );
    }

    $edad = $hoy->diff($nacimiento)->y;

    if (in_array(strtolower($rol), ['chofer','administrador']) && $edad < 18) {
        redirigirMsjUsuario(
            "❌ Debe tener al menos 18 años para registrarse como $rol",
            origenFormulario($datos),
            "error",
            $datos,
            'fecha_nacimiento',
            $id_usuario,
            $accion
        );
    }

    if (strtolower($rol) === 'pasajero' && $edad < 15) {
        redirigirMsjUsuario(
            "❌ Debe tener al menos 15 años para registrarse como pasajero",
            origenFormulario($datos),
            "error",
            $datos,
            'fecha_nacimiento',
            $id_usuario,
            $accion
        );
    }
}

function validarCedula($cedula, $datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    $accion = $id_usuario ? 'actualizar' : 'insertar';
    if (!preg_match('/^[0-9]{5,}$/', $cedula)) {
        redirigirMsjUsuario(
            "❌ La cédula debe contener al menos 5 números",
            origenFormulario($datos),
            "error",
            $datos,
            'cedula',
            $id_usuario,
            $accion
        );
    }
}

function validarTelefono($telefono, $datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    $accion = $id_usuario ? 'actualizar' : 'insertar';
    if (!preg_match('/^[0-9]{8,}$/', $telefono)) {
        redirigirMsjUsuario(
            "❌ El teléfono debe tener al menos 8 números",
            origenFormulario($datos),
            "error",
            $datos,
            'telefono',
            $id_usuario,
            $accion
        );
    }
}

function ejecutarValidaciones($datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    $accion = $id_usuario ? 'actualizar' : 'insertar';

    // Asignar rol por defecto si es nuevo usuario y no viene del formulario
    if (!$id_usuario && empty($datos['rol'])) {
        $datos['rol'] = 'Administrador'; // rol por defecto para administradores
    }

    // Campos obligatorios
    $campos = ['nombre','apellido','cedula','fecha_nacimiento','correo','telefono'];

    if (!$id_usuario) {
        // Solo validar contraseñas como obligatorias
        $campos[] = 'contrasena';
        $campos[] = 'contrasena2';
    }

    foreach ($campos as $campo) {
        if (empty($datos[$campo])) {
            redirigirMsjUsuario(
                "❌ El campo {$campo} es obligatorio",
                origenFormulario($datos),
                "error",
                $datos,
                $campo,
                $id_usuario,
                $accion
            );
        }
    }

    // Validaciones específicas
    validarCedula($datos['cedula'], $datos);
    validarTelefono($datos['telefono'], $datos);
    validarContrasena($datos['contrasena'], $datos['contrasena2'], $datos);

    // Validar edad
    $rolValidar = $datos['rol'];
    if ($id_usuario && empty($rolValidar)) {
        $usuarioExistente = obtenerUsuarioPorId($id_usuario);
        $rolValidar = $usuarioExistente['rol'] ?? 'Pasajero';
    }
    validarEdad($rolValidar, $datos['fecha_nacimiento'], $datos);

    // Verificar usuario existente solo al registrar
    if (!$id_usuario && verificarUsuarioExistente($datos['cedula'], $datos['correo'])) {
        redirigirMsjUsuario(
            "❌ Ya existe un usuario con esa cédula o correo",
            origenFormulario($datos),
            "error",
            $datos,
            'cedula',
            null,
            'insertar'
        );
    }

    // Fotografía obligatoria solo al registrar
    if (!$id_usuario && (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK)) {
        redirigirMsjUsuario(
            "❌ La fotografía es obligatoria",
            origenFormulario($datos),
            "error",
            $datos,
            'fotografia_existente',
            null,
            'insertar'
        );
    }

    return $datos; // devolvemos datos actualizados con rol por defecto
}


// ----------------------------
// PROCESAR FOTOGRAFÍA
// ----------------------------
function procesarFotografia($cedula, $nombre, $apellido, $rol, $datos) {
    $id_usuario = $datos['id_usuario'] ?? null;
    $accion = $id_usuario ? 'actualizar' : 'insertar';

    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return $datos['fotografia_existente'] ?? null;
    }

    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/';
    $rolRuta = $baseRuta . strtolower($rol) . '/';
    if (!is_dir($rolRuta)) mkdir($rolRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        redirigirMsjUsuario(
            "❌ Formato no permitido. Solo JPG, PNG o GIF",
            origenFormulario($datos),
            "error",
            $datos,
            'fotografia_existente',
            $id_usuario,
            $accion
        );
    }

    if ($archivo['size'] > 2*1024*1024) {
        redirigirMsjUsuario(
            "❌ La fotografía supera los 2MB",
            origenFormulario($datos),
            "error",
            $datos,
            'fotografia_existente',
            $id_usuario,
            $accion
        );
    }

    $nombreLimpio = preg_replace('/[^A-Za-z0-9]/', '', $nombre);
    $apellidoLimpio = preg_replace('/[^A-Za-z0-9]/', '', $apellido);
    $nombreArchivo = "{$cedula}_{$nombreLimpio}{$apellidoLimpio}.{$ext}";
    $destino = $rolRuta . $nombreArchivo;

    if (file_exists($destino)) unlink($destino);
    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        redirigirMsjUsuario(
            "❌ Error al guardar la fotografía",
            origenFormulario($datos),
            "error",
            $datos,
            'fotografia_existente',
            $id_usuario,
            $accion
        );
    }

    return $destino;
}

// ----------------------------
// ACTUALIZAR USUARIO
// ----------------------------
function actualizarUsuario($datos, $fotografia) {
    $id_usuario = $datos['id_usuario'];

    // Obtener rol si no está definido
    if (empty($datos['rol'])) {
        $usuarioExistente = obtenerUsuarioPorId($id_usuario);
        $datos['rol'] = $usuarioExistente['rol'] ?? '';
    }

    $resultado = editarUsuario(
        $id_usuario, $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $datos['rol'], $fotografia
    );

    // Actualizar datos de sesión si el usuario editado es el mismo que está logueado
    if ($_SESSION['usuario']['id_usuario'] == $id_usuario) {
        $_SESSION['usuario']['nombre'] = $datos['nombre'];
        $_SESSION['usuario']['apellido'] = $datos['apellido'];
        $_SESSION['usuario']['cedula'] = $datos['cedula'];
        $_SESSION['usuario']['correo'] = $datos['correo'];
        $_SESSION['usuario']['telefono'] = $datos['telefono'];
        $_SESSION['usuario']['fotografia'] = $fotografia;
    }

    // Mensaje según éxito o fallo
    if ($resultado['success']) {
        $_SESSION['mensaje'] = ['texto' => '✅ Usuario actualizado con éxito', 'tipo' => 'success'];
    } else {
        $_SESSION['mensaje'] = ['texto' => '❌ Error al actualizar usuario', 'tipo' => 'error'];
    }

    // Redirigir según rol
    header("Location: " . panelSegunRol($datos));
    exit;
}


// ----------------------------
// REGISTRAR USUARIO NUEVO
// ----------------------------
function registrarUsuario($datos, $fotografia) {
    $resultado = insertarUsuario(
        $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $fotografia, $datos['contrasena'], $datos['rol'] ?? 'Pasajero'
    );

    if ($resultado['success']) {
        // Mensaje según rol
        if (strtolower($datos['rol'] ?? '') === 'administrador') {
            $_SESSION['mensaje'] = ['texto' => '✅ Administrador registrado con éxito', 'tipo' => 'success'];
        } else {
            $_SESSION['mensaje'] = ['texto' => '✅ Usuario registrado. Recibirá un correo para activar la cuenta', 'tipo' => 'success'];
        }

        header("Location: " . panelSegunRol($datos, true));
        exit;
    } else {
        // Error, también usando panel según rol
        $_SESSION['mensaje'] = ['texto' => '❌ Ocurrió un error al registrar el usuario. Intente nuevamente.', 'tipo' => 'error'];
        header("Location: " . panelSegunRol($datos, true));
        exit;
    }
}


// ----------------------------
// FUNCION PRINCIPAL
// ----------------------------
function gestionarRegistro() {
    $datos = obtenerDatosFormulario();

    ejecutarValidaciones($datos);

    // Recuperar rol si es actualización y está vacío
    if (!empty($datos['id_usuario']) && empty($datos['rol'])) {
        $usuarioExistente = obtenerUsuarioPorId($datos['id_usuario']);
        $datos['rol'] = $usuarioExistente['rol'] ?? 'Pasajero';
    }

    $fotografia = procesarFotografia($datos['cedula'], $datos['nombre'], $datos['apellido'], $datos['rol'], $datos);

    if (!empty($datos['id_usuario'])) {
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
