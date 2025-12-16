<?php
namespace App\Observers;

/**
 * NotificationObserver Interface
 * 
 * Observer Pattern Implementation
 * All notification observers must implement this interface
 * Part of the Notification System (UML Design)
 */
interface NotificationObserver {
    /**
     * Update method called when an event occurs
     * 
     * @param string $eventType Type of event (e.g., 'POST_LIKED', 'PROJECT_REVIEWED')
     * @param array $payload Event data
     * @return void
     */
    public function update(string $eventType, array $payload): void;
}
