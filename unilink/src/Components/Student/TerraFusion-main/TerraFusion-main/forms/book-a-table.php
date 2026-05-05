<?php
// 1. Silence HTML Errors
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 2. Output Buffering (Catch stray HTML/Warnings)
ob_start();

// 3. JSON Header
header('Content-Type: application/json');

require_once '../config.php';

$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

try {
    // Check request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Invalid request method.");
    }

    // Retrieve and sanitize inputs
    $name = strip_tags(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"] ?? ''));
    $date = strip_tags(trim($_POST["date"] ?? ''));
    $time = strip_tags(trim($_POST["time"] ?? ''));
    $people = intval($_POST["people"] ?? 0);
    $message_text = strip_tags(trim($_POST["message"] ?? ''));

    // Validate inputs
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || $people <= 0) {
        throw new Exception("Please fill out all required fields.");
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO reservations (customer_name, contact_phone, party_size, reservation_date, reservation_time, status, notes) VALUES (?, ?, ?, ?, ?, 'Confirmed', ?)");
    if (!$stmt->execute([$name, $phone, $people, $date, $time, $message_text])) {
        throw new Exception("Database insert failed.");
    }

    // Get the new reservation ID
    $new_id = $pdo->lastInsertId();

    // Generate QR Code URL
    $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TerraFusion_Res_$new_id";

    // Success so far
    $response['status'] = 'success';
    $response['message'] = 'Booked!';
    $response['reservation_id'] = $new_id;
    $response['qr_code_url'] = $qr_code_url;
    $response['guests'] = $people;
    $response['date'] = $date;

    // Generate QR Code URL
    $qrData = "Reservation Confirmed\nName: $name\nDate: $date\nTime: $time\nGuests: $people\nRef: " . uniqid();
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);

    // Send Email (Wrapped in its own try/catch to not fail the reservation if email fails)
    try {
            // Send Confirmation Email
            $autoloadPath = __DIR__ . '/../vendor/autoload.php';
            $manualPath = __DIR__ . '/../PHPMailer/src/PHPMailer.php'; // Check for manual install

            // Determine which loader to use
            $hasComposer = file_exists($autoloadPath);
            $hasManual = file_exists($manualPath);

            if ($hasComposer || $hasManual) {
                
                if ($hasComposer) {
                    require_once $autoloadPath;
                } else {
                    // Manual Includes
                    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
                    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
                    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
                }

                // Load our custom classes (Manual require for these too if no composer)
                require_once __DIR__ . '/../app/Interfaces/EmailProviderInterface.php';
                require_once __DIR__ . '/../app/DTOs/ReservationDTO.php';
                require_once __DIR__ . '/../app/Services/PHPMailerAdapter.php';
                require_once __DIR__ . '/../app/Services/ReservationService.php';

                // Use namespace
                // Note: PHPMailer classes in global namespace if manually included? 
                // Actually they are namespaced PHPMailer\PHPMailer even in source.

                try {
                    // Initialize Service
                    // REPLACE THESE CREDENTIALS WITH YOUR ACTUAL GMAIL APP PASSWORD
                    $mailAdapter = new \App\Services\PHPMailerAdapter('smtp.gmail.com', 'salmahelhefnawi@gmail.com', 'YOUR_APP_PASSWORD_HERE');
                    $reservationService = new \App\Services\ReservationService($mailAdapter);

                    // Create DTO
                    $reservationDTO = new \App\DTOs\ReservationDTO($name, $email, $phone, $date, $time, $people, $message_text, $qrUrl);

                    // Send Email
                    $reservationService->sendConfirmationEmail($reservationDTO);

                } catch (Exception $e) {
                    error_log("PHPMailer failed: " . $e->getMessage());
                }
            } else {
                // FALLBACK: Native mail()
            $subject = "Table Reservation Confirmation - TerraFusion";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: TerraFusion <noreply@terrafusion.com>" . "\r\n";
            
            $fallbackBody = "<html><body><h2>Reservation Confirmed</h2><p>Dear $name,</p><p>We look forward to seeing you!</p></body></html>";
            
            @mail($email, $subject, $fallbackBody, $headers);
        }
    } catch (Exception $emailException) {
        // Log email error but do NOT fail the request
        error_log("Email Warning: " . $emailException->getMessage());
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 5. Output Buffering Clean & Output
ob_clean(); // Discard any HTML/PHP errors generated above
echo json_encode($response);
exit;
?>
