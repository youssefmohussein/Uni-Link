<?php

namespace App\DTOs;

class ReservationDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
        public string $date,
        public string $time,
        public int $people,
        public string $notes = '',
        public ?string $qrUrl = null
    ) {}
}
