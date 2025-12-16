<?php
namespace App\Handlers;

/**
 * ValidationHandler
 * 
 * Chain of Responsibility Pattern Implementation
 * Validates message content and format
 * Part of the Chat System (UML Design)
 */
class ValidationHandler extends MessageHandler {
    public function handle(array $message): array {
        // Validate required fields
        if (empty($message['room_id'])) {
            return ['error' => 'Room ID is required'];
        }
        
        if (empty($message['sender_id'])) {
            return ['error' => 'Sender ID is required'];
        }
        
        if (empty($message['content']) && empty($message['file_path'])) {
            return ['error' => 'Message content or file is required'];
        }
        
        // Validate content length
        if (isset($message['content']) && strlen($message['content']) > 5000) {
            return ['error' => 'Message content is too long (max 5000 characters)'];
        }
        
        // Sanitize content
        if (isset($message['content'])) {
            $message['content'] = trim($message['content']);
            $message['content'] = htmlspecialchars($message['content'], ENT_QUOTES, 'UTF-8');
        }
        
        // Pass to next handler if validation succeeds
        if ($this->next) {
            return $this->next->handle($message);
        }
        
        return $message;
    }
}
