<?php

namespace App\Services\PaymentStrategy;

/**
 * Payment Strategy Interface
 * Strategy Pattern for payment processing
 */
interface PaymentStrategyInterface
{
    /**
     * Process payment
     * @param float $amount Payment amount
     * @param array $data Additional payment data
     * @return array ['success' => bool, 'transaction_id' => string|null, 'message' => string]
     */
    public function processPayment(float $amount, array $data = []): array;
}

