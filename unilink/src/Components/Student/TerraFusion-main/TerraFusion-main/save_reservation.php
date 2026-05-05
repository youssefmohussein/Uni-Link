<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$customer_name = $input['customer_name'] ?? ($_SESSION['user_name'] ?? 'Guest');
$contact_phone = $input['contact_phone'] ?? ($_SESSION['user_phone'] ?? '');
$party_size = (int)($input['party_size'] ?? 0);
$reservation_date = $input['reservation_date'] ?? '';
$reservation_time = $input['reservation_time'] ?? '';
$notes = $input['notes'] ?? '';

    $email = $input['email'] ?? ($_SESSION['user_email'] ?? '');
    
    // Validate inputs including email if provided (it should be required for email feature)
    if (empty($reservation_date) || empty($reservation_time) || $party_size <= 0 || empty($customer_name)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO reservations (customer_name, contact_phone, party_size, reservation_date, reservation_time, status, notes) 
        VALUES (?, ?, ?, ?, ?, 'Confirmed', ?)
    ");
    
    $success = $stmt->execute([
        $customer_name,
        $contact_phone,
        $party_size,
        $reservation_date,
        $reservation_time,
        $notes
    ]);

    if ($success && !empty($email)) {
        // Generate QR Code Data (Reservation Details)
        $qrData = "Reservation Confirmed\nName: $customer_name\nDate: $reservation_date\nTime: $reservation_time\nGuests: $party_size";
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);

        // Send Email
        $to = $email;
        $subject = "Table Reservation Confirmation - TerraFusion";
        
        $message = "
        <html>
        <head>
          <title>Reservation Confirmation</title>
          <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .container { padding: 20px; border: 1px solid #ddd; border-radius: 10px; max-width: 600px; margin: 0 auto; }
            .header { background-color: #f8f9fa; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { padding: 20px; }
            .footer { font-size: 12px; color: #777; text-align: center; margin-top: 20px; }
            .qr-code { text-align: center; margin: 20px 0; }
          </style>
        </head>
        <body>
          <div class='container'>
            <div class='header'>
              <h2>Reservation Confirmed!</h2>
            </div>
            <div class='content'>
              <p>Dear <strong>$customer_name</strong>,</p>
              <p>Thank you for choosing TerraFusion. Your table has been successfully reserved.</p>
              <p><strong>Reservation Details:</strong></p>
              <ul>
                <li><strong>Date:</strong> $reservation_date</li>
                <li><strong>Time:</strong> $reservation_time</li>
                <li><strong>Party Size:</strong> $party_size guests</li>
                <li><strong>Phone:</strong> $contact_phone</li>
              </ul>
              
              <div class='qr-code'>
                <p>Please show this QR code upon arrival:</p>
                <img src='$qrUrl' alt='Reservation QR Code' />
              </div>
              
              <p>We look forward to serving you!</p>
            </div>
            <div class='footer'>
              <p>&copy; " . date('Y') . " TerraFusion Restaurant. All rights reserved.</p>
            </div>
          </div>
        </body>
        </html>
        ";

        // Headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <noreply@terrafusion.com>' . "\r\n"; // Update usage domain if known

        // Attempt to send email
        // Note: This requires a configured mail server (SMTP or local sendmail)
        @mail($to, $subject, $message, $headers);
    }

    echo json_encode(['success' => $success]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
