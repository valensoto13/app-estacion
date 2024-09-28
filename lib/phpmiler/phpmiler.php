<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


include 'credenciales.php';
include 'lib/mp-mailer/Mailer/src/PHPMailer.php';
include 'lib/mp-mailer/Mailer/src/SMTP.php';
include 'lib/mp-mailer/Mailer/src/Exception.php';


function enviarCorreo($email, $motivo, $contenido) {
    // Configuración de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->SMTPDebug = 0 ;
        $mail->Host = HOST;
        $mail->Port = PORT;
        $mail->SMTPAuth = SMTP_AUTH; 
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Username = REMITENTE;
        $mail->Password = PASSWORD;


        // Datos del usuario 
        $nombreUsuario = NOMBRE;
        $emailUsuario = REMITENTE;

        // Destinatario y contenido del correo
        $mail->setFrom(REMITENTE, NOMBRE);
        $mail->addAddress($email);
        $mail->isHTML(true);//para que funcione codigo html en el email

        $mail->Subject = utf8_decode($motivo);
        $mail->Body    = utf8_decode($contenido);

        // Enviar el correo
        $mail->send();
        // echo 'El correo se ha enviado correctamente ';
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}



?>