<?php
include_once 'usuarios.php';

function obtenerDatosFormulario() {
    return [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'cedula' => $_POST['cedula'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'rol' => $_POST['rol'] ?? '',
        'contrasena' => $_POST['contrasena'] ?? '',
        'contrasena2' => $_POST['contrasena2'] ?? ''
    ];
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

function mostrarResultado($resultado) {
    if ($resultado['success']) {
        echo "<script>
                alert('✅ Usuario registrado con éxito!'); 
             
              </script>";
    } else {
        $error = addslashes($resultado['error']); 
        echo "<script>
                alert('❌ Error al registrar usuario: {$error}');
              </script>";
    }
}

function procesarRegistro() {
    $datos = obtenerDatosFormulario();

    if (!validarContrasenas($datos['contrasena'], $datos['contrasena2'])) {
        die("Las contraseñas no coinciden.");
    }

    $fotografia = procesarFotografia();

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

?>

