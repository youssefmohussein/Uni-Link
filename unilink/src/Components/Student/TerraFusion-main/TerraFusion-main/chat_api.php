<?php
/**
 * TerraFusion Chat API - Gemini Middleman
 * Features: Secure API key handling, Rate limiting, Persona enforcement, DB Menu Grounding.
 */

session_start();
header('Content-Type: application/json');

// --- 1. Rate Limiting (10 requests per minute) ---
$now = time();
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Remove timestamps older than 60 seconds
$_SESSION['chat_history'] = array_filter($_SESSION['chat_history'], function($ts) use ($now) {
    return ($now - $ts) < 60;
});

if (count($_SESSION['chat_history']) >= 10) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests. Please wait a minute before sending more messages.']);
    exit;
}
$_SESSION['chat_history'][] = $now;


// --- 2. Environment Loading (Manual Fallback) ---
function loadEnv($path) {
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
    return $env;
}

$env = loadEnv(__DIR__ . '/.env');
$apiKey = $env['GEMINI_API_KEY'] ?? '';

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error: API Key missing.']);
    exit;
}


// --- 3. Database Connection & Menu Fetching ---
require_once __DIR__ . '/config.php'; // Ensures $pdo is available

function getFormattedMenu($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT meal_id, meal_name, meal_type, description, price FROM meals WHERE availability = 'Available' ORDER BY meal_type, meal_name");
        $stmt->execute();
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($meals)) {
            return "No menu items are currently available.";
        }

        $menuText = "";
        $currentCat = "";
        
        foreach ($meals as $meal) {
            // Group by meal_type header
            if ($meal['meal_type'] !== $currentCat) {
                $currentCat = $meal['meal_type'];
                $menuText .= "\n" . strtoupper($currentCat) . ":\n";
            }
            $menuText .= "- {$meal['meal_name']} ({$meal['meal_id']}): {$meal['description']} ($" . number_format($meal['price'], 2) . ")\n";
        }
        return $menuText;

    } catch (PDOException $e) {
        // Fallback or log error
        return "Menu temporarily unavailable (DB Error).";
    }
}

$menuContext = getFormattedMenu($pdo);


// --- 4. Get Input ---
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';
// Note: We ignore $input['menu'] now as we prefer the DB source

if (empty($userMessage)) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

// --- 5. Configuration ---
// Using gemini-flash-latest for stability
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=$apiKey";

// --- 6. System Instruction / Persona ---
$systemInstruction = "You are Chef Mahmoud, a virtual head chef. You are friendly, professional, and helpful. You speak both English and Arabic fluently.
Your goal is to suggest meals, answer menu questions, and help with reservations at TerraFusion restaurant.

HERE IS THE AVAILABLE MENU:
$menuContext

RULES:
1. You must ONLY recommend dishes from the list above. Do not invent items.
2. Use a warm, culinary persona.
3. Be bilingual: Respond in the language the user speaks (English or Arabic).
4. Strictly only discuss TerraFusion and its menu. If asked about other topics, politely steer back to food.
5. If a user wants to order or add an item to their cart, use the 'add_to_cart' tool with the exact meal_id from the list.
";

// --- 7. Prepare Payload ---
$data = [
    "system_instruction" => [
        "parts" => [
            ["text" => $systemInstruction]
        ]
    ],
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => $userMessage]
            ]
        ]
    ],
    "tools" => [
        [
            "function_declarations" => [
                [
                    "name" => "add_to_cart",
                    "description" => "Adds a specific meal to the user's shopping cart based on its meal ID.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => [
                            "meal_id" => [
                                "type" => "integer",
                                "description" => "The unique ID of the meal to add."
                            ],
                            "meal_name" => [
                                "type" => "string",
                                "description" => "The name of the meal being added."
                            ]
                        ],
                        "required" => ["meal_id", "meal_name"]
                    ]
                ],
                [
                    "name" => "create_reservation",
                    "description" => "Books a table for a customer with specific date, time, and party size.",
                    "parameters" => [
                        "type" => "object",
                        "properties" => [
                            "reservation_date" => [
                                "type" => "string",
                                "description" => "The date of the reservation (YYYY-MM-DD)."
                            ],
                            "reservation_time" => [
                                "type" => "string",
                                "description" => "The time of the reservation (HH:MM)."
                            ],
                            "party_size" => [
                                "type" => "integer",
                                "description" => "The number of people for the reservation."
                            ],
                            "notes" => [
                                "type" => "string",
                                "description" => "Any special requests or notes."
                            ]
                        ],
                        "required" => ["reservation_date", "reservation_time", "party_size"]
                    ]
                ]
            ]
        ]
    ]
];

// --- 8. Send Request with Retry Logic ---
function callGeminiAPI($url, $data, $maxRetries = 3) {
    for ($i = 0; $i < $maxRetries; $i++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 429) {
            // Rate limit hit, wait and retry
            sleep(2 * ($i + 1)); // Backoff: 2s, 4s, 6s
            continue;
        }

        return ['code' => $httpCode, 'response' => $response, 'error' => $curlError];
    }
    return ['code' => 429, 'response' => $response, 'error' => "Max retries reached"];
}

$result = callGeminiAPI($apiUrl, $data);
$httpCode = $result['code'];
$response = $result['response'];
$curlError = $result['error'];

// --- 9. Handle Result ---
if ($httpCode === 200 && $response) {
    $decoded = json_decode($response, true);
    $candidate = $decoded['candidates'][0] ?? null;
    
    if (!$candidate) {
        echo json_encode(['reply' => "I apologize, I'm having trouble thinking right now."]);
        exit;
    }

    $parts = $candidate['content']['parts'] ?? [];
    $reply = "";
    $toolCalls = [];

    foreach ($parts as $part) {
        if (isset($part['text'])) {
            $reply .= $part['text'];
        }
        if (isset($part['functionCall'])) {
            $toolCalls[] = [
                'name' => $part['functionCall']['name'],
                'args' => $part['functionCall']['args']
            ];
        }
    }

    // ... (previous logic)

    echo json_encode([
        'reply' => $reply,
        'tool_calls' => $toolCalls
    ]);

} else {
    $errorDetails = json_decode($response, true) ?: $response;
    
    // LOGGING FOR DEBUGGING
    $logMsg = date('[Y-m-d H:i:s] ') . "API Error: HTTP $httpCode - " . json_encode($errorDetails) . " - Curl Error: $curlError" . PHP_EOL;
    file_put_contents('debug_chat.log', $logMsg, FILE_APPEND);

    echo json_encode([
        'error' => 'Mahmoud is currently busy in the kitchen (API Error).', 
        'http_code' => $httpCode,
        'curl_error' => $curlError,
        'details' => $errorDetails
    ]);
}
?>
