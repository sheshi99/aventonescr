<?php

include_once ("../datos/usuarios.php");

if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];


$activado = activarUsuarioPorToken($token); 

if ($activado) {
    echo "✅ Cuenta activada correctamente. Ya puedes iniciar sesión.";
    
} else {
    echo "❌ Token inválido o ya usado.";
}

?>
