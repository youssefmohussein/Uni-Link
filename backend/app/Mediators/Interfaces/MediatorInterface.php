<?php
namespace App\Mediators\Interfaces;

/**
 * Mediator Interface
 * 
 * Defines the contract for mediator pattern implementations
 * Mediators coordinate communication between components
 */
interface MediatorInterface {
    /**
     * Notify the mediator of an event
     * Components call this method instead of communicating directly
     * 
     * @param object $sender The component sending the notification
     * @param string $event Event name
     * @param array $data Event data
     */
    public function notify(object $sender, string $event, array $data = []): void;
}
