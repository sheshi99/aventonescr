<?php

include_once ("../datos/usuarios.php");

function obtenerDatosFormulario() {
  return [
        'nombre'            => trim($_POST['nombre'] ?? ''),
        'apellido'          => trim($_POST['apellido'] ?? ''),
        'cedula'            => trim($_POST['cedula'] ?? ''),
        'fecha_nacimiento'  => trim($_POST['fecha_nacimiento'] ?? ''),
        'correo'            => trim($_POST['correo'] ?? ''),
        'telefono'          => trim($_POST['telefono'] ?? ''),
        'rol'               => trim($_POST['rol'] ?? ''), 
        'contrasena'        => $_POST['contrasena'] ?? '',
        'contrasena2'       => $_POST['contrasena2'] ?? ''
    ];
}

function validarCamposObligatorios($datos) {
    
    if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['cedula']) || 
        empty($datos['fecha_nacimiento']) || empty($datos['correo']) || empty($datos['telefono']) || 
        empty($datos['rol']) || empty($datos['contrasena']) || empty($datos['contrasena2'])) 
    {
        return "Debe completar todos los campos del formulario."; 
    }

    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return "La Fotografía personal es obligatoria y debe ser subida correctamente.";
    }

    return true; 
}


function validarContrasenas($pass1, $pass2) {
    if ($pass1 !== $pass2) {
        return "Las contraseñas no coinciden.";
    }
    if (strlen($pass1) < 8) {
        return "La contraseña debe tener al menos 8 caracteres.";
    }
    if (!preg_match('/[A-Za-z]/', $pass1)) {
        return "La contraseña debe contener al menos una letra.";
    }
    if (!preg_match('/[0-9]/', $pass1)) {
        return "La contraseña debe contener al menos un número.";
    }
    if (!preg_match('/[\W_]/', $pass1)) {
        return "La contraseña debe contener al menos un carácter especial.";
    }
    return true;
}


function procesarFotografia() {
    if (!empty($_FILES['fotografia']['name'])) {
        $ruta = 'uploads/';
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $destino = $ruta . basename($_FILES['fotografia']['name']);
        move_uploaded_file($_FILES['fotografia']['tmp_name'], $destino);
        return $destino;
    }
    return null;
}



/*
function validarFotografia() {
    
    // 1. VERIFICACIÓN BÁSICA (Existencia y Errores de PHP)
    if (!isset($_FILES['fotografia']) || $_FILES['fotografia']['error'] !== UPLOAD_ERR_OK) {
        return "La Fotografía personal es obligatoria y debe ser subida correctamente.";
    }
    
    $file_info = $_FILES['fotografia'];
    
    // 2. Reglas
    $max_size = 2 * 1024 * 1024; // 2 MB
    $allowed_formats = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));

    // 3. Validación de TAMAÑO
    if ($file_info['size'] > $max_size) {
        return "El archivo es demasiado grande. El máximo permitido es 2MB."; 
    }

    // 4. Validación de FORMATO
    if (!in_array($file_extension, $allowed_formats)) {
        return "El formato de archivo no es válido. Solo se permiten JPG, PNG y GIF.";
    }

    return true; // Éxito total
}

*/

function validarUsuarioExistente($cedula, $correo) {
    if (verificarUsuarioExistente($cedula, $correo)) {
        echo "<script>
                alert('Ya existe un usuario registrado con esa cédula o correo.');
                history.back();
              </script>";
        return false;
    }
    return true;
}

function mostrarResultado($resultado) {
    if ($resultado['success']) {
        // Normaliza el rol (minúsculas y sin espacios)
        $rol = strtolower(trim($resultado['rol'] ?? ''));

        // Si el rol es 'administrador' o 'a', redirige al panel de admin
        $destino = ($rol === 'administrador')
            ? '../interfaz/adminPanel.php'
            : '../interfaz/login.php';

        echo "<script>
                alert('✅ Usuario registrado con éxito!');
                window.location.href = '{$destino}';
              </script>";
    } else {
        $error = json_encode($resultado['error']);
        echo "<script>
                alert('❌ Error al registrar usuario: ' + {$error});
                history.back();
              </script>";
    }
}



function ejecutarValidaciones($datos) {
    // 1. Validar campos de texto
    $error_obligatorios = validarCamposObligatorios($datos); 
    if ($error_obligatorios !== true) {
        echo "<script> alert('❌ {$error_obligatorios}'); history.back(); </script>";
        return false; 
    }

    // 2. Validar reglas de la fotografía (existencia, tamaño, formato)
    /*$error_foto = validarFotografia();
    if ($error_foto !== true) {
        echo "<script> alert('❌ {$error_foto}'); history.back(); </script>";
        return false;
    }*/

    // 3. Validar existencia de usuario
    if (!validarUsuarioExistente($datos['cedula'], $datos['correo'])) {
        return false; 
    }

    // 4. Validar contraseñas
    $validacion_pass = validarContrasenas($datos['contrasena'], $datos['contrasena2']);
    if ($validacion_pass !== true) {
        echo "<script> alert('❌ {$validacion_pass}'); history.back(); </script>";
        return false;
    }

    return true; // Éxito total en las validaciones
}


function procesarRegistro() {
    $datos = obtenerDatosFormulario();

    if (!ejecutarValidaciones($datos)) {
        return; 
    }

    $fotografia = procesarFotografia();
    
    if ($fotografia === null) { 
        echo "<script> alert('❌ Error al guardar la fotografía en el servidor. Intente de nuevo.'); 
        history.back(); </script>";
        return;
    }

    $resultado = insertarUsuario(
        $datos['nombre'], $datos['apellido'], $datos['cedula'],
        $datos['fecha_nacimiento'], $datos['correo'], $datos['telefono'],
        $fotografia, $datos['contrasena'], $datos['rol']
    );

    mostrarResultado($resultado);
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    procesarRegistro();
} else {
    echo "Acceso no permitido.";
}

// validación fecha de nacimiento 


?>

