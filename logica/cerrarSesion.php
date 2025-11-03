<?php


/*
 * Archivo: cerrarSesion.php
 * Autores: Seidy Alanis y Walbyn González
 * Descripción: Elimina todas las variables de sesión y cierra la sesión del usuario, 
 * luego redirige al login.
 */


session_start();

session_unset(); 
session_destroy(); 

header("Location: ../interfaz/login.php");
exit;
?>
