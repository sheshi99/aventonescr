
<?php


function redirigirMsjUsuario($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
                             $campoError = null, $idUsuario = null, $accion = null) {
    // Guardar mensaje en sesión
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    // Limpiar contraseñas por seguridad
    if (isset($datosFormulario['contrasena'])) $datosFormulario['contrasena'] = '';
    if (isset($datosFormulario['contrasena2'])) $datosFormulario['contrasena2'] = '';

    // Limpiar campo con error si existe
    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    // Guardar ID y acción si se proporcionan
    if ($idUsuario !== null) {
        $datosFormulario['id_usuario'] = $idUsuario;
    }
    if ($accion !== null) {
        $datosFormulario['accion'] = $accion;
    }

    // Guardar datos del formulario en sesión
    $_SESSION['datos_formulario'] = $datosFormulario;

    // Redirigir
    header("Location: $destino");
    exit;
}



function valorUsuario($campo, $datosFormulario, $usuario) {
    return htmlspecialchars($datosFormulario[$campo] ?? $usuario[$campo] ?? '');
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