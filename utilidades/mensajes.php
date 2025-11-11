
<?php

/*
 * Archivo: mensajes.php
 * Autores: Seidy Alanis y Walbyn González
 * Funciones de ayuda para redirecciones y manejo de mensajes
 * para usuarios, rides y vehículos. Permiten mostrar alertas,
 * conservar datos de formularios y limpiar campos con errores.
 */


function redirigirMsjUsuario($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
                             $campoError = null, $idUsuario = null, $accion = null) {
    
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    
    if (isset($datosFormulario['contrasena'])) $datosFormulario['contrasena'] = '';
    if (isset($datosFormulario['contrasena2'])) $datosFormulario['contrasena2'] = '';

  
    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

    
    if ($idUsuario !== null) {
        $datosFormulario['id_usuario'] = $idUsuario;
    }
    if ($accion !== null) {
        $datosFormulario['accion'] = $accion;
    }

    
    $_SESSION['datos_formulario'] = $datosFormulario;

  
    header("Location: $destino");
    exit;
}



function valorUsuario($campo, $datosFormulario, $usuario) {
    return htmlspecialchars($datosFormulario[$campo] ?? $usuario[$campo] ?? '');
}


function redirigirMsjRide($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
                                 $campoError = null, $idRide = null, $accion = null) {
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo];

    
    if ($campoError && isset($datosFormulario[$campoError])) {
        $datosFormulario[$campoError] = '';
    }

   
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