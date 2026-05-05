<?php

namespace App\Services\PaymentStrategy;

/**
 * Card Payment Strategy
 */
class CardPayment implements PaymentStrategyInterface
{
    public function processPayment(float $amount, array $data = []): array
    {
        // Simulate card payment processing
        // In production, integrate with payment gateway (Stripe, PayPal, etc.)
        
        $cardNumber = $data['card_number'] ?? '';
        $expiry = $data['expiry'] ?? '';
        $cvv = $data['cvv'] ?? '';
        
        // Basic validation (in production, use proper card validation)
        if (empty($cardNumber) || empty($expiry) || empty($cvv)) {
            return [
                'success' => false,
                'transaction_id' => null,
                'message' => 'Card details are required'
            ];
        }
        
        // Simulate payment processing delay
        usleep(500000); // 0.5 seconds
        
        // Simulate 95% success rate
        $success = rand(1, 100) <= 95;
        
        if ($success) {
            return [
                'success' => true,
                'transaction_id' => 'CARD-' . time() . '-' . rand(10000, 99999),
                'message' => 'Card payment processed successfully'
            ];
        } else {
            return [
                'success' => false,
                'transaction_id' => null,
                'message' => 'Card payment declined. Please try another card.'
            ];
        }
    }
}

