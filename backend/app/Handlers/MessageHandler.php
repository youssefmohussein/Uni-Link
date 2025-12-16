<?php
namespace App\Handlers;

/**
 * MessageHandler Abstract Class
 * 
 * Chain of Responsibility Pattern Implementation
 * Base class for all message handlers
 * Part of the Chat System (UML Design)
 */
abstract class MessageHandler {
    protected ?MessageHandler $next = null;
    
    /**
     * Set the next handler in the chain
     */
    public function setNext(MessageHandler $handler): MessageHandler {
        $this->next = $handler;
        return $handler;
    }
    
    /**
     * Handle the message
     * Each handler decides whether to process and/or pass to next
     * 
     * @param array $message Message data
     * @return array Processed message data or error
     */
    abstract public function handle(array $message): array;
}
