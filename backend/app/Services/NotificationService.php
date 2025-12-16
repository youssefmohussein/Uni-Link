<?php
namespace App\Services;

use App\Observers\NotificationObserver;

/**
 * NotificationService
 * 
 * Observer Pattern Implementation
 * Manages notification observers and broadcasts events
 * Part of the Notification System (UML Design)
 */
class NotificationService {
    private array $observers = [];
    
    /**
     * Subscribe an observer to notifications
     */
    public function subscribe(NotificationObserver $observer): void {
        $this->observers[] = $observer;
    }
    
    /**
     * Unsubscribe an observer
     */
    public function unsubscribe(NotificationObserver $observer): void {
        $this->observers = array_filter($this->observers, function($obs) use ($observer) {
            return $obs !== $observer;
        });
    }
    
    /**
     * Notify all observers of an event
     * 
     * @param string $eventType Type of event (e.g., 'POST_LIKED', 'PROJECT_APPROVED')
     * @param array $payload Event data
     */
    public function notifyAll(string $eventType, array $payload): void {
        foreach ($this->observers as $observer) {
            try {
                $observer->update($eventType, $payload);
            } catch (\Exception $e) {
                error_log("NotificationService: Observer failed - " . $e->getMessage());
            }
        }
    }
    
    /**
     * Get count of registered observers
     */
    public function getObserverCount(): int {
        return count($this->observers);
    }
}
