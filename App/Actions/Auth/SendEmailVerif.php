<?php
namespace App\Actions\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use Exception;
use React\EventLoop\Factory;
use React\Promise\Promise;


class SendEmailVerif {
    public static function sendVerificationEmailAsync($email, $token)
    {
        $loop = Factory::create();
        $promise = new Promise(function ($resolve) use ($email, $token, $loop) {
            $loop->futureTick(function () use ($resolve, $email, $token) {
                self::sendVerificationEmail($email, $token);
                $resolve(true);
            });
        });

        $promise->then(function () {
            echo "Email sent asynchronously.\n";
        });

        $loop->run();
    }

    public static function sendVerificationEmail($email, $token) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'galenryandito123@gmail.com';
            $mail->Password = 'cvgmwmhqvasiltcl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->setFrom('galenryandito123@gmail.com', 'inLavorie Resto');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification inLavorie Resto';
            $mail->Body    = '<html>
    <head>
        <style>
            .email-container {
                font-family: Arial, sans-serif;
                color: #333;
                background-color: #f9f9f9;
                padding: 20px;
                border: 1px solid #ddd;
                max-width: 600px;
                margin: 0 auto;
            }
            .email-header {
                font-size: 24px;
                font-weight: bold;
                color: #4CAF50;
                text-align: center;
                margin-bottom: 20px;
            }
            .email-content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .email-button {
                display: inline-block;
                padding: 10px 20px;
                color: #fff;
                background-color: #4CAF50;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
            }
            .footer {
                font-size: 12px;
                color: #666;
                text-align: center;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                Email Verification - inLavorie Resto
            </div>
            <div class="email-content">
                Hello, <br><br>
                Terima kasih telah mendaftar inLavorie Resto, untuk menyelesaikan pendaftaran, silahkan klik button dibawah ini!
            </div>
            <div style="text-align: center; margin-bottom: 20px;color:white;">
                <a href="http://localhost:8000/verification-email/'.$token.'" class="email-button">Verifikasi Email</a>
            </div>
            <div class="footer">
                &copy; '.date("Y").' Lavorie Resto. All rights reserved.
            </div>
        </div>
    </body>
    </html>';
    
            $mail->send();
            echo 'Verification email has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}