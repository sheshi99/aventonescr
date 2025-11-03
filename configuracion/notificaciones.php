<?php

/*
 * --------------------------------------------------------------
 * Archivo: notificaciones.php
 * Autor: Seidy Alanis y Walbyn González
 * 
 * Descripción:
 * Script de consola que busca reservas pendientes que llevan más
 * de X minutos sin respuesta y envía correos electrónicos a los
 * choferes correspondientes notificándoles sobre dichas reservas.
 * Permite pasar los minutos como parámetro o ingresarlos manualmente
 * al ejecutarlo.
 * --------------------------------------------------------------
 */

include_once 'config.php'; 
include_once 'conexion.php'; 
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Obtener minutos ---
$minutos = $argv[1] ?? null;

if (!$minutos) {
    echo "Ingrese los minutos: ";
    $minutos = trim(fgets(STDIN));
}

if (!is_numeric($minutos) || $minutos <= 0) {
    die("❌ Ingrese un número válido.\n");
}

echo "⏳ Buscando reservas pendientes con más de $minutos minutos...\n";

// --- Conexión ---
$conexion = conexionBD();

// --- Consulta ---
$sql = "
SELECT r.id_reserva, r.fecha_reserva, rd.id_chofer, u.correo, u.nombre
FROM reservas r
JOIN rides rd ON rd.id_ride = r.id_ride
JOIN usuarios u ON u.id_usuario = rd.id_chofer
WHERE r.estado = 'pendiente'
AND TIMESTAMPDIFF(MINUTE, r.fecha_reserva, NOW()) > ?
";

$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    die("Error en prepare: " . mysqli_error($conexion) . "\n");
}

mysqli_stmt_bind_param($stmt, "i", $minutos);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($resultado) === 0) {
    echo "✅ No hay reservas pendientes con más de $minutos minutos.\n";
    exit;
}

// --- Función para enviar correo ---
function enviarCorreoNotificacion($correo, $nombre, $minutos) {
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USERNAME, 'AventonesCR');
        $mail->addAddress($correo, $nombre);

        $mail->isHTML(true);
        $mail->Subject = '🚗 Tienes solicitudes de reserva pendientes';
        $mail->Body = "
        <html><body style='font-family: Arial; background-color: #f9f9f9; padding: 20px;'>
        <div style='max-width:600px;margin:auto;background:#fff;padding:30px;border-radius:10px;'>
            <h2 style='color:#2196F3;text-align:center;'>🚗 Tienes reservas pendientes</h2>
            <p>Hola <strong>$nombre</strong>,</p>
            <p>Tienes una solicitud que llevan más de <strong>$minutos minutos</strong> sin respuesta.</p>
            <p>Por favor, revisa tu panel de chofer.</p>
        </div></body></html>";

        $mail->send();
        error_log("Correo enviado a $correo");
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo a $correo: " . $mail->ErrorInfo);
        return false;
    }
}

// --- Envío de correos ---
$total = 0;
while ($fila = mysqli_fetch_assoc($resultado)) {
    $correo = $fila['correo'];
    $nombre = $fila['nombre'];
    $id_chofer = $fila['id_chofer'];

    echo "📩 Enviando notificación a $nombre (Chofer ID: $id_chofer)... ";

    if (enviarCorreoNotificacion($correo, $nombre, $minutos)) {
        echo "✅ Enviado\n";
        $total++;
    } else {
        echo "❌ Error\n";
    }
}

echo "\n🎉 Total de correos enviados: $total\n";

mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>


