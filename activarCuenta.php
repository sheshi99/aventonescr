<?php
include_once 'usuarios.php';

if (!isset($_GET['token'])) {
    die("Token no proporcionado.");
}

$token = $_GET['token'];
$activado = activarUsuarioPorToken($token);

if ($activado) {
    echo "✅ Cuenta activada correctamente. Ya puedes iniciar sesión.";
    // Opcional: redirigir automáticamente al login
    echo "<script>setTimeout(()=>{window.location.href='login.php';}, 3000);</script>";
} else {
    echo "❌ Token inválido o ya usado.";
}
?>
