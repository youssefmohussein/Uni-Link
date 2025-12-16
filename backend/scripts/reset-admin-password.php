<?php
/**
 * Quick Admin Password Reset
 * 
 * Simple web interface to reset admin password
 * Visit: http://localhost/backend/scripts/reset-admin-password.php
 */

// Suppress all error output to prevent HTML in JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

try {
    require_once __DIR__ . '/../config/autoload.php';
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load application: ' . $e->getMessage()
    ]);
    exit;
}

use App\Utils\Database;

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear any output that might have been generated
    ob_end_clean();
    
    header('Content-Type: application/json');
    
    $identifier = $_POST['identifier'] ?? null;
    $newPassword = $_POST['password'] ?? null;
    
    if (empty($identifier) || empty($newPassword)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Both identifier and password are required'
        ]);
        exit;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Find user - check for both 'ADMIN' and 'Admin' roles (case-insensitive)
        $stmt = $db->prepare("
            SELECT user_id, username, email, role 
            FROM Users 
            WHERE (email = ? OR username = ?) 
            AND (role = 'ADMIN' OR role = 'Admin')
        ");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'Admin user not found. Make sure the user exists and has Admin role.'
            ]);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        if ($hashedPassword === false) {
            throw new Exception('Failed to hash password');
        }
        
        // Update password
        $updateStmt = $db->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
        $result = $updateStmt->execute([$hashedPassword, $user['user_id']]);
        
        if (!$result) {
            throw new Exception('Failed to update password in database');
        }
        
        // Verify the update
        $verifyStmt = $db->prepare("SELECT password_hash FROM Users WHERE user_id = ?");
        $verifyStmt->execute([$user['user_id']]);
        $updated = $verifyStmt->fetch(\PDO::FETCH_ASSOC);
        $passwordValid = password_verify($newPassword, $updated['password_hash']);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Admin password updated successfully!',
            'user' => [
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ],
            'password_verified' => $passwordValid
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } catch (Error $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'PHP Error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Clear output buffer before displaying HTML
ob_end_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Admin Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        button:active:not(:disabled) {
            transform: translateY(0);
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Admin Password</h1>
        <p class="subtitle">Update password hash for admin account</p>
        
        <div class="warning">
            ⚠️ <strong>Security Note:</strong> This tool should only be used in development or when you've added a user directly via SQL with a plain text password.
        </div>
        
        <div id="message" class="message"></div>
        
        <form id="resetForm">
            <div class="form-group">
                <label for="identifier">Email or Username</label>
                <input 
                    type="text" 
                    id="identifier" 
                    name="identifier" 
                    placeholder="admin@example.com or admin"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter new password"
                    required
                    minlength="6"
                >
            </div>
            
            <button type="submit">Update Password</button>
        </form>
    </div>
    
    <script>
        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const messageDiv = document.getElementById('message');
            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.textContent;
            
            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.textContent = 'Updating...';
            messageDiv.className = 'message';
            messageDiv.textContent = '';
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                // Get response text first to check if it's JSON
                const responseText = await response.text();
                let data;
                
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    // If not JSON, show the raw response (might be HTML error)
                    throw new Error('Server returned invalid response. Check PHP error logs. Response: ' + responseText.substring(0, 200));
                }
                
                messageDiv.className = 'message ' + (data.status === 'success' ? 'success' : 'error');
                messageDiv.textContent = data.message;
                
                if (data.status === 'success') {
                    e.target.reset();
                    setTimeout(() => {
                        messageDiv.className = 'message';
                        messageDiv.textContent = '';
                    }, 5000);
                }
            } catch (error) {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Error: ' + error.message;
                console.error('Full error:', error);
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalButtonText;
            }
        });
    </script>
</body>
</html>

