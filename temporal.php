<?php
include_once 'usuarios.php';

$usuario = obtenerUsuarioPorCedula('123456789');
var_dump($usuario['contrasena']);
