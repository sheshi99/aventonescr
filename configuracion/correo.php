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
        $mail->Host = SMTP_HOST; 
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME; 
        $mail->Password = SMTP_PASSWORD; 
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        // Remitente y destinatario
        $mail->setFrom(SMTP_USERNAME, 'AventonesCR');
        $mail->addAddress($correo, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Activacion de cuenta';

        // URL de activación
        $url = BASE_URL . "/activarCuenta.php?token=" . urlencode($token);

        // Cuerpo HTML con botón
        $mail->Body = "
        <html>
        <body>
            <p>Hola $nombre,</p>
            <p>Para activar tu cuenta haz clic en el siguiente boton:</p>
            <p>
                <a href='$url' style='
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #fff;
                    background-color: #2196F3;
                    text-decoration: none;
                    border-radius: 5px;
                '>Activar Cuenta</a>
            </p>
            <p>Gracias.</p>
        </body>
        </html>
        ";

        // Enviar correo
        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo a $correo: " . $mail->ErrorInfo);
        return false;
    }
}
?>
