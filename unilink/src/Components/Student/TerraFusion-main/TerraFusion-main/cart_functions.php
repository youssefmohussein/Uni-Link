<?php
// cart_functions.php - Database operations for cart functionality (using Orders table)

require_once 'config.php';

/**
 * Get or create a cart (Active Order with status 'New') for the current user
 */
function getOrCreateCart($customer_id) {
    global $pdo;
    
    // Check if user already has an active order (status 'New')
    $stmt = $pdo->prepare("
        SELECT order_id 
        FROM orders 
        WHERE served_by_fk = ? AND status = 'New'
        ORDER BY order_date DESC 
        LIMIT 1
    ");
    $stmt->execute([$customer_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($cart) {
        return $cart['order_id'];
    }
    
    // Create a new order (cart) if none exists
    try {
        $stmt = $pdo->prepare("
            INSERT INTO orders (served_by_fk, status, order_date, total_amount) 
            VALUES (?, 'New', NOW(), 0.00)
        ");
        $stmt->execute([$customer_id]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        // Check for Foreign Key violation (SQLSTATE 23000)
        if ($e->getCode() == '23000') {
            return false;
        }
        throw $e;
    }
}

/**
 * Get all items in the cart
 */
function getCartItems($cart_id) {
    global $pdo;
    
    // Corrected    // Join order_details with meals to get names and images
    $stmt = $pdo->prepare("
        SELECT 
            od.item_fk as meal_id,
            m.meal_name,
            m.description,
            m.image,
            od.quantity,
            od.price_at_sale as price
        FROM order_details od
        JOIN meals m ON od.item_fk = m.meal_id
        WHERE od.order_fk = ?
    ");
    $stmt->execute([$cart_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Add an item to the cart or update quantity if it exists
 */
function addToCart($order_id, $meal_id, $quantity = 1) {
    global $pdo;
    
    try {
        // Get current price of the meal
        $stmtPrice = $pdo->prepare("SELECT price FROM meals WHERE meal_id = ?");
        $stmtPrice->execute([$meal_id]);
        $meal = $stmtPrice->fetch(PDO::FETCH_ASSOC);
        
        if (!$meal) return false;
        
        $price = $meal['price'];

        // Check if item already in cart
        $stmt = $pdo->prepare("
            SELECT quantity FROM order_details 
            WHERE order_fk = ? AND item_fk = ?
        ");
        $stmt->execute([$order_id, $meal_id]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            return updateCartItem($order_id, $meal_id, $newQuantity);
        } else {
            // Add new item
            $stmt = $pdo->prepare("
                INSERT INTO order_details (order_fk, item_fk, quantity, price_at_sale)
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$order_id, $meal_id, $quantity, $price]);
        }
    } catch (PDOException $e) {
        error_log("Error adding to cart: " . $e->getMessage());
        return false;
    }
}

/**
 * Update cart item quantity
 */
function updateCartItem($order_id, $meal_id, $quantity) {
    global $pdo;
    
    try {
        if ($quantity <= 0) {
            return removeFromCart($order_id, $meal_id);
        }
        
        $stmt = $pdo->prepare("
            UPDATE order_details 
            SET quantity = ?
            WHERE order_fk = ? AND item_fk = ?
        ");
        return $stmt->execute([$quantity, $order_id, $meal_id]);
    } catch (PDOException $e) {
        error_log("Error updating cart item: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove an item from the cart
 */
function removeFromCart($order_id, $meal_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM order_details 
            WHERE order_fk = ? AND item_fk = ?
        ");
        return $stmt->execute([$order_id, $meal_id]);
    } catch (PDOException $e) {
        error_log("Error removing from cart: " . $e->getMessage());
        return false;
    }
}

/**
 * Calculate cart total
 */
function getCartTotal($order_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantity * price_at_sale), 0) as total
        FROM order_details
        WHERE order_fk = ?
    ");
    $stmt->execute([$order_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return (float)$result['total'];
}

/**
 * Get total number of items in cart
 */
function getCartCount($order_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(quantity), 0) as count 
        FROM order_details 
        WHERE order_fk = ?
    ");
    $stmt->execute([$order_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return (int)$result['count'];
}

/**
 * Clear all items from a cart
 */
function clearCart($order_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM order_details WHERE order_fk = ?");
    return $stmt->execute([$order_id]);
}
