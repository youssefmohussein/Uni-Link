<?php

namespace App\Services\PaymentStrategy;

/**
 * Cash Payment Strategy
 */
class CashPayment implements PaymentStrategyInterface
{
    public function processPayment(float $amount, array $data = []): array
    {
        // Cash payments are always successful (assume cash received)
        // In real scenario, you'd verify cash count
        
        return [
            'success' => true,
            'transaction_id' => 'CASH-' . time() . '-' . rand(1000, 9999),
            'message' => 'Cash payment received successfully'
        ];
    }
}

