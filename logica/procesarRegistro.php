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
        mostrarMensajeYRedirigir("❌ Las contraseñas no coinciden", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
        mostrarMensajeYRedirigir("❌ La contraseña debe tener al menos 8 caracteres, una letra,
         un número y un carácter especial", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }
}

function calcularEdad($fechaNacimiento) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    return $hoy->diff($nacimiento)->y;
}

function validarEdad($rol, $fechaNacimiento) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();

    // Validar que no sea una fecha futura
    if ($nacimiento >= $hoy) {
        mostrarMensajeYRedirigir("❌ La fecha de nacimiento no puede ser futura ni igual a hoy", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }

    $edad = $hoy->diff($nacimiento)->y;

    if (in_array(strtolower($rol), ['chofer', 'administrador'])) {
        if ($edad < 18) {
            mostrarMensajeYRedirigir("❌ Debe tener al menos 18 años para registrarse como $rol", 
            "../interfaz/registroUsuarioAdmin.php", "error");
        }
    } elseif (strtolower($rol) === 'pasajero') {
        if ($edad < 15) {
            mostrarMensajeYRedirigir("❌ Debe tener al menos 15 años para registrarse como pasajero", "../interfaz/registroUsuarioAdmin.php", "error");
        }
    }
}

function validarCedula($cedula) {
    if (!preg_match('/^[0-9]{5,}$/', $cedula)) {
        mostrarMensajeYRedirigir("❌ La cédula debe contener al menos 5 números", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }
}

function validarTelefono($telefono) {
    if (!preg_match('/^[0-9]{8,}$/', $telefono)) {
        mostrarMensajeYRedirigir("❌ El teléfono debe tener al menos 8 números", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }
}

function ejecutarValidaciones($datos) {
    $campos = ['nombre','apellido','cedula','fecha_nacimiento','correo','telefono','rol','contrasena','contrasena2'];

    foreach ($campos as $campo) {
        if (empty($datos[$campo])) {
            mostrarMensajeYRedirigir("❌ Debe completar todos los campos", 
            "../interfaz/registroUsuarioAdmin.php", "error");
        }
    }

    validarCedula($datos['cedula']);
    validarTelefono($datos['telefono']);
    validarContrasena($datos['contrasena'], $datos['contrasena2']);
    validarEdad($datos['rol'], $datos['fecha_nacimiento']);

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


function fotoExiste($archivoTmp) {
    $baseRuta = 'uploads/';
    foreach (glob($baseRuta . '*/*') as $fotoExistente) {
        if (is_file($fotoExistente)) {
            if (md5_file($fotoExistente) === md5_file($archivoTmp)) {
                return true;
            }
        }
    }
    return false;
}


function procesarFotografia($cedula, $nombre, $apellido, $rol) {
    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/';
    $rolRuta = $baseRuta . strtolower($rol) . '/';

    if (!is_dir($rolRuta)) mkdir($rolRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $permitidas)) {
        mostrarMensajeYRedirigir("❌ Formato no permitido. Solo JPG, PNG o GIF", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }

    if ($archivo['size'] > 2*1024*1024) {
        mostrarMensajeYRedirigir("❌ La fotografía supera los 2MB", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }

    // Validar si la foto ya existe (por contenido)
    if (fotoExiste($archivo['tmp_name'])) {
        mostrarMensajeYRedirigir("❌ Ya existe una fotografía idéntica en el sistema. Suba otra.", 
        "../interfaz/registroUsuarioAdmin.php", "error");
    }

    $nombreLimpio = preg_replace('/[^A-Za-z0-9]/', '', $nombre);
    $apellidoLimpio = preg_replace('/[^A-Za-z0-9]/', '', $apellido);
    $nombreArchivo = "{$cedula}_{$nombreLimpio}{$apellidoLimpio}.{$ext}";
    $destino = $rolRuta . $nombreArchivo;

    if (file_exists($destino)) {
        unlink($destino);
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
    $fotografia = procesarFotografia($datos['cedula'], $datos['nombre'], $datos['apellido'], $datos['rol']
    );

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
        mostrarMensajeYRedirigir("❌ Error al registrar usuario", "../interfaz/registroUsuarioAdmin.php", "error");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registrar();
} else {
    echo "Acceso no permitido.";
}
?>



