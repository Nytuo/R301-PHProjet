<?php

class Mailer
{

    public static function sendMail($to,$subject, $message): void
    {
        $message = "Bonjour, \n\n" . $message . "\n\nCordialement,\nL'équipe de Comics Sans MS";
        $headers = "From: COMICSsansMS@CSMS.fr" . "\r\n" .
            "Reply-To: arnaud.beux@etu.unice.fr" . "\r\n" .
            "X-Mailer: PHP/" . phpversion();
        mail($to, $subject, $message, $headers);
    }
}