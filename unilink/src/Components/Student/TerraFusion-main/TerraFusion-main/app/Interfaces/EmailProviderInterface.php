<?php

namespace App\Interfaces;

interface EmailProviderInterface
{
    /**
     * Send an email.
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $bodyUrl HTML body content
     * @return bool True if sent successfully, false otherwise
     */
    public function send(string $to, string $subject, string $body): bool;
}
