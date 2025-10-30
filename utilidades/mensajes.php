
<?php

function mostrarMensajeYRedirigir($mensaje, $destino, $tipo = 'info', $datosFormulario = [], 
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
?>