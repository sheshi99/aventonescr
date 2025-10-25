<?php
session_start();

// Destruir toda la sesión
session_unset(); // limpia todas las variables de sesión
session_destroy(); // destruye la sesión

// Redirigir al login u otra página pública
header("Location: ../interfaz/login.php");
exit;
?>
