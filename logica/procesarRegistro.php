<?php
session_start();
include_once("../datos/usuarios.php");


function origenFormulario() {
    return "../interfaz/registroUsuario.php";
}

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

function mostrarMensajeYRedirigir($mensaje, $tipo = 'info', $datos = []) {
    $_SESSION['mensaje'] = [
        'texto' => $mensaje,
        'tipo'  => $tipo
    ];

    if (!empty($datos)) {
        $_SESSION['form_data'] = $datos;
    }

    header("Location: " . origenFormulario());
    exit;
}

function validarContrasena($contrasena, $contrasena2, $datos) {
    if ($contrasena !== $contrasena2) {
        mostrarMensajeYRedirigir("❌ Las contraseñas no coinciden", "error", $datos);
    }

    if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $contrasena)) {
        mostrarMensajeYRedirigir(
            "❌ La contraseña debe tener al menos 8 caracteres, una letra, un número y un carácter especial", 
            "error", $datos
        );
    }
}

function validarEdad($rol, $fechaNacimiento, $datos) {
    $nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();

    if ($nacimiento >= $hoy) {
        mostrarMensajeYRedirigir("❌ La fecha de nacimiento no puede ser futura ni igual a hoy", "error", $datos);
    }

    $edad = $hoy->diff($nacimiento)->y;

    if (in_array(strtolower($rol), ['chofer', 'administrador']) && $edad < 18) {
        mostrarMensajeYRedirigir("❌ Debe tener al menos 18 años para registrarse como $rol", "error", $datos);
    }

    if (strtolower($rol) === 'pasajero' && $edad < 15) {
        mostrarMensajeYRedirigir("❌ Debe tener al menos 15 años para registrarse como pasajero", 
        "error", $datos);
    }
}

function validarCedula($cedula, $datos) {
    if (!preg_match('/^[0-9]{5,}$/', $cedula)) {
        mostrarMensajeYRedirigir("❌ La cédula debe contener al menos 5 números",
         "error", $datos);
    }
}

function validarTelefono($telefono, $datos) {
    if (!preg_match('/^[0-9]{8,}$/', $telefono)) {
        mostrarMensajeYRedirigir("❌ El teléfono debe tener al menos 8 números", 
        "error", $datos);
    }
}

function ejecutarValidaciones($datos) {
    $campos = ['nombre','apellido','cedula','fecha_nacimiento','correo','telefono',
    'rol','contrasena','contrasena2'];

    foreach ($campos as $campo) {
        if (empty($datos[$campo])) {
            mostrarMensajeYRedirigir("❌ Debe completar todos los campos", "error", $datos);
        }
    }

    validarCedula($datos['cedula'], $datos);
    validarTelefono($datos['telefono'], $datos);
    validarContrasena($datos['contrasena'], $datos['contrasena2'], $datos);
    validarEdad($datos['rol'], $datos['fecha_nacimiento'], $datos);

    if (verificarUsuarioExistente($datos['cedula'], $datos['correo'])) {
        mostrarMensajeYRedirigir("❌ Ya existe un usuario con esa cédula o correo", 
        "error", $datos);
    }

    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        mostrarMensajeYRedirigir("❌ La fotografía es obligatoria", "error", $datos);
    }

    return true;
}

function fotoExiste($archivoTmp) {
    $baseRuta = 'uploads/';
    foreach (glob($baseRuta . '*/*') as $fotoExistente) {
        if (is_file($fotoExistente) && md5_file($fotoExistente) === md5_file($archivoTmp)) {
            return true;
        }
    }
    return false;
}

function procesarFotografia($cedula, $nombre, $apellido, $rol, $datos) {
    $archivo = $_FILES['fotografia'];
    $baseRuta = 'uploads/';
    $rolRuta = $baseRuta . strtolower($rol) . '/';

    if (!is_dir($rolRuta)) mkdir($rolRuta, 0777, true);

    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','gif'];
    if (!in_array($ext, $permitidas)) {
        mostrarMensajeYRedirigir("❌ Formato no permitido. Solo JPG, PNG o GIF", "error", $datos);
    }

    if ($archivo['size'] > 2*1024*1024) {
        mostrarMensajeYRedirigir("❌ La fotografía supera los 2MB", "error", $datos);
    }

    if (fotoExiste($archivo['tmp_name'])) {
        mostrarMensajeYRedirigir("❌ Ya existe una fotografía idéntica en el sistema. 
        Suba otra.", "error", $datos);
    }

    $nombreLimpio = preg_replace('/[^A-Za-z0-9]/', '', $nombre);
    $apellidoLimpio = preg_replace('/[^A-Za-z0-9]/', '', $apellido);
    $nombreArchivo = "{$cedula}_{$nombreLimpio}{$apellidoLimpio}.{$ext}";
    $destino = $rolRuta . $nombreArchivo;

    if (file_exists($destino)) unlink($destino);

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        mostrarMensajeYRedirigir("❌ Error al guardar la fotografía", "error", $datos);
    }

    return $destino;
}

function registrar() {
    $datos = obtenerDatosFormulario();
    ejecutarValidaciones($datos);

    $fotografia = procesarFotografia($datos['cedula'], $datos['nombre'], 
    $datos['apellido'], $datos['rol'], $datos);

    $resultado = insertarUsuario(
        $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $fotografia, $datos['contrasena'], $datos['rol']
    );

    if ($resultado['success']) {

        $destino = (isset($_SESSION['usuario']['rol']) && 
        $_SESSION['usuario']['rol'] === 'Administrador')
            ? '../interfaz/adminPanel.php?filtro_rol=Administrador'
            : '../interfaz/login.php';

        echo "<script>
                alert('✅ Usuario registrado con éxito');
                window.location.href='$destino';
            </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Error al registrar usuario');
                window.history.back();
            </script>";
        exit;
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    registrar();
} else {
    echo "Acceso no permitido.";
}
?>
