<?php

namespace App\Services;

use App\Interfaces\EmailProviderInterface;
use App\DTOs\ReservationDTO;

class ReservationService
{
    private $emailProvider;

    public function __construct(EmailProviderInterface $emailProvider)
    {
        $this->emailProvider = $emailProvider;
    }

    public function sendConfirmationEmail(ReservationDTO $reservation): bool
    {
        $subject = "Table Reservation Confirmation - TerraFusion";
        $body = $this->generateEmailBody($reservation);

        return $this->emailProvider->send($reservation->email, $subject, $body);
    }

    private function generateEmailBody(ReservationDTO $reservation): string
    {
        // Dark/Gold Theme HTML
        $qrImage = $reservation->qrUrl ? "<div style='text-align: center; margin: 20px;'><p>Please present this QR code upon arrival:</p><img src='{$reservation->qrUrl}' alt='QR Code' style='border: 2px solid #cda45e; padding: 10px; border-radius: 5px;' width='200'></div>" : "";
        
        return "
        <html>
        <head>
          <style>
            body { font-family: 'Playfair Display', serif; background-color: #1a1814; color: #fff; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; background-color: #0c0b09; border: 1px solid #cda45e; border-radius: 5px; overflow: hidden; }
            .header { background-color: #0c0b09; padding: 30px; text-align: center; border-bottom: 1px solid #cda45e; }
            .header h1 { color: #cda45e; margin: 0; font-size: 28px; }
            .content { padding: 30px; color: #ffffff; }
            .details { background-color: #1a1814; padding: 20px; border-radius: 5px; margin: 20px 0; border: 1px solid #333; }
            .details ul { list-style: none; padding: 0; }
            .details li { padding: 10px 0; border-bottom: 1px solid #333; color: #aaa; }
            .details li strong { color: #cda45e; width: 100px; display: inline-block; }
            .footer { background-color: #0c0b09; padding: 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #333; }
          </style>
        </head>
        <body>
          <div class='container'>
            <div class='header'>
              <h1>Reservation Confirmed</h1>
            </div>
            <div class='content'>
              <p>Dear {$reservation->name},</p>
              <p>We are honored to confirm your reservation at TerraFusion. Your table has been successfully booked.</p>
              
              <div class='details'>
                <ul>
                  <li><strong>Date:</strong> {$reservation->date}</li>
                  <li><strong>Time:</strong> {$reservation->time}</li>
                  <li><strong>Guests:</strong> {$reservation->people}</li>
                  <li><strong>Phone:</strong> {$reservation->phone}</li>
                  <li><strong>Notes:</strong> {$reservation->notes}</li>
                </ul>
              </div>

              {$qrImage}
              
              <p style='text-align: center;'>We look forward to providing you with an exceptional dining experience.</p>
            </div>
            <div class='footer'>
              <p>&copy; " . date('Y') . " TerraFusion Restaurant. All rights reserved.</p>
            </div>
          </div>
        </body>
        </html>
        ";
    }
}
