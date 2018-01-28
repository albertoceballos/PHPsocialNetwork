<?php

  require_once('PHPMailer-5.2-stable/PHPMailerAutoload.php');

  class Mail{
    public static function sendMail($subject, $body, $address) {
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = '465';
                $mail->isHTML();
                $mail->Username = 'albertoceballos20@gmail.com';
                $mail->Password = 'K1LLt0S4V34L1F3';
                $mail->SetFrom('albertoceballos20@gmail.com');
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AddAddress($address);
                $mail->Send();
        }
  }

?>
