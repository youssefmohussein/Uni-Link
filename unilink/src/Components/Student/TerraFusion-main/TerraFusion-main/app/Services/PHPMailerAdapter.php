<?php

namespace App\Services;

use App\Interfaces\EmailProviderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerAdapter implements EmailProviderInterface
{
    private $mailer;

    public function __construct(string $host, string $username, string $password, int $port = 587)
    {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host       = $host;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $username;
        $this->mailer->Password   = $password;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $port;
        
        // Default From
        $this->mailer->setFrom($username, 'TerraFusion Reservations');
    }

    public function send(string $to, string $subject, string $body): bool
    {
        try {
            $this->mailer->clearAddresses(); // Clear previous recipients
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
