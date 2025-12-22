<?php
namespace App\Services;

use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;

/**
 * AiService
 * 
 * Handles automated responses from the @unilink bot
 */
class AiService extends BaseService
{
    private ChatRepository $chatRepo;
    private UserRepository $userRepo;
    private const BOT_USERNAME = 'unilink';

    public function __construct(ChatRepository $chatRepo, UserRepository $userRepo)
    {
        $this->chatRepo = $chatRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Process a new message and generate a reply if needed
     * 
     * @param array $message The message that was just sent
     */
    public function processMessage(array $message): void
    {
        // Don't reply to self
        if ($this->isBot($message['sender_id'])) {
            return;
        }

        // Check if bot is mentioned
        if ($this->isMentioned($message['content'])) {
            $this->generateReply($message);
        }
    }

    private function isBot(int $userId): bool
    {
        $botUser = $this->userRepo->findByUsername(self::BOT_USERNAME);
        return $botUser && $botUser['user_id'] == $userId;
    }

    private function isMentioned(?string $content): bool
    {
        if (!$content) return false;
        return stripos($content, '@' . self::BOT_USERNAME) !== false;
    }

    private function generateReply(array $triggerMessage): void
    {
        $botUser = $this->userRepo->findByUsername(self::BOT_USERNAME);
        if (!$botUser) {
            error_log("AiService: Bot user not found");
            return;
        }

        $replyContent = $this->fetchAiResponse($triggerMessage['content']);

        $replyData = [
            'room_id' => $triggerMessage['room_id'],
            'sender_id' => $botUser['user_id'],
            'content' => $replyContent,
            'message_type' => 'TEXT',
            'file_path' => null
        ];

        // Create the message directly via repo
        $this->chatRepo->createMessage($replyData);
    }

    private function fetchAiResponse(string $userContent): string
    {
        // Configuration - Defaults to Groq if not set in .env
        // Updated to use supported model as of late 2024/2025
        $apiKey = $_ENV['AI_API_KEY'] ?? 'your_api_key_here';
        $apiUrl = $_ENV['AI_API_URL'] ?? 'https://api.groq.com/openai/v1/chat/completions';
        $model = $_ENV['AI_MODEL'] ?? 'llama-3.3-70b-versatile';

        if ($apiKey === 'your_api_key_here' || empty($apiKey)) {
            return $this->getMockResponse($userContent) . "\n\n(System: Real API Key not configured)";
        }

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are Uni-Link AI, a helpful coding assistant for the Uni-Link platform.\n\n" .
                               "CONTEXT: You have access to the following Uni-Link API Endpoints:\n" . 
                               $this->getApiDocs() . 
                               "\n\nWhen answering questions about the project, use this API documentation."
                ],
                [
                    'role' => 'user',
                    'content' => $userContent
                ]
            ],
            'temperature' => 0.7
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        // Disable SSL verify for local dev environments if needed, but risky for prod
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log("AiService API Error: " . $error);
            return "Sorry, I'm having trouble connecting to my brain right now. (Network Error)";
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("AiService API Error Status: " . $httpCode . " Response: " . $response);
            // Return detailed error for debugging
            $errorDetails = json_decode($response, true);
            $errorMessage = $errorDetails['error']['message'] ?? $response;
            return "API Error ($httpCode): " . $errorMessage;
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? "I'm not sure how to respond to that.";
    }

    private function getMockResponse(string $userContent): string
    {
        // Simple rule-based responses (Fallback)
        $lowerContent = strtolower($userContent);

        if (strpos($lowerContent, 'hello') !== false || strpos($lowerContent, 'hi') !== false) {
            return "Hello! I am Uni-Link AI. How can I help you today?";
        }

        if (strpos($lowerContent, 'help') !== false) {
            return "I can help you with navigating the platform, finding resources, or connecting with others. Just ask!";
        }
        
        // Default response
        return "That's an interesting question! As an AI, I'm still learning. Please configure my API key to unlock my full potential!";
    }

    /**
     * Get API Documentation for the AI Context
     */
    private function getApiDocs(): string
    {
        return "
AUTH:
POST /api/auth/login, POST /api/auth/logout, GET /api/auth/me

USERS:
GET /api/user, POST /api/user, GET /api/user/profile

PROJECTS:
GET /api/projects (Params: faculty_id, status)
POST /api/projects (Body: title, description, file, etc)
POST /api/projects/upload (Multipart)
POST /api/projects/grade (Body: project_id, grade, status)
GET /api/grading/projects (Params: status=graded|not_graded)

ROOMS & CHAT:
GET /api/project-rooms (List all)
POST /api/project-rooms (Create)
GET /api/chat/messages?room_id={id}
POST /api/chat/send (Body: room_id, content, message_type)
POST /api/chat/upload (Multipart)

POSTS:
GET /api/posts, POST /api/posts
POST /api/post-interactions (Like/Love)
GET /api/comments?post_id={id}, POST /api/comments

NOTIFICATIONS:
GET /api/notifications
";
    }
}
