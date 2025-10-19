<?php
include_once 'usuarios.php';
session_start();

// Verificar admin logueado
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Administrador') {
    die("Acceso no permitido.");
}

// Procesar filtro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = $_POST['rol_filtro'] ?? '';
    if ($rol === 'Chofer' || $rol === 'Pasajero') {
        // Obtener usuarios según el rol
        $usuarios = listarUsuariosPorRol($rol);
        
        // Guardar los resultados en sesión temporal
        $_SESSION['usuarios_filtrados'] = $usuarios;
        $_SESSION['rol_filtrado'] = $rol;

        // Redirigir de nuevo al panel
        header("Location: panelAdmin.php");
        exit;
    } else {
        die("Rol inválido.");
    }
} else {
    die("Acceso no permitido.");
}
