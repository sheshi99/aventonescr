<?php


include_once 'config.php'; 

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoActivacion($correo, $nombre, $token) {
    $mail = new PHPMailer(true);
    try {
        // Configuración SMTP usando CONSTANTES
        $mail->isSMTP();
        $mail->Host = SMTP_HOST; // Usando la constante
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME; // Usando la constante
        $mail->Password = SMTP_PASSWORD; // Usando la constante
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        // ... resto de la configuración ...
        $mail->setFrom(SMTP_USERNAME, 'AventonesCR'); // Usando la constante
        $mail->addAddress($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Activación de cuenta';
        
        // Usando la constante BASE_URL
        $url = BASE_URL . "/activarCuenta.php?token=$token"; 
        
        $mail->Body = "Hola $nombre,<br><br>
                       Para activar tu cuenta haz clic en el siguiente enlace:<br>
                       <a href='$url'>$url</a><br><br>Gracias.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo a $correo: " . $mail->ErrorInfo);
        return false;
    }
}
