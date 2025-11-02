
<?php


function origenFormulario($rol = '', $editar = false) {
    return ($rol === 'Administrador') ? "../interfaz/registroAdmin.php" 
                                     : "../interfaz/registroUsuario.php";
}

function redirigirMsjUsuario($mensaje, $tipo = 'info', $datosFormulario = [], 
    $campoError = null, $idUsuario = null, $accion = null
) {
    // Guardar mensaje en sesión
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    // Limpiar contraseñas si hubo error
    if (in_array($campoError, ['contrasena','contrasena2'])) {
        $datosFormulario['contrasena'] = '';
        $datosFormulario['contrasena2'] = '';
    }

    // Limpiar solo el campo específico si es otro error
    if ($campoError && !in_array($campoError, ['contrasena','contrasena2']) && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    // Guardar ID y acción si se proporcionan
    if ($idUsuario) {
        $datosFormulario['id_usuario'] = $idUsuario;
    }
    if ($accion) {
        $datosFormulario['editar'] = ($accion === 'actualizar');
    }

    // Guardar datos del formulario en sesión
    $_SESSION['datos_formulario'] = $datosFormulario;

    // Determinar rol y si es edición
    $rol = $datosFormulario['rol'] ?? '';
    $editar = $datosFormulario['editar'] ?? false;

    // Redirigir al formulario correcto según rol y edición
    header("Location: " . origenFormulario($rol, $editar));
    exit;
}



function redirigirMsjRide($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
                                 $campoError = null, $idRide = null, $accion = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    // Si se indica un campo con error, lo limpiamos del formulario
    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    // Guardar ID y ACCIÓN en los datos del formulario
    if ($idRide) {
        $datosFormulario['id_ride'] = $idRide;
    }
    if ($accion) {
        $datosFormulario['accion'] = $accion;
    }

    $_SESSION['datos_formulario'] = $datosFormulario;
    header("Location: $destino");
    exit;
}


function redirigirMsjVehiculo($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
                              $campoError = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo, 'campo_error' => $campoError];

    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    $_SESSION['datos_formulario'] = $datosFormulario;
    header("Location: $destino");
    exit;
}

?>