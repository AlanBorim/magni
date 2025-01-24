<?php

namespace App\Core;

require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService
{
    public static function send(string $to, string $subject, string $message): bool
    {
        // Configurações do servidor SMTP
        $mail = new PHPMailer(true);
       
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST']; // Host SMTP
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME']; // Usuário SMTP
            $mail->Password = $_ENV['MAIL_PASSWORD']; // Senha SMTP
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION']; // TLS ou SSL
            $mail->Port = $_ENV['MAIL_PORT']; // Porta SMTP
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Nível de depuração (ver opções abaixo)
            // $mail->Debugoutput = 'html'; // Formato da saída (html, echo, ou log personalizado)

            // Configuração do remetente
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);

            // Configuração do destinatário
            $mail->addAddress($to);

            // Conteúdo do e-mail
            $mail->isHTML(true); // Configura como HTML
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = strip_tags($message); // Texto alternativo (sem HTML)

            // Envia o e-mail
            $mail->send();

            return true;
        } catch (Exception $e) {
            // Registro de erro (opcionalmente, você pode logar)
            error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo . ' ' . $e->getMessage());
            return false;
        }
    }
}
