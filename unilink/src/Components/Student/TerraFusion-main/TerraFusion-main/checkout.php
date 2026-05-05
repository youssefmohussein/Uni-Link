<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';
require_once 'cart_functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$customer_id = $_SESSION['user_id'];
$cart_id = getOrCreateCart($customer_id);

$cartItems = getCartItems($cart_id);
$subtotal = getCartTotal($cart_id);
$tax = $subtotal * 0.10;
$deliveryFee = 5.00;
$total = $subtotal + $tax + $deliveryFee;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TerraFusion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Complete your order with secure payment</p>
        </div>

        <div class="checkout-container">
            <!-- Checkout Steps -->
            <div class="checkout-steps">
                <div class="checkout-step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Delivery Info</div>
                </div>
                <div class="checkout-step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="checkout-step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Review</div>
                </div>
            </div>

            <form id="checkout-form" action="process-order.php" method="POST">
                <!-- Step 1: Delivery Information -->
                <div class="checkout-form-section active" id="step-1">
                    <div class="card">
                        <h2 class="section-title">Delivery Information</h2>
                        
                        <div class="form-group">
                            <label class="form-label">
                                Order Type <span class="required">*</span>
                            </label>
                            <select class="form-select" name="order_type" id="order_type" required>
                                <option value="">Select order type</option>
                                <option value="takeaway">Pick-up</option>
                                <option value="delivery">Delivery</option>
                            </select>
                            <div class="form-error">Please select an order type</div>
                        </div>

                        <div id="table-selection" class="form-group" style="display: none;">
                            <label class="form-label">
                                Table Number <span class="required">*</span>
                            </label>
                            <select class="form-select" name="table_number" id="table_number">
                                <option value="">Select table</option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?php echo $i; ?>">Table <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="form-error">Please select a table number</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    First Name <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-input" 
                                       name="first_name" 
                                       id="first_name" 
                                       required 
                                       placeholder="John">
                                <div class="form-error">First name is required</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    Last Name <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-input" 
                                       name="last_name" 
                                       id="last_name" 
                                       required 
                                       placeholder="Doe">
                                <div class="form-error">Last name is required</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Email Address <span class="required">*</span>
                            </label>
                            <input type="email" 
                                   class="form-input" 
                                   name="email" 
                                   id="email" 
                                   required 
                                   placeholder="john.doe@example.com">
                            <div class="form-error">Please enter a valid email address</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Phone Number <span class="required">*</span>
                            </label>
                            <input type="tel" 
                                   class="form-input" 
                                   name="phone" 
                                   id="phone" 
                                   required 
                                   placeholder="+1 (555) 123-4567">
                            <div class="form-error">Please enter a valid phone number</div>
                        </div>

                        <div id="delivery-address" class="form-group" style="display: none;">
                            <label class="form-label">
                                Delivery Address <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   class="form-input" 
                                   name="address_line1" 
                                   id="address_line1" 
                                   placeholder="Street Address">
                            <div class="form-error">Please enter your delivery address</div>
                        </div>

                        <div id="delivery-address-2" class="form-row" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" 
                                       class="form-input" 
                                       name="city" 
                                       id="city" 
                                       placeholder="City">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" 
                                       class="form-input" 
                                       name="postal_code" 
                                       id="postal_code" 
                                       placeholder="12345">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Special Instructions</label>
                            <textarea class="form-textarea" 
                                      name="special_instructions" 
                                      id="special_instructions" 
                                      placeholder="Any special requests or dietary requirements..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Payment Information -->
                <div class="checkout-form-section" id="step-2">
                    <div class="card">
                        <h2 class="section-title">Payment Method</h2>
                        
                        <div class="payment-methods">
                            <div class="payment-method" onclick="selectPaymentMethod('card')">
                                <input type="radio" name="payment_method" id="payment_card" value="card" required>
                                <label for="payment_card" style="cursor: pointer; display: block;">
                                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">💳</div>
                                    <div>Credit/Debit Card</div>
                                </label>
                            </div>
                            
                            <div class="payment-method" onclick="selectPaymentMethod('cash')">
                                <input type="radio" name="payment_method" id="payment_cash" value="cash">
                                <label for="payment_cash" style="cursor: pointer; display: block;">
                                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">💵</div>
                                    <div>Cash on Delivery</div>
                                </label>
                            </div>
                            
                            <div class="payment-method" onclick="selectPaymentMethod('paypal')">
                                <input type="radio" name="payment_method" id="payment_paypal" value="paypal">
                                <label for="payment_paypal" style="cursor: pointer; display: block;">
                                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">🅿️</div>
                                    <div>PayPal</div>
                                </label>
                            </div>
                        </div>

                        <div id="card-details" style="display: none; margin-top: 2rem;">
                            <!-- Credit Card Visual Component -->
                            <div class="credit-card-container">
                                <div class="credit-card" id="credit-card">
                                    <div class="credit-card-front">
                                        <div class="credit-card-logo">💳</div>
                                        <div class="credit-card-chip"></div>
                                        <div class="credit-card-number" id="card-display-number">1234 5678 9012 3456</div>
                                        <div class="credit-card-info">
                                            <div>
                                                <div class="credit-card-label">CARDHOLDER</div>
                                                <div class="credit-card-name" id="card-display-name">JOHN DOE</div>
                                            </div>
                                            <div>
                                                <div class="credit-card-label">EXPIRES</div>
                                                <div class="credit-card-expiry" id="card-display-expiry">MM/YY</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="credit-card-back">
                                        <div class="credit-card-stripe"></div>
                                        <div class="credit-card-cvv" id="card-display-cvv">123</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Card Number <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-input" 
                                       name="card_number" 
                                       id="card_number" 
                                       placeholder="1234 5678 9012 3456"
                                       maxlength="19">
                                <div class="form-error">Please enter a valid card number</div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">
                                        Expiry Date <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-input" 
                                           name="card_expiry" 
                                           id="card_expiry" 
                                           placeholder="MM/YY"
                                           maxlength="5">
                                    <div class="form-error">Please enter a valid expiry date</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        CVV <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-input" 
                                           name="card_cvv" 
                                           id="card_cvv" 
                                           placeholder="123"
                                           maxlength="4">
                                    <div class="form-error">Please enter a valid CVV</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Cardholder Name <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       class="form-input" 
                                       name="cardholder_name" 
                                       id="cardholder_name" 
                                       placeholder="John Doe">
                                <div class="form-error">Please enter the cardholder name</div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 2rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="save_payment" id="save_payment">
                                <span>Save payment method for future orders</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Review Order -->
                <div class="checkout-form-section" id="step-3">
                    <div class="card">
                        <h2 class="section-title">Review Your Order</h2>
                        
                        <div style="margin-bottom: 2rem;">
                            <h3 style="margin-bottom: 1rem; color: var(--accent-gold);">Order Items</h3>
                            <ul class="order-items-list">
                                <?php if (!empty($cartItems)): ?>
                                    <?php foreach ($cartItems as $item): ?>
                                        <li class="order-item">
                                            <span>
                                                <span class="order-item-name"><?php echo htmlspecialchars($item['meal_name']); ?></span>
                                                <span class="order-item-quantity">(x<?php echo $item['quantity']; ?>)</span>
                                            </span>
                                            <span class="order-item-price">
                                                <?php echo number_format($item['price'] * $item['quantity'], 2); ?> EGP
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="order-item">No items in cart</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="order-summary-box">
                            <h3 class="summary-box-title">Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span><?php echo number_format($subtotal, 2); ?> EGP</span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span><?php echo number_format($tax, 2); ?> EGP</span>
                            </div>
                            <div class="summary-row">
                                <span>Delivery Fee</span>
                                <span><?php echo number_format($deliveryFee, 2); ?> EGP</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span><?php echo number_format($total, 2); ?> EGP</span>
                            </div>
                        </div>

                        <div id="review-delivery-info" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                            <h3 style="margin-bottom: 1rem; color: var(--accent-gold);">Delivery Information</h3>
                            <div id="review-info-content" style="color: var(--text-secondary);">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="checkout-navigation">
                    <button type="button" class="btn btn-secondary" id="prev-btn" onclick="previousStep()" style="display: none;">
                        ← Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="next-btn" onclick="nextStep()">
                        Next →
                    </button>
                    <button type="submit" class="btn btn-primary" id="submit-btn" style="display: none;">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/checkout.js"></script>
</body>
</html>

