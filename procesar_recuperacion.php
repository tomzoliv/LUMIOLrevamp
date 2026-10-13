<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $stmt = $conexion->prepare("SELECT Id FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmtInsert = $conexion->prepare("INSERT INTO recuperacion_tokens (email, token, expiracion) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("sss", $email, $token, $expiracion);
        $stmtInsert->execute();

        $ip_local = "172.30.189.13"; 
        $link = "http://" . $ip_local . "/LUMIOLrevamp/restablecer.php?token=" . $token;

        $cuerpo_html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Restablecer contraseña - LUMIOL</title>
        </head>
        <body style="margin:0; padding:0; background-color:#f5f0e6; font-family:\'Georgia\', serif; color:#2c2c2c;">
            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f5f0e6; padding: 20px 0;">
                <tr>
                    <td align="center">
                        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 500px; background-color: #fcf8f2; border: 1px solid #c8a882; padding: 30px; text-align: center;">
                            <!-- Encabezado / Marca -->
                            <tr>
                                <td style="padding-bottom: 20px; border-bottom: 2px solid #b84a39;">
                                    <h1 style="margin:0; font-size: 28px; color: #b84a39; letter-spacing: 2px;">LUMIOL</h1>
                                </td>
                            </tr>
                            <!-- Mensaje Principal -->
                            <tr>
                                <td style="padding: 25px 0 15px 0;">
                                    <h2 style="margin:0 0 15px 0; font-size: 20px; color: #2c2c2c; font-weight: normal;">Restablecimiento de contraseña</h2>
                                    <p style="font-size: 15px; line-height: 1.6; color: #555555; margin: 0 0 20px 0;">
                                        Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Haz clic en el siguiente botón para continuar:
                                    </p>
                                </td>
                            </tr>
                            <!-- Botón de Acción -->
                            <tr>
                                <td align="center" style="padding-bottom: 20px;">
                                    <a href="' . $link . '" style="background-color: #b84a39; color: #ffffff; text-decoration: none; padding: 14px 28px; font-size: 16px; font-weight: bold; border-radius: 4px; display: inline-block; letter-spacing: 1px;">
                                        RESTABLECER CONTRASEÑA
                                    </a>
                                </td>
                            </tr>
                            <!-- Caja de Aviso de Expiración -->
                            <tr>
                                <td align="center" style="padding-bottom: 25px;">
                                    <div style="background-color: #f2e6d8; border: 1px dashed #b84a39; padding: 10px 15px; border-radius: 4px; display: inline-block; font-size: 13px; color: #8a2e22;">
                                        ⏰ <strong>Atención:</strong> Este enlace expira en <strong>1 hora</strong> por razones de seguridad.
                                    </div>
                                </td>
                            </tr>
                            <!-- Enlace alternativo para copiar -->
                            <tr>
                                <td style="border-top: 1px solid #e0d8cc; padding-top: 20px; font-size: 12px; color: #777777;">
                                    <p style="margin: 0 0 10px 0;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                                    <p style="margin: 0; word-break: break-all;"><a href="' . $link . '" style="color: #b84a39;">' . $link . '</a></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lumiolsupport@gmail.com';
            $mail->Password = 'bxywtlpoesjuwyqx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('lumiolsupport@gmail.com', 'LUMIOL Soporte');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Restablecer contraseña - LUMIOL';
            $mail->Body = $cuerpo_html;

            $mail->send();
        } catch (Exception $e) {
            echo "El correo no se pudo enviar. Error: {$mail->ErrorInfo}";
            exit;
        }
    }

    echo "Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.";
}
?>