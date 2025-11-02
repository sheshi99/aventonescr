<?php

include_once 'config.php'; 

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function enviarCorreoActivacion($correo, $nombre, $token,$rol) {
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = 'UTF-8'; // Para tildes
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
        $mail->Subject = 'Activación de cuenta';

        $url = BASE_URL . "/activarCuenta.php?token=" . urlencode($token);

        $mail->Body = "
        <html>
        <body style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;'>
            <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #2196F3; text-align: center;'>¡Bienvenido a la Comunidad de Aventones CR!</h2>
                <p>Hola <strong>$nombre</strong>,</p>
                <p>Estamos emocionados de que te unas a nuestra comunidad como $rol. 🚗✨</p>
                <p>Para activar tu cuenta y comenzar a disfrutar de Aventones CR, haz clic en el siguiente botón:</p>
                <p style='text-align: center;'>
                    <a href='$url' style='
                        display: inline-block;
                        padding: 12px 25px;
                        font-size: 16px;
                        color: #fff;
                        background-color: #2196F3;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                    '>Activar mi cuenta</a>
                </p>
                <p style='margin-top: 20px;'>Si no creaste esta cuenta, puedes ignorar este correo.</p>
                <p style='text-align: center; color: #777; font-size: 12px;'>Gracias por confiar en la Comunidad de Aventones CR 🌟</p>
            </div>
        </body>
        </html>
        ";


        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo a $correo: " . $mail->ErrorInfo);
        return false;
    }
}


?>
